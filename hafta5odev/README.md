# Yavuzlar Modüler Web Shell Kullanım Kılavuzu

Yavuzlar Modüler Web Shell, çeşitli sistem komutlarını çalıştırabileceğiniz,dosya yönetimi yapabileceğiniz, sistemde dosya arayabileceğiniz ve önemli config dosyalarını arayabileceğiniz bir web shell'dir.

İhtiyaca göre şekillendirilebilecek page as a function bakış açısı ile yazılmıştır(benim uydurmam).

Aşağıda çeşitli alt menüler ve bilgiler var

## Ana Menü

Shell'i açtığınızda ilk bu sayfa gelecek bu sayfada üst menü ve açıklama yeri var

- **Ana Sayfa**: Web Shell'in giriş ekranı.
- **Terminal (POST Request)**: Komutları POST isteği ile çalıştıran terminal.
- **Terminal V2 (GET Request)**: Komutları GET isteği ile çalıştıran terminal.
- **Dosya Yöneticisi**: Sisteminizdeki dosyaları ve dizinleri görüntüleyebilir, dosya yükleme ve silme işlemleri yapabilirsiniz.
- **Dosya Arama**: Belirli bir dosyayı belirtilen dizinde arayabilirsiniz.
- **Config Dosyası Tespiti**: Sistemdeki belirli dosya tiplerini (config dosyaları, suid/sgid dosyaları vb.) tespit edebilirsiniz.
- **Sunucu Bilgileri**: Atılan sunucunun sistem bilgilerini görebilirsiniz.

## Dosya Yöneticisi Kullanımı

1. **Dosya Görüntüleme**: Belirtilen dizindeki dosya ve klasörleri görüntüleyebilir, üst klasöre geçiş yapabilirsiniz.
2. **Dosya İndirme**: Dosya üzerine tıklayarak sisteminizden dosya indirebilirsiniz.
3. **Dosya Silme**: Silmek istediğiniz dosya ya da klasöre tıklayıp, onayladıktan sonra silme işlemi yapabilirsiniz.
4. **Dosya Yükleme**: Dosya yüklemek için formu kullanarak bilgisayarınızdan dosya seçin ve yükleyin.

## Terminal Kullanımı

### Terminal (POST Request)

POST isteği kullanarak komutlarınızı çalıştırabileceğiniz bir terminaldir.

Belirli sitelerde çalışmayabilir, ancak diğer terminal çok daha fazla yerde çalışır.

- **Komut Girme**: Komut alanına istediğiniz terminal komutunu yazın ve **Execute** butonuna tıklayın.
- **Örnek Komutlar**: 
  - `ls` - Dosyaları ve klasörleri listeleme.
  - `pwd` - Şu anki çalışma dizinini gösterme.
  - `cat [dosya_adı]` - Dosya içeriğini görüntüleme.

### Terminal V2 (GET Request)

Terminal V2, GET isteği kullanarak komut çalıştırmanızı sağlar. 

Bu terminal bazı durumlarda POST ile çalışmayan ortamlarda alternatif olarak kullanılabilir.

## Dosya Arama

Dosya arama fonksiyonu ile belirtilen dizinde belirli bir dosya adını arayabilirsiniz.

1. **Arama Yapılacak Dizin**: Aramanın yapılacağı dizini belirtin.
2. **Aranacak Dosya Adı**: Aramak istediğiniz dosya adını girin ve **Ara** butonuna tıklayın.
3. **Sonuçlar**: Arama sonuçları listelenir ve ilgili dosyalara ulaşabilirsiniz.

## Sunucu Bilgileri

Sunucunuzla ilgili sistem bilgilerini görüntüleyebilirsiniz:

- Sistem bilgileri
- Sunucu yazılımı
- Sunucu IP adresi
- PHP sürümü ve yüklü PHP eklentileri

**Dikkat!** `Config Dosyası Tespiti` fonksiyonu sisteme ağır yük bindirebilir ve kaynak kullanımını artırabilir. Çünkü aynı anda bir sürü komut çalıştırıyoruz bu sayfayı açtığımızda.



---

Web Shell'in her fonksiyonunu menüden kolayca seçip kullanabilirsiniz.












