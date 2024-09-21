<?php
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/includes/functions.php';

requireLogin();
requireAdmin();

$coupons = getCoupons($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kupon Listesi</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="bgspecial">
<div class="container">
    <h1>Kupon Listesi</h1>
    <table>
        <tr>
            <th>ID</th>
            <th>Kupon Adı</th>
            <th>İndirim</th>
            <th>Restoran</th>
            <th>Oluşturulma Tarihi</th>
            <th>İşlemler</th>
        </tr>
        <?php foreach ($coupons as $coupon): ?>
        <tr>
            <td><?= htmlspecialchars($coupon['id']) ?></td>
            <td><?= htmlspecialchars($coupon['name']) ?></td>
            <td><?= htmlspecialchars($coupon['discount']) ?>%</td>
            <td><?= $coupon['restaurant_id'] ? htmlspecialchars($coupon['restaurant_name']) : 'Tüm Restoranlar' ?></td>
            <td><?= htmlspecialchars($coupon['created_at']) ?></td>
            <td>
                <a href="coupon_edit.php?id=<?= $coupon['id'] ?>" class="btn">Düzenle</a>
                <a href="coupon_delete.php?id=<?= $coupon['id'] ?>" class="btn" onclick="return confirm('Bu kuponu silmek istediğinize emin misiniz?');">Sil</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <p><a href="coupon_add.php" class="btn">Yeni Kupon Ekle</a></p>
    <p><a href="admin.php" class="btn">Admin Paneline Dön</a></p>
</div>
</div>
</body>
</html><?php include './footer.php'; ?>