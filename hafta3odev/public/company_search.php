<?php
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/includes/functions.php';

requireLogin();
requireAdmin();

$search = $_GET['search'] ?? '';
$companies = searchCompanies($pdo, $search);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Firma Arama</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="bgspecial">
<div class="container">
    <h1>Firma Arama</h1>
    <form method="GET">
        <input type="text" name="search" placeholder="Firma Adı" value="<?= htmlspecialchars($search) ?>">
        <input type="submit" value="Ara" class="btn">
    </form>
    
    <?php if ($search): ?>
    <table>
        <tr>
            <th>ID</th>
            <th>Firma Adı</th>
            <th>Açıklama</th>
            <th>Sahibi</th>
            <th>İşlemler</th>
        </tr>
        <?php foreach ($companies as $company): ?>
        <tr>
            <td><?= htmlspecialchars($company['id']) ?></td>
            <td><?= htmlspecialchars($company['name']) ?></td>
            <td><?= htmlspecialchars($company['description']) ?></td>
            <td><?= htmlspecialchars($company['owner_name'] . ' ' . $company['owner_surname']) ?></td>
            <td>
                <a href="company_edit.php?id=<?= $company['id'] ?>" class="btn">Düzenle</a>
                <a href="company_delete.php?id=<?= $company['id'] ?>" class="btn" onclick="return confirm('Bu firmayı silmek istediğinize emin misiniz?');">Sil</a>
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