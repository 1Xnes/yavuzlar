<?php
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/includes/functions.php';

requireLogin();
requireRole('customer');

unset($_SESSION['applied_coupon']);
$_SESSION['success_message'] = "Kupon başarıyla kaldırıldı.";

header("Location: customer_cart.php");
exit();
?>