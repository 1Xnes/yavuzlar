<?php
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/includes/functions.php';

requireLogin();
requireRole('company');

$couponId = $_GET['id'] ?? 0;

if (deleteCoupon($pdo, $couponId)) {
    $_SESSION['success_message'] = "Kupon başarıyla silindi.";
} else {
    $_SESSION['error_message'] = "Kupon silinirken bir hata oluştu.";
}

header("Location: company_coupons.php");
exit();
?>