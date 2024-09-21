<?php
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/includes/functions.php';

requireLogin();
requireRole('company');

$userId = $_SESSION['user_id'];
$companyInfo = getCompanyInfoByUserId($pdo, $userId);

// Filtreleme parametrelerini al
$status = $_GET['status'] ?? '';
$dateFrom = $_GET['date_from'] ?? '';
$dateTo = $_GET['date_to'] ?? '';

// Siparişleri al
$orders = getOrdersByCompanyId($pdo, $companyInfo['id'], $status, $dateFrom, $dateTo);

// Sipariş durumu güncelleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['new_status'])) {
    $orderId = $_POST['order_id'];
    $newStatus = $_POST['new_status'];
    if (updateOrderStatus($pdo, $orderId, $newStatus, $companyInfo['id'])) {
        $_SESSION['success_message'] = "Sipariş durumu başarıyla güncellendi.";
    } else {
        $_SESSION['error_message'] = "Sipariş durumu güncellenirken bir hata oluştu.";
    }
    header("Location: company_orders.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sipariş Yönetimi</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="bgspecial">
<div class="container">
    <h1>Sipariş Yönetimi</h1>

    <?php if (isset($_SESSION['success_message'])): ?>
        <p class="success"><?= htmlspecialchars($_SESSION['success_message']) ?></p>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <p class="error"><?= htmlspecialchars($_SESSION['error_message']) ?></p>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <form method="GET" action="">
        <select name="status">
            <option value="">Tüm Durumlar</option>
            <option value="Hazırlanıyor" <?= $status === 'Hazırlanıyor' ? 'selected' : '' ?>>Hazırlanıyor</option>
            <option value="Yola Çıktı" <?= $status === 'Yola Çıktı' ? 'selected' : '' ?>>Yola Çıktı</option>
            <option value="Teslim Edildi" <?= $status === 'Teslim Edildi' ? 'selected' : '' ?>>Teslim Edildi</option>
        </select>
        <input type="date" name="date_from" value="<?= htmlspecialchars($dateFrom) ?>" placeholder="Başlangıç Tarihi">
        <input type="date" name="date_to" value="<?= htmlspecialchars($dateTo) ?>" placeholder="Bitiş Tarihi">
        <button type="submit" class="btn">Filtrele</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Sipariş ID</th>
                <th>Müşteri</th>
                <th>Restoran</th>
                <th>Toplam Fiyat</th>
                <th>Durum</th>
                <th>Tarih</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?= htmlspecialchars($order['id']) ?></td>
                    <td><?= htmlspecialchars($order['customer_name']) ?></td>
                    <td><?= htmlspecialchars($order['restaurant_name']) ?></td>
                    <td><?= number_format($order['total_price'], 2) ?> TL</td>
                    <td><?= htmlspecialchars($order['order_status']) ?></td>
                    <td><?= htmlspecialchars($order['created_at']) ?></td>
                    <td>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                            <select name="new_status">
                                <option value="Hazırlanıyor" <?= $order['order_status'] === 'Hazırlanıyor' ? 'selected' : '' ?>>Hazırlanıyor</option>
                                <option value="Yola Çıktı" <?= $order['order_status'] === 'Yola Çıktı' ? 'selected' : '' ?>>Yola Çıktı</option>
                                <option value="Teslim Edildi" <?= $order['order_status'] === 'Teslim Edildi' ? 'selected' : '' ?>>Teslim Edildi</option>
                            </select>
                            <button type="submit" class="btn">Güncelle</button>
                        </form>
                        <a href="company_order_details.php?id=<?= $order['id'] ?>" class="btn">Detaylar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <p><a href="company_dashboard.php" class="btn">Firma Paneline Dön</a></p>
</div>
</div>
</body>
</html><?php include './footer.php'; ?>