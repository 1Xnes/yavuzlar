<?php
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/includes/functions.php';

requireLogin();
requireRole('company');

$userId = $_SESSION['user_id'];
$companyInfo = getCompanyInfoByUserId($pdo, $userId);

$foodId = $_GET['id'] ?? 0;
$food = getFoodById($pdo, $foodId);

if (!$food || !isCompanyOwnFood($pdo, $companyInfo['id'], $foodId)) {
    $_SESSION['error_message'] = "Geçersiz yemek veya yetkiniz yok.";
    header("Location: company_foods.php");
    exit();
}

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
        $result = updateFood($pdo, $foodId, $restaurantId, $name, $description, $price, $discount, $image);
        if ($result) {
            $_SESSION['success_message'] = "Yemek başarıyla güncellendi.";
            header("Location: company_foods.php");
            exit();
        } else {
            $error = "Yemek güncellenirken bir hata oluştu.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yemek Düzenle</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="bgspecial">
<div class="container">
    <h1>Yemek Düzenle</h1>
    
    <?php if (isset($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    
    <form method="POST" enctype="multipart/form-data">
        <label for="restaurant_id">Restoran:</label>
        <select id="restaurant_id" name="restaurant_id" required>
            <?php foreach ($restaurants as $restaurant): ?>
                <option value="<?= $restaurant['id'] ?>" <?= $restaurant['id'] == $food['restaurant_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($restaurant['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="name">Yemek Adı:</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($food['name']) ?>" required>
        
        <label for="description">Açıklama:</label>
        <textarea id="description" name="description"><?= htmlspecialchars($food['description']) ?></textarea>
        
        <label for="price">Fiyat:</label>
        <input type="number" id="price" name="price" step="0.01" value="<?= htmlspecialchars($food['price']) ?>" required>
        
        <label for="discount">İndirim (%):</label>
        <input type="number" id="discount" name="discount" step="0.01" min="0" max="100" value="<?= htmlspecialchars($food['discount']) ?>">
        
        <label for="image">Yeni Yemek Resmi:</label>
        <input type="file" id="image" name="image" accept="image/*">
        
        <?php if ($food['image_path']): ?>
            <p>Mevcut Resim:</p>
            <img src="<?= htmlspecialchars($food['image_path']) ?>" alt="Yemek Resmi" style="max-width: 200px;">
        <?php endif; ?>
        
        <button type="submit" class="btn">Yemek Güncelle</button>
    </form>
    
    <p><a href="company_foods.php" class="btn">Yemek Listesine Dön</a></p>
</div>
</div>
</body>
</html><?php include './footer.php'; ?>