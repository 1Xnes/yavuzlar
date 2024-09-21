<?php
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/includes/functions.php';

requireLogin();
requireAdmin();

$couponId = $_GET['id'] ?? 0;

if ($couponId) {
    if (deleteCoupon($pdo, $couponId)) {
        $_SESSION['success_message'] = "Kupon başarıyla silindi.";
    } else {
        $_SESSION['error_message'] = "Kupon silinirken bir hata oluştu.";
    }
}

header("Location: coupon_list.php");
exit();