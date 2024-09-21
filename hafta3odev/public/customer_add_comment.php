<?php
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/includes/functions.php';

requireLogin();
requireRole('customer');

$userId = $_SESSION['user_id'];
$restaurantId = $_GET['restaurant_id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $score = $_POST['score'] ?? 0;

    if ($restaurantId && $title && $description && $score) {
        if (addComment($pdo, $userId, $restaurantId, $title, $description, $score)) {
            $_SESSION['success_message'] = "Yorumunuz başarıyla eklendi.";
            header("Location: customer_restaurant_menu.php?id=$restaurantId");
            exit();
        } else {
            $_SESSION['error_message'] = "Yorum eklenirken bir hata oluştu.";
        }
    } else {
        $_SESSION['error_message'] = "Tüm alanları doldurun.";
    }
}

$restaurant = getRestaurantById($pdo, $restaurantId);

if (!$restaurant) {
    $_SESSION['error_message'] = "Restoran bulunamadı.";
    header("Location: customer_restaurants.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yorum Ekle</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="bgspecial">
<div class="container">
    <h1>Yorum Ekle</h1>

    <?php if (isset($_SESSION['error_message'])): ?>
        <p class="error"><?= htmlspecialchars($_SESSION['error_message']) ?></p>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <form method="POST">
        <label for="title">Başlık:</label>
        <input type="text" id="title" name="title" required>
        
        <label for="description">Açıklama:</label>
        <textarea id="description" name="description" required></textarea>
        
        <label for="score">Puan (1-10):</label>
        <input type="number" id="score" name="score" min="1" max="10" required>
        
        <button type="submit" class="btn">Yorumu Ekle</button>
    </form>

    <p><a href="customer_restaurant_menu.php?id=<?= $restaurantId ?>" class="btn">Geri Dön</a></p>
</div>
</div>
</body>
</html><?php include './footer.php'; ?>