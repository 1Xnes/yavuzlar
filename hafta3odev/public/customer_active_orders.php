<?php
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/includes/functions.php';

requireLogin();
requireRole('customer');

$userId = $_SESSION['user_id'];
$activeOrders = getActiveOrdersByUserId($pdo, $userId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aktif Siparişlerim</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="bgspecial">
<div class="container">
    <h1>Aktif Siparişlerim</h1>

    <?php if (empty($activeOrders)): ?>
        <p>Aktif siparişiniz bulunmuyor.</p>
    <?php else: ?>
        <?php foreach ($activeOrders as $order): ?>
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
                            <?php if ($item['note']): ?>
                                <br>Not: <?= htmlspecialchars($item['note']) ?>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <?php if ($order['order_status'] !== 'Teslim Edildi'): ?>
                    <a href="order_status.php?id=<?= $order['id'] ?>" class="btn">Sipariş Durumunu Görüntüle</a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <p><a href="customer_dashboard.php" class="btn">Ana Sayfaya Dön</a></p>
</div>
</div>
</body>
</html><?php include './footer.php'; ?>