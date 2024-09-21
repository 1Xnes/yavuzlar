<?php
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/includes/functions.php';

requireLogin();
requireRole('customer');

$userId = $_SESSION['user_id'];

// Arama parametrelerini al
$search = $_GET['search'] ?? '';

// Restoranları al
$restaurants = searchRestaurants($pdo, $search);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restoranlar</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="bgspecial">
<div class="container">
    <h1>Restoranlar</h1>

    <form method="GET" action="">
        <input type="text" name="search" placeholder="Restoran Ara" value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="btn">Ara</button>
    </form>

    <?php if (empty($restaurants)): ?>
        <p>Restoran bulunamadı.</p>
    <?php else: ?>
        <div class="restaurant-list">
            <?php foreach ($restaurants as $restaurant): ?>
                <div class="restaurant-item">
                    <h2><?= htmlspecialchars($restaurant['name']) ?></h2>
                    <p><?= htmlspecialchars($restaurant['description']) ?></p>
                    <?php if ($restaurant['image_path']): ?>
                        <img src="<?= htmlspecialchars($restaurant['image_path']) ?>" alt="<?= htmlspecialchars($restaurant['name']) ?>" style="max-width: 200px;">
                    <?php endif; ?>
                    <p>Ortalama Puan: <?= number_format($restaurant['average_score'], 1) ?>/10</p>
                    <a href="customer_restaurant_menu.php?id=<?= $restaurant['id'] ?>" class="btn">Menüyü Gör</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <p><a href="all_restaurant_comments.php" class="btn">Restoran Yorumlarına Bak</a></p>
    <p><a href="customer_dashboard.php" class="btn">Ana Sayfaya Dön</a></p>
</div>
</div>
</body>
</html><?php include './footer.php'; ?>