<?php
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/includes/functions.php';

requireLogin();
requireRole('customer');

$search = $_GET['search'] ?? '';
$minPrice = $_GET['min_price'] ?? '';
$maxPrice = $_GET['max_price'] ?? '';

$foods = searchFoods($pdo, $search, $minPrice, $maxPrice);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yemek Arama</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="bgspecial">
<div class="container">
    <h1>Yemek Arama</h1>
    <?php if (isset($_SESSION['success_message'])): ?>
        <p class="success"><?= htmlspecialchars($_SESSION['success_message']) ?></p>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <p class="error"><?= htmlspecialchars($_SESSION['error_message']) ?></p>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>
    <form method="GET" action="">
        <input type="text" name="search" placeholder="Yemek Adı" value="<?= htmlspecialchars($search) ?>">
        <input type="number" name="min_price" placeholder="Min Fiyat" value="<?= htmlspecialchars($minPrice) ?>">
        <input type="number" name="max_price" placeholder="Max Fiyat" value="<?= htmlspecialchars($maxPrice) ?>">
        <button type="submit" class="btn">Ara</button>
    </form>

    <?php if (empty($foods)): ?>
        <p>Yemek bulunamadı.</p>
    <?php else: ?>
        <div class="food-list">
            <?php foreach ($foods as $food): ?>
                <div class="food-item">
                    <h2><?= htmlspecialchars($food['name']) ?></h2>
                    <p>Restoran: <?= htmlspecialchars($food['restaurant_name']) ?></p>
                    <p><?= htmlspecialchars($food['description']) ?></p>
                    <?php if ($food['image_path']): ?>
                        <img src="<?= htmlspecialchars($food['image_path']) ?>" alt="<?= htmlspecialchars($food['name']) ?>" style="max-width: 200px;">
                    <?php endif; ?>
                    <p>Fiyat: <?= number_format($food['price'], 2) ?> TL</p>
                    <?php if ($food['discount'] > 0): ?>
                        <p>İndirim: %<?= $food['discount'] ?></p>
                        <p>İndirimli Fiyat: <?= number_format($food['price'] * (1 - $food['discount'] / 100), 2) ?> TL</p>
                    <?php endif; ?>
                    <form action="add_to_cart.php" method="POST">
                        <input type="hidden" name="food_id" value="<?= $food['id'] ?>">
                        <input type="number" name="quantity" value="1" min="1" max="10">
                        <button type="submit" class="btn">Sepete Ekle</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <p><a href="customer_dashboard.php" class="btn">Ana Sayfaya Dön</a></p>
</div>
</div>
</body>
</html><?php include './footer.php'; ?>