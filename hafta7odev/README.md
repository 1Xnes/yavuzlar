# CLI Giriş Sistemi

Bu proje, Go programlama dili kullanılarak geliştirdiğim basit bir CLI (Command Line Interface) tabanlı kullanıcı giriş ve yönetim sistemidir.
Rahatça güncellenebilmesi için fonksiyon tabanlı bir yapıya sahiptir.

## 🚀 Özellikler

- Admin ve Müşteri kullanıcı tipleri
- Kullanıcı giriş/çıkış takibi
- Otomatik log sistemi
- Dosya tabanlı kullanıcı veritabanı

## 📋 Gereksinimler

- Go 1.15 veya üzeri

## 💻 Kurulum

1. Projeyi bilgisayarınıza klonlayın
2. Terminal veya komut istemcisinde proje klasörüne gidin
3. Programı çalıştırın:
```bash
go run main.go
```

## 📌 Kullanım

### Giriş Yapma
- Admin girişi için: `0`
- Müşteri girişi için: `1`
- Ardından kullanıcı adı ve şifre girilmeli

### Varsayılan Kullanıcılar
```
Admin: 
- Kullanıcı adı: admin
- Şifre: admin

Müşteri:
- Kullanıcı adı: usr
- Şifre: usr
```

### Admin Yetkileri
1. Müşteri Ekleme
2. Müşteri Silme
3. Log Listeleme
4. Çıkış Yapma

### Müşteri Yetkileri
1. Profil Görüntüleme
2. Şifre Değiştirme
3. Çıkış Yapma

## 📁 Sistem Dosyaları

### kullanicilar.txt
Kullanıcı bilgilerinin saklandığı dosya. Format:
```
kullaniciAdi,sifre,rol
```
Örnek içerik:
```
admin,admin,0
usr,usr,1
usr2,usr2,1
```

### loglar.txt
Sistem loglarının tutulduğu dosya. Format:
```
kullaniciAdi,rol,tarih,islem
```
Örnek log kaydı:
```
admin,0,22.10.2024 01:39:23,Giriş Yapılamadı
usr,1,22.10.2024 01:45:01,Giriş Yapıldı
admin,0,22.10.2024 01:47:53,Çıkış Yapıldı
```

## 🔒 Güvenlik

- Şifreler düz metin olarak saklanmakta.
- Her giriş/çıkış işlemi loglanır
- Yanlış giriş denemeleri kaydedilir

## 📝 Not

Eğitim amaçlı ve bin tane exploit çıkar burdan güvenli değil ne yapalım hocam :')