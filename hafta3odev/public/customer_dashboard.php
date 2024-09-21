<?php
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/includes/functions.php';

requireLogin();
requireRole('customer');

$userId = $_SESSION['user_id'];
$userInfo = getUserById($pdo, $userId);

// Son siparişleri al
$recentOrders = getRecentOrdersByUserId($pdo, $userId, 5);

// Popüler restoranları al
$popularRestaurants = getPopularRestaurants($pdo, 5);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Müşteri Paneli</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="bgspecial">
<div class="container">
    <h1>Hoş Geldiniz, <?= htmlspecialchars($userInfo['name']) ?></h1>

    <h2>Son Siparişleriniz</h2>
    <?php if (empty($recentOrders)): ?>
        <p>Henüz sipariş vermediniz.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($recentOrders as $order): ?>
                <li>
                    Sipariş #<?= $order['id'] ?> - 
                    <?= htmlspecialchars($order['restaurant_name']) ?> - 
                    <?= number_format($order['total_price'], 2) ?> TL - 
                    <?= htmlspecialchars($order['order_status']) ?>
                </li>
            <?php endforeach; ?>
        </ul>

        <p><a href="customer_order_history.php" class="btn">Sipariş Geçmişim</a></p>
    <?php endif; ?>

    <h2>Popüler Restoranlar</h2>
    <ul>
        <?php foreach ($popularRestaurants as $restaurant): ?>
            <li>
                <a href="customer_restaurant_menu.php?id=<?= $restaurant['id'] ?>">
                    <?= htmlspecialchars($restaurant['name']) ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>

    <p><a href="customer_restaurants.php" class="btn">Tüm Restoranlar</a></p>
    <p><a href="customer_food_search.php" class="btn">Yemek ara ve Söyle</a></p>
    <p><a href="customer_cart.php" class="btn">Sepetim</a></p>
    <p><a href="customer_profile.php" class="btn">Profilim</a></p>
    <p><a href="logout.php" class="btn">Çıkış Yap</a></p>
</div>
</div>
</body>
</html><?php include './footer.php'; ?>