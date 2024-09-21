<?php
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/includes/functions.php';

requireLogin();
requireRole('customer');

$userId = $_SESSION['user_id'];
$restaurantId = $_GET['id'] ?? 0;

// Restoran bilgilerini al
$restaurant = getRestaurantById($pdo, $restaurantId);

if (!$restaurant) {
    $_SESSION['error_message'] = "Restoran bulunamadı.";
    header("Location: customer_restaurants.php");
    exit();
}

// Restoran menüsünü al
$menu = getRestaurantMenu($pdo, $restaurantId);

// Kuponları al
$coupons = getRestaurantCoupons($pdo, $restaurantId);

// Ortalama puanı al
$averageScore = getAverageScore($pdo, $restaurantId);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($restaurant['name']) ?> - Menü</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="bgspecial">
<div class="container">
<?php if (isset($_SESSION['success_message'])): ?>
        <p class="success"><?= htmlspecialchars($_SESSION['success_message']) ?></p>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <p class="error"><?= htmlspecialchars($_SESSION['error_message']) ?></p>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>
    <h1><?= htmlspecialchars($restaurant['name']) ?> - Menü</h1>
    
    <p>Ortalama Puan: <?= number_format($averageScore, 1) ?>/10</p>

    <?php if (!empty($coupons)): ?>
        <div class="coupons">
            <h2>Mevcut Kuponlar</h2>
            <ul>
                <?php foreach ($coupons as $coupon): ?>
                    <li><?= htmlspecialchars($coupon['name']) ?> - %<?= $coupon['discount'] ?> indirim</li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (empty($menu)): ?>
        <p>Bu restoran için menü bulunamadı.</p>
    <?php else: ?>
        <div class="menu-list">
            <?php foreach ($menu as $item): ?>
                <div class="menu-item">
                    <h3><?= htmlspecialchars($item['name']) ?></h3>
                    <p><?= htmlspecialchars($item['description']) ?></p>
                    <?php if ($item['image_path']): ?>
                        <img src="<?= htmlspecialchars($item['image_path']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" style="max-width: 200px;">
                    <?php endif; ?>
                    <p>Fiyat: <?= number_format($item['price'], 2) ?> TL</p>
                    <?php if ($item['discount'] > 0): ?>
                        <p>İndirim: %<?= $item['discount'] ?></p>
                        <p>İndirimli Fiyat: <?= number_format($item['price'] * (1 - $item['discount'] / 100), 2) ?> TL</p>
                    <?php endif; ?>
                    <form action="add_to_cart.php" method="POST">
                        <input type="hidden" name="food_id" value="<?= $item['id'] ?>">
                        <input type="number" name="quantity" value="1" min="1" max="10">
                        <button type="submit" class="btn">Sepete Ekle</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <p><a href="customer_restaurants.php" class="btn">Restoran Listesine Dön</a></p>
    <p><a href="customer_dashboard.php" class="btn">Ana Sayfaya Dön</a></p>
    <p><a href="customer_add_comment.php?restaurant_id=<?= $restaurantId ?>" class="btn">Yorum Ekle</a></p>
</div>
</div>
</body>
</html><?php include './footer.php'; ?>