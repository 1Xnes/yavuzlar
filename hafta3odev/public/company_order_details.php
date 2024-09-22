<?php
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/includes/functions.php';

requireLogin();
requireRole('company');

$userId = $_SESSION['user_id'];
$companyInfo = getCompanyInfoByUserId($pdo, $userId);

$orderId = $_GET['id'] ?? 0;
$orderDetails = getOrderDetails($pdo, $orderId, $companyInfo['id']);

if (!$orderDetails) {
    $_SESSION['error_message'] = "Geçersiz sipariş veya yetkiniz yok.";
    header("Location: company_orders.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sipariş Detayları</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="bgspecial">
<div class="container">
    <h1>Sipariş Detayları</h1>

    <h2>Sipariş Bilgileri</h2>
    <p>Sipariş ID: <?= htmlspecialchars($orderDetails['id']) ?></p>
    <p>Müşteri: <?= htmlspecialchars($orderDetails['customer_name']) ?></p>
    <p>Restoran: <?= htmlspecialchars($orderDetails['restaurant_name']) ?></p>
    <p>Toplam Fiyat: <?= number_format($orderDetails['total_price'], 2) ?> TL</p>
    <p>Durum: <?= htmlspecialchars($orderDetails['order_status']) ?></p>
    <p>Tarih: <?= htmlspecialchars($orderDetails['created_at']) ?></p>

    <h2>Sipariş Öğeleri</h2>
    <table>
        <thead>
            <tr>
                <th>Yemek Adı</th>
                <th>Adet</th>
                <th>Birim Fiyat</th>
                <th>Toplam Fiyat</th>
                <th>Not</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orderDetails['items'] as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['food_name']) ?></td>
                    <td><?= htmlspecialchars($item['quantity']) ?></td>
                    <td><?= number_format($item['price'], 2) ?> TL</td>
                    <td><?= number_format($item['quantity'] * $item['price'], 2) ?> TL</td>
                    <td><?= htmlspecialchars($item['note'] ?? '') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <p><a href="company_orders.php" class="btn">Siparişlere Dön</a></p>
</div>
</div>
</body>
</html><?php include './footer.php'; ?>