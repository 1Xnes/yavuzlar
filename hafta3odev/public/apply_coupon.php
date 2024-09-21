<?php
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/includes/functions.php';

requireLogin();
requireRole('customer');

$userId = $_SESSION['user_id'];
$couponCode = $_POST['coupon_code'] ?? '';

if ($couponCode) {
    $coupon = getCouponByCode($pdo, $couponCode);

    if ($coupon) {
        $cartItems = getCartItems($pdo, $userId);
        $validCoupon = false;

        foreach ($cartItems as $item) {
            if ($coupon['restaurant_id'] == $item['restaurant_id']) {
                $validCoupon = true;
                break;
            }
        }

        if ($validCoupon) {
            $_SESSION['applied_coupon'] = $couponCode;
            $_SESSION['success_message'] = "Kupon kodu başarıyla uygulandı.";
        } else {
            $_SESSION['error_message'] = "Bu kupon kodu sepetinizdeki ürünler için geçerli değil.";
        }
    } else {
        $_SESSION['error_message'] = "Geçersiz kupon kodu.";
    }
} else {
    $_SESSION['error_message'] = "Kupon kodu boş olamaz.";
}

header("Location: customer_cart.php");
exit();
?>