<?php
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/includes/functions.php';

requireLogin();
requireAdmin();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="/css/style.css">
    
</head>
<body>
<div class="bgspecial">
<div class="container">
    <h1>Admin Panel</h1>
    <p><a href="user_list.php" class="btn">Tüm Kullanıcı Listesi</a></p>
    <p><a href="user_add.php" class="btn">Kullanıcı Ekle</a></p>
    <h2>Müşteri Yönetimi</h2>
    <p><a href="customer_list.php" class="btn">Müşteri Listesi</a></p>
    <p><a href="customer_search.php" class="btn">Müşteri Arama</a></p>
    <p><a href="comment_list.php" class="btn">Müşteri Yorumları</a></p>
    
    <h2>Firma Yönetimi</h2>
    <p><a href="company_list.php" class="btn">Firma Listesi</a></p>
    <p><a href="company_search.php" class="btn">Firma Arama</a></p>
    
    <h2>Kupon Yönetimi</h2>
    <p><a href="coupon_list.php" class="btn">Kupon Listesi</a></p>
    <p><a href="coupon_add.php" class="btn">Kupon Ekle</a></p>
    <hr>
    <p><a href="index.php" class="btn">Ana Sayfa</a></p>
    <p><a href="logout.php" class="btn">Çıkış Yap</a></p>
    
</div>
</div>
</body>
</html>


<?php include './footer.php'; ?>