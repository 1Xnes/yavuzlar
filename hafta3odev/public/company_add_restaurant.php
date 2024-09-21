<?php
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/includes/functions.php';

requireLogin();
requireRole('company');

$userId = $_SESSION['user_id'];
$companyInfo = getCompanyInfoByUserId($pdo, $userId);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $image = $_FILES['image'] ?? null;

    if (empty($name)) {
        $error = "Restoran adı boş olamaz.";
    } else {
        $result = addRestaurant($pdo, $companyInfo['id'], $name, $description, $image);
        if ($result) {
            $_SESSION['success_message'] = "Restoran başarıyla eklendi.";
            header("Location: company_restaurants.php");
            exit();
        } else {
            $error = "Restoran eklenirken bir hata oluştu.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yeni Restoran Ekle</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="bgspecial">
<div class="container">
    <h1>Yeni Restoran Ekle</h1>
    
    <?php if (isset($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    
    <form method="POST" enctype="multipart/form-data">
        <label for="name">Restoran Adı:</label>
        <input type="text" id="name" name="name" required>
        
        <label for="description">Açıklama:</label>
        <textarea id="description" name="description"></textarea><br>
        
        <label for="image">Restoran Resmi:</label>
        <input type="file" id="image" name="image" accept="image/*">
        <br><br><br>
        <button type="submit" class="btn">Restoran Ekle</button>
    </form>
    
    <p><a href="company_restaurants.php" class="btn">Restoranlarıma Dön</a></p>
</div>
</div>
</body>
</html><?php include './footer.php'; ?>