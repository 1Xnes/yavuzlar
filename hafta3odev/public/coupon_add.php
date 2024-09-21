<?php
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/includes/functions.php';

requireLogin();
requireAdmin();

$restaurants = getRestaurants($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $discount = $_POST['discount'] ?? '';
    $restaurantId = $_POST['restaurant_id'] !== '' ? $_POST['restaurant_id'] : null;

    if (addCoupon($pdo, $name, $discount, $restaurantId)) {
        $_SESSION['success_message'] = "Kupon başarıyla eklendi.";
        header("Location: coupon_list.php");
        exit();
    } else {
        $error = "Kupon eklenirken bir hata oluştu.";
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
<div class="bgspecial">
<div class="container">
    <h1>Kupon Ekle</h1>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?= $error ?></p>
    <?php endif; ?>
    <form method="POST">
        <input type="text" name="name" placeholder="Kupon Adı" required><br>
        <input type="number" name="discount" placeholder="İndirim Yüzdesi" min="0" max="100" required><br>
        <select name="restaurant_id">
            <option value="">Tüm Restoranlar</option>
            <?php foreach ($restaurants as $restaurant): ?>
                <option value="<?= $restaurant['id'] ?>"><?= htmlspecialchars($restaurant['name']) ?></option>
            <?php endforeach; ?>
        </select><br>
        <input type="submit" value="Kupon Ekle" class="btn">
    </form>
    <p><a href="coupon_list.php" class="btn">Kupon Listesine Dön</a></p>
</div>
</div>
</body>
</html><?php include './footer.php'; ?>