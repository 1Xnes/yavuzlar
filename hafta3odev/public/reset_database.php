<?php
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/includes/functions.php';

requireLogin();
requireAdmin();

// Ek güvenlik kontrolü
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['confirm_reset'])) {
    // Onay sayfasını göster
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Veritabanı Sıfırlama Onayı</title>
        <link rel="stylesheet" href="/css/style.css">
    </head>
    <body>
    <div class="bgspecial">
    <div class="container">
        <h1>Veritabanı Sıfırlama Onayı</h1>
        <p>DİKKAT: Bu işlem tüm veritabanını sıfırlayacak ve tüm verileri silecektir.</p>
        <form method="POST">
            <input type="hidden" name="confirm_reset" value="1">
            <input type="submit" value="Veritabanını Sıfırla" class="btn danger">
        </form>
        <p><a href="admin.php" class="btn">İptal</a></p>
    </div>
    </div>
    </body>
    </html><?php include './footer.php'; ?>
    <?php
    exit();
}

// Veritabanını sıfırla
try {
    $pdo->beginTransaction();

    // Tüm tabloları sil
    $tables = ['order_items', '`order`', 'basket', 'food', 'comments', 'coupon', 'restaurant', 'users', 'company'];
    foreach ($tables as $table) {
        $pdo->exec("DROP TABLE IF EXISTS $table");
    }

    // init.sql dosyasını oku ve çalıştır
    $sql = file_get_contents(__DIR__ . '/yedekcinit.sql');
    $pdo->exec($sql);

    $pdo->commit();

    // Admin kullanıcısını yeniden oluştur
    createDefaultAdminUser($pdo);

    $_SESSION['success_message'] = "Veritabanı başarıyla sıfırlandı ve yeniden oluşturuldu.";
    header("Location: admin.php");
    exit();
} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['error_message'] = "Veritabanı sıfırlanırken bir hata oluştu: " . $e->getMessage();
    header("Location: admin.php");
    exit();
}