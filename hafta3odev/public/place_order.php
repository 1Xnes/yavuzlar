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
            header("Location: customer_cart.php");
            exit();
        }
    } else {
        $_SESSION['error_message'] = "Geçersiz kupon kodu.";
        unset($_SESSION['applied_coupon']);
        header("Location: customer_cart.php");
        exit();
    }
}

$total = calculateCartTotal($cartItems, $discount);


// bu kısımda kullanıcı bakiyesini aldık
$user = getUserById($pdo, $userId);
$currentBalance = $user['balance'];

// bakiye kontrolü
if ($currentBalance < $total) {
    $_SESSION['error_message'] = "Yetersiz bakiye. Lütfen bakiyenizi artırın.";
    header("Location: customer_cart.php");
    exit();
}




if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo->beginTransaction();

    try {
        // Yeni sipariş oluştur
        $orderId = createOrder($pdo, $userId, $total);

        // Sepet öğelerini sipariş öğelerine dönüştür
        foreach ($cartItems as $item) {
            if (!isset($item['food_id'])) {
                throw new Exception("Sepetteki bir öğenin yemek ID'si tanımlı değil.");
            }
            addOrderItem($pdo, $orderId, $item['food_id'], $item['quantity'], $item['price'], $item['note']);
        }

        // Kullanıcının bakiyesini güncelle
        updateUserBalance($pdo, $userId, -$total);

        // Sepeti temizle
        clearCart($pdo, $userId);

        $pdo->commit();
        $_SESSION['success_message'] = "Siparişiniz başarıyla oluşturuldu.";
        header("Location: customer_order_history.php");
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error_message'] = "Sipariş oluşturulurken bir hata oluştu: " . $e->getMessage();
        header("Location: customer_cart.php");
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sipariş Onayı</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="bgspecial">
<div class="container">
    <h1>Sipariş Onayı</h1>
    
    <h2>Sepetinizdeki Ürünler:</h2>
    <table>
        <thead>
            <tr>
                <th>Yemek</th>
                <th>Fiyat</th>
                <th>Miktar</th>
                <th>Toplam</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cartItems as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['food_name']) ?></td>
                    <td><?= number_format($item['price'], 2) ?> TL</td>
                    <td><?= $item['quantity'] ?></td>
                    <td><?= number_format($item['price'] * $item['quantity'], 2) ?> TL</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if ($couponCode): ?>
        <p>Uygulanan Kupon: <?= htmlspecialchars($couponCode) ?></p>
        <p>İndirim: %<?= $discount ?></p>
    <?php endif; ?>

    <p>Toplam Tutar: <?= number_format($total, 2) ?> TL</p>

    <form method="POST">
        <button type="submit" class="btn">Siparişi Onayla</button>
    </form>

    <p><a href="customer_cart.php" class="btn">Sepete Geri Dön</a></p>
</div>
</div>
</body>
</html></html><?php include './footer.php'; ?>