<?php
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/includes/functions.php';

requireLogin();
requireRole('admin'); // Yorumları sadece admin görüntüleyebilir

$comments = getAllComments($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yorumlar</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="bgspecial">
<div class="container">
    <h1>Yorumlar</h1>

    <?php if (isset($_SESSION['success_message'])): ?>
        <p class="success"><?= htmlspecialchars($_SESSION['success_message']) ?></p>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <p class="error"><?= htmlspecialchars($_SESSION['error_message']) ?></p>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <?php if (empty($comments)): ?>
        <p>Henüz yorum yok.</p>
    <?php else: ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Kullanıcı</th>
                <th>Restoran</th>
                <th>Başlık</th>
                <th>Açıklama</th>
                <th>Puan</th>
                <th>İşlemler</th>
            </tr>
            <?php foreach ($comments as $comment): ?>
            <tr>
                <td><?= htmlspecialchars($comment['id']) ?></td>
                <td><?= htmlspecialchars($comment['user_name'] . ' ' . $comment['user_surname']) ?></td>
                <td><?= htmlspecialchars($comment['restaurant_name']) ?></td>
                <td><?= htmlspecialchars($comment['title']) ?></td>
                <td><?= htmlspecialchars($comment['description']) ?></td>
                <td><?= htmlspecialchars($comment['score']) ?></td>
                <td>
                    <a href="delete_comment.php?id=<?= $comment['id'] ?>" class="btn" onclick="return confirm('Bu yorumu silmek istediğinize emin misiniz?');">Sil</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <p><a href="admin.php" class="btn">Admin Paneline Dön</a></p>
</div>
</div>
</body>
</html>