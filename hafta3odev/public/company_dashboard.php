<?php
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/includes/functions.php';

// Kullanıcının giriş yapmış ve firma rolüne sahip olduğundan emin olun
requireLogin();
requireRole('company');

// Kullanıcının firma bilgilerini al
$userId = $_SESSION['user_id'];
$companyInfo = getCompanyInfoByUserId($pdo, $userId);

// Firmanın restoranlarını al
$restaurants = getRestaurantsByCompanyId($pdo, $companyInfo['id']);

// Firmanın tüm siparişlerini al
$orders = getOrdersByCompanyId($pdo, $companyInfo['id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Firma Kontrol Paneli</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="bgspecial">
<div class="container">
    <h1>Hoş geldiniz, <?= htmlspecialchars($companyInfo['name']) ?></h1>
    
    <h2>Hızlı Erişim</h2>
    
    <a href="company_restaurants.php" class="btn">Restoranlarım</a>
    <a href="company_foods.php" class="btn">Yemekler</a>
    <a href="company_orders.php" class="btn">Siparişler</a>
    <a href="company_coupons.php" class="btn">Kuponlar</a>

    <p><a href="logout.php" class="btn">Çıkış Yap</a></p>
</div>
</div>
</body>
</html><?php include './footer.php'; ?>