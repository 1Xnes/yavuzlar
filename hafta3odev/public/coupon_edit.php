<?php
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/includes/functions.php';

requireLogin();
requireAdmin();

$couponId = $_GET['id'] ?? 0;
$coupon = getCouponById($pdo, $couponId);
$restaurants = getRestaurants($pdo);

if (!$coupon) {
    $_SESSION['error_message'] = "Kupon bulunamadı.";
    header("Location: coupon_list.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $discount = $_POST['discount'] ?? '';
    $restaurantId = $_POST['restaurant_id'] !== '' ? $_POST['restaurant_id'] : null;

    if (updateCoupon($pdo, $couponId, $name, $discount, $restaurantId)) {
        $_SESSION['success_message'] = "Kupon başarıyla güncellendi.";
        header("Location: coupon_list.php");
        exit();
    } else {
        $error = "Kupon güncellenirken bir hata oluştu.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kupon Düzenle</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="bgspecial">
<div class="container">
    <h1>Kupon Düzenle</h1>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?= $error ?></p>
    <?php endif; ?>
    <form method="POST">
        <input type="text" name="name" value="<?= htmlspecialchars($coupon['name']) ?>" placeholder="Kupon Adı" required><br>
        <input type="number" name="discount" value="<?= htmlspecialchars($coupon['discount']) ?>" placeholder="İndirim Yüzdesi" min="0" max="100" required><br>
        <select name="restaurant_id">
            <option value="">Tüm Restoranlar</option>
            <?php foreach ($restaurants as $restaurant): ?>
                <option value="<?= $restaurant['id'] ?>" <?= $coupon['restaurant_id'] == $restaurant['id'] ? 'selected' : '' ?>><?= htmlspecialchars($restaurant['name']) ?></option>
            <?php endforeach; ?>
        </select><br>
        <input type="submit" value="Kuponu Güncelle" class="btn">
    </form>
    <p><a href="coupon_list.php" class="btn">Kupon Listesine Dön</a></p>
</div>
</div>
</body>
</html><?php include './footer.php'; ?>