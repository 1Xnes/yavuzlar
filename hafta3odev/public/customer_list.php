<?php
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/includes/functions.php';

requireLogin();
requireAdmin();

$filter = $_GET['filter'] ?? 'all';
$customers = getCustomers($pdo, $filter);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Müşteri Listesi</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="bgspecial">
<div class="container">
    <h1>Müşteri Listesi</h1>
    
    <form method="GET">
        <select name="filter">
            <option value="all" <?= $filter == 'all' ? 'selected' : '' ?>>Tümü</option>
            <option value="active" <?= $filter == 'active' ? 'selected' : '' ?>>Aktif</option>
            <option value="banned" <?= $filter == 'banned' ? 'selected' : '' ?>>Banlanmış</option>
        </select>
        <input type="submit" value="Filtrele" class="btn">
    </form>

    <table>
        <tr>
            <th>ID</th>
            <th>Ad</th>
            <th>Soyad</th>
            <th>Kullanıcı Adı</th>
            <th>Durum</th>
            <th>Detaylar</th>
            <th>İşlemler</th>
        </tr>
        <?php foreach ($customers as $customer): ?>
        <tr>
            <td><?= htmlspecialchars($customer['id']) ?></td>
            <td><?= htmlspecialchars($customer['name']) ?></td>
            <td><?= htmlspecialchars($customer['surname']) ?></td>
            <td><?= htmlspecialchars($customer['username']) ?></td>
            <td><?= $customer['deleted_at'] ? 'Banlanmış' : 'Aktif' ?></td>
            <td>
                <a href="customer_details.php?id=<?= $customer['id'] ?>" class="btn">Detaylar</a>
            </td>
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
    <p><a href="admin.php" class="btn">Admin Paneline Dön</a></p>
</div>
</div>
</body>
</html><?php include './footer.php'; ?>