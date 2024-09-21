<?php
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/includes/functions.php';

requireLogin();
requireRole('company');

$userId = $_SESSION['user_id'];
$companyInfo = getCompanyInfoByUserId($pdo, $userId);

$restaurants = getRestaurantsByCompanyId($pdo, $companyInfo['id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? '';
    $discount = $_POST['discount'] ?? 0;
    $restaurantId = $_POST['restaurant_id'] ?? '';
    $image = $_FILES['image'] ?? null;

    if (empty($name) || empty($price) || empty($restaurantId)) {
        $error = "Lütfen tüm gerekli alanları doldurun.";
    } else {
        $result = addFood($pdo, $restaurantId, $name, $description, $price, $discount, $image);
        if ($result) {
            $_SESSION['success_message'] = "Yemek başarıyla eklendi.";
            header("Location: company_foods.php");
            exit();
        } else {
            $error = "Yemek eklenirken bir hata oluştu.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yeni Yemek Ekle</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="bgspecial">
<div class="container">
    <h1>Yeni Yemek Ekle</h1>
    
    <?php if (isset($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    
    <form method="POST" enctype="multipart/form-data">
        <label for="restaurant_id">Restoran:</label>
        <select id="restaurant_id" name="restaurant_id" required>
            <?php foreach ($restaurants as $restaurant): ?>
                <option value="<?= $restaurant['id'] ?>"><?= htmlspecialchars($restaurant['name']) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="name">Yemek Adı:</label>
        <input type="text" id="name" name="name" required>
        
        <label for="description">Açıklama:</label>
        <textarea id="description" name="description"></textarea>
        
        <label for="price">Fiyat:</label>
        <input type="number" id="price" name="price" step="0.01" required>
        
        <label for="discount">İndirim (%):</label>
        <input type="number" id="discount" name="discount" step="0.01" min="0" max="100" value="0">
        
        <label for="image">Yemek Resmi:</label>
        <input type="file" id="image" name="image" accept="image/*">
        
        <button type="submit" class="btn">Yemek Ekle</button>
    </form>
    
    <p><a href="company_foods.php" class="btn">Yemek Listesine Dön</a></p>
</div>
</div>
</body>
</html><?php include './footer.php'; ?>