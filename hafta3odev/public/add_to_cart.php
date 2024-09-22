<?php
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/includes/functions.php';

requireLogin();
requireRole('customer');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $foodId = $_POST['food_id'] ?? 0;
    $quantity = $_POST['quantity'] ?? 1;
    $note = $_POST['note'] ?? '';

    if ($foodId && $quantity > 0) {
        $userId = $_SESSION['user_id'];
        $note = $_POST['note'] ?? ''; // Not alanını ekleyin
        
        // Sepette başka bir restorandan yemek var mı kontrol et
        if (isCartFromDifferentRestaurant($pdo, $userId, $foodId)) {
            $_SESSION['error_message'] = "Sepetinizde başka bir restorandan yemek var. Lütfen önce sepetinizi boşaltın.";
        } else {
            if (addToCart($pdo, $userId, $foodId, $quantity, $note)) {
                $_SESSION['success_message'] = "Ürün sepete eklendi.";
            } else {
                $_SESSION['error_message'] = "Ürün sepete eklenirken bir hata oluştu.";
            }
        }
    } else {
        $_SESSION['error_message'] = "Geçersiz ürün veya miktar.";
    }
}

// Kullanıcıyı geri yönlendir
header("Location: " . $_SERVER['HTTP_REFERER']);
exit();