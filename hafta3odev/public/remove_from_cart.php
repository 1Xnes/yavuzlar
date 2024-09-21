<?php
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/includes/functions.php';

requireLogin();
requireRole('customer');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $basketId = $_POST['basket_id'] ?? 0;

    if ($basketId) {
        if (removeFromCart($pdo, $basketId)) {
            $_SESSION['success_message'] = "Ürün sepetten kaldırıldı.";
        } else {
            $_SESSION['error_message'] = "Ürün sepetten kaldırılırken bir hata oluştu.";
        }
    } else {
        $_SESSION['error_message'] = "Geçersiz sepet öğesi.";
    }
}

header("Location: customer_cart.php");
exit();