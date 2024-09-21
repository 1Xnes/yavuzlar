<?php
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/includes/functions.php';

requireLogin();
requireRole('company');

$companyId = $_SESSION['user_id'];
$companyInfo = getCompanyInfoByUserId($pdo, $companyId);
$coupons = getCouponsByCompanyId($pdo, $companyInfo['id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuponlar</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="container">
    <h1>Kuponlar</h1>

    <?php if (isset($_SESSION['success_message'])): ?>
        <p class="success"><?= htmlspecialchars($_SESSION['success_message']) ?></p>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <p class="error"><?= htmlspecialchars($_SESSION['error_message']) ?></p>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <p><a href="company_add_coupon.php" class="btn">Yeni Kupon Ekle</a></p>

    <?php if (empty($coupons)): ?>
        <p>Henüz kupon eklenmemiş.</p>
    <?php else: ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Kupon Adı</th>
                <th>İndirim Oranı</th>
                <th>Restoran</th>
                <th>İşlemler</th>
            </tr>
            <?php foreach ($coupons as $coupon): ?>
            <tr>
                <td><?= htmlspecialchars($coupon['id']) ?></td>
                <td><?= htmlspecialchars($coupon['name']) ?></td>
                <td><?= htmlspecialchars($coupon['discount']) ?> %</td>
                <td><?= htmlspecialchars($coupon['restaurant_name']) ?></td>
                <td>
                <a href="company_delete_coupon.php?id=<?= $coupon['id'] ?>" class="btn" onclick="return confirm('Bu kuponu silmek istediğinize emin misiniz?');">Sil</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <p><a href="company_dashboard.php" class="btn">Ana Sayfaya Dön</a></p>
</div>
</body>
</html><?php include './footer.php'; ?>