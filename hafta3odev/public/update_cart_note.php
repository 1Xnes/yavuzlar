<?php
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/includes/functions.php';

requireLogin();
requireRole('customer');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $basketId = $_POST['basket_id'] ?? 0;
    $note = $_POST['note'] ?? '';

    if ($basketId) {
        if (updateCartItemNote($pdo, $basketId, $note)) {
            $_SESSION['success_message'] = "Not güncellendi.";
        } else {
            $_SESSION['error_message'] = "Not güncellenirken bir hata oluştu.";
        }
    } else {
        $_SESSION['error_message'] = "Geçersiz sepet öğesi.";
    }
}

header("Location: customer_cart.php");
exit();