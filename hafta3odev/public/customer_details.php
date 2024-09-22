<?php
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/includes/functions.php';

requireLogin();
requireAdmin();

$customerId = $_GET['id'] ?? 0;
$customer = getCustomerDetails($pdo, $customerId);
$activeOrders = getActiveOrdersForCustomer($pdo, $customerId);

if (!$customer) {
    $_SESSION['error_message'] = "Müşteri bulunamadı.";
    header("Location: customer_list.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Müşteri Detayları</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="bgspecial">
<div class="container">
    <h1>Müşteri Detayları</h1>
    <h2><?= htmlspecialchars($customer['name'] . ' ' . $customer['surname']) ?></h2>
    <p>Kullanıcı Adı: <?= htmlspecialchars($customer['username']) ?></p>
    <p>Bakiye: <?= htmlspecialchars($customer['balance']) ?> TL</p>
    <p>Toplam Sipariş Sayısı: <?= htmlspecialchars($customer['order_count']) ?></p>
    <p>Toplam Harcama: <?= htmlspecialchars($customer['total_spent']) ?> TL</p>
    <p>Durum: <?= $customer['deleted_at'] ? 'Banlanmış' : 'Aktif' ?></p>

    <h3>Aktif Siparişler</h3>
    <?php if (empty($activeOrders)): ?>
        <p>Aktif sipariş bulunmamaktadır.</p>
    <?php else: ?>
        <table>
            <tr>
                <th>Sipariş ID</th>
                <th>Toplam Fiyat</th>
                <th>Durum</th>
                <th>Sipariş Tarihi</th>
                <th>Not</th>
            </tr>
            <?php foreach ($activeOrders as $order): ?>
            <tr>
                <td><?= htmlspecialchars($order['id']) ?></td>
                <td><?= htmlspecialchars($order['total_price']) ?> TL</td>
                <td><?= htmlspecialchars($order['order_status']) ?></td>
                <td><?= htmlspecialchars($order['created_at']) ?></td>
                <td><?= htmlspecialchars($order['note'] ?? '') ?></td> <!-- Not alanını ekleyin -->
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <p><a href="customer_list.php" class="btn">Müşteri Listesine Dön</a></p>
</div>
</div>
</body>
</html><?php include './footer.php'; ?>