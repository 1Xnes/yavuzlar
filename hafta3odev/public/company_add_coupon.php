<?php
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/includes/functions.php';

requireLogin();
requireRole('company');

$companyId = $_SESSION['user_id'];
$companyInfo = getCompanyInfoByUserId($pdo, $companyId);
$restaurants = getRestaurantsByCompanyId($pdo, $companyInfo['id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $discount = $_POST['discount'] ?? 0;
    $restaurantId = $_POST['restaurant_id'] ?? null;

    if (addCoupon($pdo, $name, $discount, $restaurantId)) {
        $_SESSION['success_message'] = "Kupon başarıyla eklendi.";
        header("Location: company_coupons.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Kupon eklenirken bir hata oluştu.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kupon Ekle</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="container">
    <h1>Kupon Ekle</h1>

    <?php if (isset($_SESSION['success_message'])): ?>
        <p class="success"><?= htmlspecialchars($_SESSION['success_message']) ?></p>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <p class="error"><?= htmlspecialchars($_SESSION['error_message']) ?></p>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <form method="POST">
        <label for="name">Kupon Adı:</label>
        <input type="text" id="name" name="name" required>
        <br>
        <label for="discount">İndirim Oranı (%):</label>
        <input type="number" id="discount" name="discount" min="0" max="100" step="0.01" required>
        <br>
        <label for="restaurant_id">Restoran:</label>
        <select id="restaurant_id" name="restaurant_id">
            <?php foreach ($restaurants as $restaurant): ?>
                <option value="<?= $restaurant['id'] ?>"><?= htmlspecialchars($restaurant['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <br> <br>
        <button type="submit" class="btn">Kupon Ekle</button>
    </form>

    <p><a href="company_coupons.php" class="btn">Kupon Listesine Dön</a></p>
</div>
</body>
</html><?php include './footer.php'; ?>