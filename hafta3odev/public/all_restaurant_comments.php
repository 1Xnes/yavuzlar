<?php
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/includes/functions.php';

requireLogin();
requireRole('customer'); // Yorumları görmek için müşteri rolü yeterli

$comments = getAllRestaurantComments($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tüm Restoran Yorumları</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="bgspecial">
<div class="container">
    <h1>Tüm Restoran Yorumları</h1>

    <?php if (isset($_SESSION['success_message'])): ?>
        <p class="success"><?= htmlspecialchars($_SESSION['success_message']) ?></p>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <p class="error"><?= htmlspecialchars($_SESSION['error_message']) ?></p>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <?php if (empty($comments)): ?>
        <p>Henüz hiç yorum yapılmamış.</p>
    <?php else: ?>
        <div class="comments-list">
            <?php foreach ($comments as $comment): ?>
                <div class="comment">
                    <h3><?= htmlspecialchars($comment['title']) ?></h3>
                    <p><?= htmlspecialchars($comment['description']) ?></p>
                    <p>Puan: <?= htmlspecialchars($comment['score']) ?>/10</p>
                    <p>Restoran: <?= htmlspecialchars($comment['restaurant_name']) ?></p>
                    <p>Yorum Yapan: <?= htmlspecialchars($comment['user_name']) ?> <?= htmlspecialchars($comment['user_surname']) ?></p>
                    <p>Tarih: <?= htmlspecialchars($comment['created_at']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <p><a href="customer_restaurants.php" class="btn">Restoran Listesine Dön</a></p>
    <p><a href="customer_dashboard.php" class="btn">Ana Sayfaya Dön</a></p>
</div>
</div>
</body>
</html>

<?php include './footer.php'; ?>