<?php
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/includes/functions.php';

requireLogin();
requireRole('customer');

$userId = $_SESSION['user_id'];
$cartItems = getCartItems($pdo, $userId);

// Kupon uygulanmış mı kontrol et
$couponCode = isset($_SESSION['applied_coupon']) ? $_SESSION['applied_coupon'] : '';
$discount = 0;

if ($couponCode) {
    $coupon = getCouponByCode($pdo, $couponCode);

    if ($coupon) {
        $validCoupon = false;

        foreach ($cartItems as $item) {
            if ($coupon['restaurant_id'] == $item['restaurant_id']) {
                $validCoupon = true;
                break;
            }
        }

        if ($validCoupon) {
            $discount = $coupon['discount'];
        } else {
            $_SESSION['error_message'] = "Bu kupon kodu sepetinizdeki ürünler için geçerli değil.";
            unset($_SESSION['applied_coupon']);
        }
    } else {
        $_SESSION['error_message'] = "Geçersiz kupon kodu.";
        unset($_SESSION['applied_coupon']);
    }
}

$total = calculateCartTotal($cartItems, $discount);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sepetim</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="bgspecial">
<div class="container">
    <h1>Sepetim</h1>

    <?php if (isset($_SESSION['success_message'])): ?>
        <p class="success"><?= htmlspecialchars($_SESSION['success_message']) ?></p>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <p class="error"><?= htmlspecialchars($_SESSION['error_message']) ?></p>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <?php if (empty($cartItems)): ?>
        <p>Sepetiniz boş.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Yemek</th>
                    <th>Fiyat</th>
                    <th>İndirimli Fiyat</th>
                    <th>Miktar</th>
                    <th>Toplam</th>
                    <th>Not</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($cartItems as $item): ?>
    <tr>
        <td><?= htmlspecialchars($item['food_name']) ?></td>
        <td><?= number_format($item['price'], 2) ?> TL</td>
        <td><?= number_format($item['price'] * (1 - $item['discount'] / 100), 2) ?> TL</td>
        <td>
            <form action="update_cart.php" method="POST">
                <input type="hidden" name="basket_id" value="<?= $item['id'] ?>">
                <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" max="10">
                <button type="submit" class="btn">Güncelle</button>
            </form>
        </td>
        <td><?= number_format($item['price'] * $item['quantity'] * (1 - $item['discount'] / 100), 2) ?> TL</td>
        <td>
            <form action="update_cart_note.php" method="POST">
                <input type="hidden" name="basket_id" value="<?= $item['id'] ?>">
                <input type="text" name="note" value="<?= htmlspecialchars($item['note']) ?>">
                <button type="submit" class="btn">Not Güncelle</button>
            </form>
        </td>
        <td>
            <form action="remove_from_cart.php" method="POST">
                <input type="hidden" name="basket_id" value="<?= $item['id'] ?>">
                <button type="submit" class="btn">Kaldır</button>
            </form>
        </td>
    </tr>
<?php endforeach; ?>
            </tbody>
        </table>

        <?php if ($couponCode): ?>
            <p>Uygulanan Kupon: <?= htmlspecialchars($couponCode) ?></p>
            <p>İndirim: %<?= $discount ?></p>
            <form action="remove_coupon.php" method="POST">
                <button type="submit" class="btn">Kuponu Kaldır</button>
            </form>
        <?php else: ?>
            <form action="apply_coupon.php" method="POST">
                <label for="coupon_code">Kupon Kodu:</label>
                <input type="text" id="coupon_code" name="coupon_code">
                <button type="submit" class="btn">Kuponu Uygula</button>
            </form>
        <?php endif; ?>

        <p>Toplam: <?= number_format($total, 2) ?> TL</p>

        <form action="place_order.php" method="POST">
            <button type="submit" class="btn">Sipariş Ver</button>
        </form>
    <?php endif; ?>

    <p><a href="customer_restaurants.php" class="btn">Alışverişe Devam Et</a></p>
    <p><a href="customer_dashboard.php" class="btn">Ana Sayfaya Dön</a></p>
</div>
</div>
</body>
</html></html><?php include './footer.php'; ?>