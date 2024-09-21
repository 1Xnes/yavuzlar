<?php
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/includes/functions.php';

requireLogin();
requireRole('company');

$userId = $_SESSION['user_id'];
$companyInfo = getCompanyInfoByUserId($pdo, $userId);

$restaurantId = $_GET['id'] ?? 0;
$restaurant = getRestaurantById($pdo, $restaurantId);

if (!$restaurant || $restaurant['company_id'] !== $companyInfo['id']) {
    $_SESSION['error_message'] = "Geçersiz restoran.";
    header("Location: company_restaurants.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = deleteRestaurant($pdo, $restaurantId);
    if ($result) {
        $_SESSION['success_message'] = "Restoran başarıyla silindi.";
    } else {
        $_SESSION['error_message'] = "Restoran silinirken bir hata oluştu.";
    }
    header("Location: company_restaurants.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restoran Sil</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="bgspecial">
<div class="container">
    <h1>Restoran Sil</h1>
    
    <p>Bu restoranı silmek istediğinizden emin misiniz?</p>
    <p><strong><?= htmlspecialchars($restaurant['name']) ?></strong></p>
    
    <form method="POST">
        <button type="submit" class="btn">Evet, Sil</button>
        <a href="company_restaurants.php" class="btn">İptal</a>
    </form>
</div>
</div>
</body>
</html><?php include './footer.php'; ?>