<?php
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/includes/functions.php';

requireLogin();
requireAdmin();

$search = $_GET['search'] ?? '';
$customers = searchCustomers($pdo, $search);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Müşteri Arama</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="bgspecial">
<div class="container">
    <h1>Müşteri Arama</h1>
    
    <form method="GET">
        <input type="text" name="search" placeholder="Müşteri Adı veya Kullanıcı Adı" value="<?= htmlspecialchars($search) ?>">
        <input type="submit" value="Ara" class="btn">
    </form>
    
    <?php if ($search): ?>
    <table>
        <tr>
            <th>ID</th>
            <th>Ad</th>
            <th>Soyad</th>
            <th>Kullanıcı Adı</th>
            <th>Durum</th>
            <th>İşlemler</th>
        </tr>
        <?php foreach ($customers as $customer): ?>
        <tr>
            <td><?= htmlspecialchars($customer['id']) ?></td>
            <td><?= htmlspecialchars($customer['name']) ?></td>
            <td><?= htmlspecialchars($customer['surname']) ?></td>
            <td><?= htmlspecialchars($customer['username']) ?></td>
            <td><?= $customer['deleted_at'] ? 'Silinmiş' : 'Aktif' ?></td>
            <td>
                <?php if (!$customer['deleted_at']): ?>
                    <a href="customer_ban.php?id=<?= $customer['id'] ?>" class="btn" onclick="return confirm('Bu müşteriyi banlamak istediğinize emin misiniz?');">Banla</a>
                <?php else: ?>
                    <a href="customer_unban.php?id=<?= $customer['id'] ?>" class="btn">Banı Kaldır</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php endif; ?>
    
    <p><a href="admin.php" class="btn">Admin Paneline Dön</a></p>
</div>
</div>
</body>
</html><?php include './footer.php'; ?>