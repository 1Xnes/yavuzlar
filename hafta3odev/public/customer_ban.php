<?php
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/includes/functions.php';

requireLogin();
requireAdmin();

$customerId = $_GET['id'] ?? 0;

if ($customerId && banCustomer($pdo, $customerId)) {
    $_SESSION['success_message'] = "Müşteri başarıyla banlandı.";
} else {
    $_SESSION['error_message'] = "Müşteri banlanırken bir hata oluştu.";
}

header("Location: customer_list.php");
exit();