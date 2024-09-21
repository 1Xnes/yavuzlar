<?php
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/includes/functions.php';

// Kullanıcının giriş yapmış ve firma rolüne sahip olduğundan emin olun
requireLogin();
requireRole('company');

// Kullanıcının firma bilgilerini al
$userId = $_SESSION['user_id'];
$companyInfo = getCompanyInfoByUserId($pdo, $userId);

// Firmanın restoranlarını al
$restaurants = getRestaurantsByCompanyId($pdo, $companyInfo['id']);

// Başarı veya hata mesajlarını kontrol et
$success_message = $_SESSION['success_message'] ?? null;
$error_message = $_SESSION['error_message'] ?? null;
unset($_SESSION['success_message'], $_SESSION['error_message']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restoranlarım</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="bgspecial">
<div class="container">
    <h1>Restoranlarım</h1>
    
    <?php if ($success_message): ?>
        <p class="success"><?= htmlspecialchars($success_message) ?></p>
    <?php endif; ?>
    
    <?php if ($error_message): ?>
        <p class="error"><?= htmlspecialchars($error_message) ?></p>
    <?php endif; ?>
    
    <?php if (empty($restaurants)): ?>
        <p>Henüz restoran eklenmemiş.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Restoran Adı</th>
                    <th>Açıklama</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($restaurants as $restaurant): ?>
                    <tr>
                        <td><?= htmlspecialchars($restaurant['name']) ?></td>
                        <td><?= htmlspecialchars($restaurant['description']) ?></td>
                        <td>
                            <a href="company_edit_restaurant.php?id=<?= $restaurant['id'] ?>" class="btn">Düzenle</a>
                            <a href="company_delete_restaurant.php?id=<?= $restaurant['id'] ?>" class="btn" onclick="return confirm('Bu restoranı silmek istediğinizden emin misiniz?')">Sil</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    
    <p><a href="company_add_restaurant.php" class="btn">Yeni Restoran Ekle</a></p>
    <p><a href="company_dashboard.php" class="btn">Firma Paneline Dön</a></p>
</div>
</div>
</body>
</html><?php include './footer.php'; ?>