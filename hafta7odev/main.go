package main

import (
	"bufio"
	"fmt"
	"os"
	"strconv"
	"strings"
	"time"
)

// User= müşteri & Admin= admin
// Kullanıcı adları ve şifreleri için global liste

var users = []User{}

type User struct {
	Username string
	Password string
	//0 admin, 1 user
	Role int
}

var currentUser string
var roleOfUser int = 0
var loggedIn bool = false

func main() {
	loadUsersFromFile()
	// default credentials
	if len(users) == 0 {
		users = append(users, User{"admin", "admin", 0})
		saveUsersToFile()
	}
	mainMenu()
}

func mainMenu() {
	fmt.Println("Ana Menüye Hoşgeldiniz!")
	fmt.Println("Şuanki saat: ", time.Now().Format("02.01.2006 15:04:05"))
	if loggedIn {
		fmt.Println("Hoşgeldiniz,", currentUser, ".\n Giriş yapıldı.")
		if roleOfUser == 0 {
			fmt.Println("Admin paneline yönlendiriliyorsunuz...")
			adminMenu()
		} else {
			fmt.Println("Müşteri paneline yönlendiriliyorsunuz...")
			userMenu()
		}

	} else {
		fmt.Println("Lütfen giriş yapınız.")
		login()
	}
}

func login() {
	fmt.Println()
	//debug code:
	fmt.Println("Şuanki kullanıcı listesi: ", users)
	//debug code end
	fmt.Print("Admin olarak giriş yapmak için 0, Müşteri olarak giriş yapmak için 1 giriniz:")
	var choice int
	fmt.Scanln(&choice)
	if choice != 0 && choice != 1 {
		fmt.Println("Lütfen 0 veya 1 giriniz.")
		login()
	}
	fmt.Print("Kullanıcı Adı:")
	var username string
	fmt.Scanln(&username)
	fmt.Print("Şifre:")
	var password string
	fmt.Scanln(&password)
	fmt.Println("Giriş Yapılıyor...")
	if authenticate(username, password, choice) == 1 {
		if choice == 0 {
			fmt.Println("Admin girişi başarılı.")
			addLoginLog(username, 0)
		} else {
			fmt.Println("User girişi başarılı.")
			addLoginLog(username, 1)
		}
		loggedIn = true
		currentUser = username
		roleOfUser = choice
		mainMenu()
	} else if authenticate(username, password, choice) == 2 {
		fmt.Println("Giriş Yapılamadı. Yanlış kullanıcı rolü. Tekrar deneyiniz.")
		addFailedLoginLog(username, choice)
		login()
	} else {
		fmt.Println("Giriş Yapılamadı. Kullanıcı adı veya şifre yanlış.")
		addFailedLoginLog(username, choice)
		login()
	}
}

func authenticate(username, password string, choice int) int {
	for i := 0; i < len(users); i++ {
		if users[i].Username == username && users[i].Password == password && users[i].Role == choice {
			return 1
		} else if users[i].Username == username && users[i].Password == password && users[i].Role != choice {
			return 2
		}
	}
	return 0
}

/*
Müşteri paneli özellikleri
a. Profil görüntüleme
b. Şifre değiştirebilme
*/

func userMenu() {
	fmt.Println("Müşteri Paneline Hoşgeldiniz!")
	fmt.Println("1. Profil Görüntüleme")
	fmt.Println("2. Şifre Değiştirme")
	fmt.Println("3. Çıkış Yap")
	fmt.Print("Seçiminizi yapınız:")
	var choice int
	fmt.Scanln(&choice)
	switch choice {
	case 1:
		viewProfile()
	case 2:
		changePassword()
	case 3:
		fmt.Println("Çıkış Yapılıyor...")
		logout()
	default:
		fmt.Println("Lütfen 1-3 arasında bir seçim yapınız.")
		userMenu()
	}
}

func viewProfile() {
	fmt.Println("Profil Bilgileriniz:")
	fmt.Println("Kullanıcı Adı:", currentUser)
	fmt.Println("Rol:", roleOfUser)
	fmt.Println("Müşteri Paneline Dönmek için bir tuşa basın.")
	fmt.Scanln()
	userMenu()
}

func changePassword() {
	fmt.Print("Yeni Şifrenizi giriniz:")
	var newPassword string
	fmt.Scanln(&newPassword)
	fmt.Print("Yeni Şifrenizi onaylayınız:")
	var confirmPassword string
	fmt.Scanln(&confirmPassword)
	if newPassword != confirmPassword {
		fmt.Println("Şifreler eşleşmedi. Tekrar deneyiniz.")
		changePassword()
	} else {
		// şifre değiştiriliyor
		for i := 0; i < len(users); i++ {
			if users[i].Username == currentUser {
				users[i].Password = newPassword
				break
			}
		}
		fmt.Println("Şifreniz başarıyla değiştirildi.")
		saveUsersToFile()
	}
	fmt.Println("Müşteri Paneline Dönmek için bir tuşa basın.")
	fmt.Scanln()
	userMenu()
}

/*
Admin Paneli İçin Menü
a. Müşteri ekleme
b. Müşteri silme
c. Log listeleme
*/
func adminMenu() {
	fmt.Println("Admin Paneline Hoşgeldiniz!")
	fmt.Println("1. Müşteri Ekleme")
	fmt.Println("2. Müşteri Silme")
	fmt.Println("3. Log Listeleme")
	fmt.Println("4. Çıkış Yap")
	fmt.Print("Seçiminizi yapınız:")
	var choice int
	fmt.Scanln(&choice)
	switch choice {
	case 1:
		addUser()
	case 2:
		deleteUser()
	case 3:
		listLogs()
	case 4:
		fmt.Println("Çıkış Yapılıyor...")
		logout()
	default:
		fmt.Println("Lütfen 1-4 arasında bir seçim yapınız.")
		adminMenu()
	}
}

func addUser() {

	fmt.Print("Kullanıcı Adı:")
	var username string
	fmt.Scanln(&username)
	fmt.Print("Şifre:")
	var password string
	fmt.Scanln(&password)
	fmt.Print("Rolü 0 Admin, 1 User olarak seçiniz:")
	var role int
	fmt.Scanln(&role)
	if role != 0 && role != 1 {
		fmt.Println("Lütfen 0 veya 1 giriniz.")
		addUser()
	}
	users = append(users, User{username, password, role})
	saveUsersToFile()
	fmt.Println("Müşteri Ekleme Başarılı.")
	fmt.Println("Admin Menüsüne Dönmek için bir tuşa basın.")
	fmt.Scanln()
	adminMenu()
}

func deleteUser() {
	fmt.Print("Silmek istediğiniz kullanıcı adını giriniz:")
	var username string
	fmt.Scanln(&username)
	for i := 0; i < len(users); i++ {
		if users[i].Username == username {
			// diziyi dilimleyerek kullanıcımızı siliyoruz
			users = append(users[:i], users[i+1:]...)
			fmt.Println("Müşteri Silme Başarılı.")
			saveUsersToFile()
			break
		}
	}
	fmt.Println("Admin Menüsüne Dönmek için bir tuşa basın.")
	fmt.Scanln()
	adminMenu()
}

func listLogs() {
	fmt.Println("Loglar Listeleniyor...")
	readLogsFromFile()
	fmt.Println("Loglar Listelendi.")
	fmt.Println("Admin Menüsüne Dönmek için bir tuşa basın.")
	fmt.Scanln()
	adminMenu()
}

func readLogsFromFile() {
	// loglar.txt dosyasından logları okuyor
	file, err := os.Open("loglar.txt")
	if err != nil {
		fmt.Println(err)
	}
	defer file.Close()
	scanner := bufio.NewScanner(file)
	for scanner.Scan() {
		fmt.Println(scanner.Text())
	}
	if err := scanner.Err(); err != nil {
		fmt.Println(err)
	}
}

func addLoginLog(username string, role int) {
	log := username + "," + fmt.Sprint(role) + "," + time.Now().Format("02.01.2006 15:04:05") + "," + "Giriş Yapıldı"
	pushLogToTxt(log)
}

func addFailedLoginLog(username string, role int) {
	log := username + "," + fmt.Sprint(role) + "," + time.Now().Format("02.01.2006 15:04:05") + "," + "Giriş Yapılamadı"
	pushLogToTxt(log)
}

func addLogoutLog(username string, role int) {
	log := username + "," + fmt.Sprint(role) + "," + time.Now().Format("02.01.2006 15:04:05") + "," + "Çıkış Yapıldı"
	pushLogToTxt(log)
}

func pushLogToTxt(log string) {
	// loglar.txt dosyasına log ekliyoruz
	file, err := os.OpenFile("loglar.txt", os.O_APPEND|os.O_CREATE|os.O_WRONLY, 0644)
	if err != nil {
		fmt.Println(err)
	}
	defer file.Close()
	_, err = file.WriteString(log + "\n")
	if err != nil {
		fmt.Println(err)
	}
}

func logout() {
	addLogoutLog(currentUser, roleOfUser)
	loggedIn = false
	currentUser = ""
	roleOfUser = 0
	mainMenu()
}

func loadUsersFromFile() {
	// kullanicilar.txt dosyasından kullanıcıları okuyor
	file, err := os.Open("kullanicilar.txt")
	if err != nil {
		fmt.Println(err)
	}
	defer file.Close()
	scanner := bufio.NewScanner(file)

	for scanner.Scan() {
		line := scanner.Text()
		parts := strings.Split(line, ",")
		if len(parts) == 3 {
			username := parts[0]
			password := parts[1]
			role, err := strconv.Atoi(parts[2])
			if err != nil {
				fmt.Println("Rol değeri dönüştürülemedi:", err)
				continue
			}
			users = append(users, User{username, password, role})
		} else {
			fmt.Println("Geçersiz kullanıcı satırı:", line)
		}
	}
	if err := scanner.Err(); err != nil {
		fmt.Println(err)
	}
}

func saveUsersToFile() {
	//  kullanicilar.txt dosyasına kullanıcılar kaydediliyor
	file, err := os.OpenFile("kullanicilar.txt", os.O_WRONLY|os.O_TRUNC|os.O_CREATE, 0644)
	if err != nil {
		fmt.Println(err)
	}
	defer file.Close()
	for i := 0; i < len(users); i++ {
		_, err = file.WriteString(users[i].Username + "," + users[i].Password + "," + fmt.Sprint(users[i].Role) + "\n")
		if err != nil {
			fmt.Println(err)
		}
	}
}
