<?php
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/includes/functions.php';

requireLogin();
requireAdmin();

$filter = $_GET['filter'] ?? 'all';
$companies = getCompanies($pdo, $filter);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Firma Listesi</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="bgspecial">
<div class="container">
    <h1>Firma Listesi</h1>
    
    <form method="GET">
        <select name="filter">
            <option value="all" <?= $filter == 'all' ? 'selected' : '' ?>>Tümü</option>
            <option value="active" <?= $filter == 'active' ? 'selected' : '' ?>>Aktif</option>
            <option value="deleted" <?= $filter == 'deleted' ? 'selected' : '' ?>>Silinmiş</option>
        </select>
        <input type="submit" value="Filtrele" class="btn">
    </form>
    
    <table>
        <tr>
            <th>ID</th>
            <th>Firma Adı</th>
            <th>Açıklama</th>
            <th>Sahibi</th>
            <th>Durum</th>
            <th>İşlemler</th>
        </tr>
        <?php foreach ($companies as $company): ?>
        <tr>
            <td><?= htmlspecialchars($company['id']) ?></td>
            <td><?= htmlspecialchars($company['name']) ?></td>
            <td><?= htmlspecialchars($company['description']) ?></td>
            <td><?= htmlspecialchars($company['owner_name'] . ' ' . $company['owner_surname']) ?></td>
            <td><?= $company['deleted_at'] ? 'Silinmiş' : 'Aktif' ?></td>
            <td>
                <?php if (!$company['deleted_at']): ?>
                    <a href="company_edit.php?id=<?= $company['id'] ?>" class="btn">Düzenle</a>
                    <a href="company_delete.php?id=<?= $company['id'] ?>" class="btn" onclick="return confirm('Bu firmayı silmek istediğinize emin misiniz?');">Sil</a>
                    <br><br><br><a href="admin_company_foods.php?id=<?= $company['id'] ?>" class="btn">Yemekleri Görüntüle</a>
                <?php else: ?>
                    <a href="company_restore.php?id=<?= $company['id'] ?>" class="btn">Geri Yükle</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <p><a href="user_add.php" class="btn">Yeni Firma Ekle</a></p>
    <p><a href="admin.php" class="btn">Admin Paneline Dön</a></p>
</div>
</div>
</body>
</html>
<?php include './footer.php'; ?>