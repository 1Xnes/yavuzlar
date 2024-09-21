<?php
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/includes/functions.php';

requireLogin();
requireRole('customer');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $basketId = $_POST['basket_id'] ?? 0;
    $quantity = $_POST['quantity'] ?? 1;

    if ($basketId && $quantity > 0) {
        if (updateCartItemQuantity($pdo, $basketId, $quantity)) {
            $_SESSION['success_message'] = "Sepet güncellendi.";
        } else {
            $_SESSION['error_message'] = "Sepet güncellenirken bir hata oluştu.";
        }
    } else {
        $_SESSION['error_message'] = "Geçersiz sepet öğesi veya miktar.";
    }
}

header("Location: customer_cart.php");
exit();