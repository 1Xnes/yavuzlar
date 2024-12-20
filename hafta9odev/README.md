# SSH Brute Force Aracı

## Kurulum

1. **Go Modülünü Başlatın:**
   
   ```bash
   go mod init sshCracker
   ```

2. **SSH Kütüphanesini İndirin:**
   
   ```bash
   go get golang.org/x/crypto/ssh
   ```

## Kullanım

Araç, komut satırından aşağıdaki argümanları alarak çalışır:

* `-p`  **Tek Şifre:** Denenecek tek bir şifre belirtir. (Örn: `-p password123`)
* `-P`  **Şifre Listesi:** Şifrelerin bulunduğu bir dosya belirtir. (Örn: `-P passwords.txt`)
* `-u`  **Tek Kullanıcı Adı:** Denenecek tek bir kullanıcı adı belirtir. (Örn: `-u testuser`)
* `-U`  **Kullanıcı Adı Listesi:** Kullanıcı adlarının bulunduğu bir dosya belirtir. (Örn: `-U users.txt`)
* `-h`  **Hedef Host:** Hedef sunucunun IP adresini veya hostname'ini belirtir. (Örn: `-h 192.168.1.100` veya `-h example.com`)

**Not:** `-p` veya `-P` ve `-u` veya `-U` argümanlarından birer tanesi zorunludur.  Ayrıca `-h` argümanı da zorunludur.

### Örnek Çalıştırmalar

1. **Tek Kullanıcı ve Şifre ile Deneme:**
   
   ```bash
   go run main.go -u testuser -p password123 -h 192.168.1.100
   ```

2. **Kullanıcı Adı Listesi ve Şifre Listesi ile Deneme:**
   
   ```bash
   go run main.go -U users.txt -P passwords.txt -h 192.168.1.100
   ```

3. **Tek Kullanıcı Adı ve Şifre Listesi ile Deneme:**
   
   ```bash
   go run main.go -u testuser -P passwords.txt -h 192.168.1.100
   ```

4. **Kullanıcı Adı Listesi ve Tek Şifre ile Deneme:**
   
   ```bash
   go run main.go -U users.txt -p password123 -h 192.168.1.100
   ```
5. **Hackviser Secure Command Warmup Lab'i için örnek deneme:**
   
   ```bash
   go run main.go -u hackviser -p hackviser -h 172.20.3.189
   ```


### Dosya Formatları

- **Kullanıcı Adı ve Şifre Listesi Dosyaları:** Dosyadaki her satır, bir kullanıcı adı veya şifre içermelidir.
  
  Örneğin `users.txt` dosyası:
  
  ```
  testuser1
  testuser2
  admin
  ```
  
  Örneğin `passwords.txt` dosyası:
  
  ```
  password123
  secret456
  qwerty
  ```
  
  ## Çalışma Mantığı

Araç, worker pool mantığı ile çalışır. Yani, şifre ve kullanıcı adı denemeleri paralel olarak gerçekleştirilir. Bu, işlem hızını artırır.

## Dikkat Edilmesi Gerekenler

* **Yasal Uyarı:** Bu araç, yalnızca güvenlik testleri ve eğitim amaçlı kullanılmalıdır. Başka sistemlere izinsiz erişim sağlamak yasa dışıdır.
* **Hedef Sistem:**  Aracı kullanacağınız sunucunun izni olduğundan emin olun.