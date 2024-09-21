<?php
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/includes/functions.php';

requireLogin();
requireRole('customer');

$userId = $_SESSION['user_id'];
$pastOrders = getPastOrdersByUserId($pdo, $userId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sipariş Geçmişim</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="bgspecial">
<div class="container">
    <h1>Sipariş Geçmişim</h1>

    <?php if (empty($pastOrders)): ?>
        <p>Henüz sipariş vermemişsiniz.</p>
    <?php else: ?>
        <?php foreach ($pastOrders as $order): ?>
            <div class="order">
                <h2>Sipariş #<?= $order['id'] ?></h2>
                <p>Tarih: <?= $order['created_at'] ?></p>
                <p>Durum: <?= $order['order_status'] ?></p>
                <p>Toplam: <?= number_format($order['total_price'], 2) ?> TL</p>
                <h3>Sipariş Detayları:</h3>
                <ul>
                    <?php foreach ($order['items'] as $item): ?>
                        <li>
                            <?= htmlspecialchars($item['food_name']) ?> - 
                            <?= $item['quantity'] ?> adet - 
                            <?= number_format($item['price'], 2) ?> TL
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <p><a href="customer_dashboard.php" class="btn">Ana Sayfaya Dön</a></p>
</div>
</div>
</body>
</html><?php include './footer.php'; ?>