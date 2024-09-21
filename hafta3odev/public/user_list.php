<?php
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/includes/functions.php';

requireLogin();
requireAdmin();

$users = getAllUsers($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kullanıcı Listesi</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="container">
    <h1>Kullanıcı Listesi</h1>
    <table>
        <tr>
            <th>ID</th>
            <th>Ad</th>
            <th>Soyad</th>
            <th>Kullanıcı Adı</th>
            <th>Rol</th>
            <th>Durum</th>
            <th>İşlemler</th>
        </tr>
        <?php foreach ($users as $user): ?>
        <tr>
            <td><?= htmlspecialchars($user['id']) ?></td>
            <td><?= htmlspecialchars($user['name']) ?></td>
            <td><?= htmlspecialchars($user['surname']) ?></td>
            <td><?= htmlspecialchars($user['username']) ?></td>
            <td><?= htmlspecialchars($user['role']) ?></td>
            <td><?= $user['deleted_at'] ? 'Silinmiş' : 'Aktif' ?></td>
            <td>
                <?php if ($user['deleted_at']): ?>
                    <a href="user_restore.php?id=<?= $user['id'] ?>" class="btn">Geri Yükle</a>
                <?php else: ?>
                    <a href="user_edit.php?id=<?= $user['id'] ?>" class="btn">Düzenle</a>
                    <a href="user_delete.php?id=<?= $user['id'] ?>" class="btn" onclick="return confirm('Bu kullanıcıyı silmek istediğinize emin misiniz?');">Sil</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <p><a href="admin.php" class="btn">Admin Paneline Dön</a></p>
</div>
</body>
</html><?php include './footer.php'; ?>