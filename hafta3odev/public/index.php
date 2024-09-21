<?php
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/includes/functions.php';

if (!isAdminUserExists($pdo)) {
    createDefaultAdminUser($pdo);
}
?>

<!DOCTYPE html>
<html lang="en">
<div class="bgspecial">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yemek Yönetim Sistemi</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <div class="container">
    <h1>Yemek Yönetim Sistemi</h1>
    <?php if (isLoggedIn()): ?>
        <p>Hoşgeldin, <?= htmlspecialchars($_SESSION['user_name']) ?>!</p>
        <?php if (isAdmin()): ?>
            <p><a href="admin.php" class="btn">Admin Paneli</a></p>
        <?php elseif (isCompany()): ?>
            <p><a href="company_dashboard.php" class="btn">Firma Paneli</a></p>
        <?php else: ?>
            <p><a href="customer_dashboard.php" class="btn">Müşteri Paneli</a></p>
        <?php endif; ?>
        <p><a href="logout.php" class="btn">Çıkış Yap</a></p>
    <?php else: ?>
        <p><a href="login.php" class="btn">Giriş yap</a></p>
        <p><a href="register.php" class="btn">Kaydol</a></p>
    <?php endif; ?>
    </div>
</body>
</div>
</html><?php include './footer.php'; ?>