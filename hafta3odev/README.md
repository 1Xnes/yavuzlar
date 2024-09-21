# YAVUZLAR YEMEK YÖNETİM SİSTEMİ PROJESİ

### Proje kullanımı

db dizininde terminal veya powershell açtıktan sonra başlatmak için

```
docker-compose up
```

durdurmak için ise

```
docker-compose down
```

### Sorunlu olan Yerler

- Müşteri Profil Sayfasında Pseudo profil fotoğrafı koyma kısmı

- Sepetteki güncellenebilen not kısmının backende gitmemesi

- Her compose uplandığında başka bir volume kullanmaya başlaması

- Bazı yerlerde sapıtan buton lokasyonları stilleri (admin panelindeki firma listesi)

- Kör olabileceğiniz kadar kötü frontend dizaynı

- Dizinlere ayrılmamış ana public dizini

- Footer'a css linklemeyi beceremediğim için footerin cssi footer.php'nin içine enjekte etmem gerekti yavaşlatıyor muhtemelen bu biraz (ama hissedilmiyor)

- Bloat bloat ve bloat

- Fazla kaotik

### İyi olan yerler

- Çalışıyor

- Animasyonlu footer yavuzlar logosu

- Argon2id düzgün çalışıyor

- Dockerde olduğu için benim bilgisayarımda çalışıyorsa diğer bilgisayarlarda da çalışabilir

- Neredeyse 1k satır functions.php

### Çalışma Şekli

index.phpye ilk girildiğinde veritabanına default admin admin şeklinde bir kullanıcı ekliyor, sonrasında eklenecek her yer kullanıcıya kalmış, her sayfanın altında footeri içerecek şekilde kod var

Veritabanı yapısını bazı veriler foreign key olacak şekilde ayarladım ve gereken bazı yerlerde join de kullanılıyor( belirli sql sorgularını içeren fonksiyonlarım)