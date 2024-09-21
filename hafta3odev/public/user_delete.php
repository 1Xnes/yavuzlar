<?php
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/includes/functions.php';

requireLogin();
requireAdmin();

$userId = $_GET['id'] ?? 0;

if ($userId) {
    if (deleteUser($pdo, $userId)) {
        $_SESSION['success_message'] = "Kullanıcı başarıyla silindi.";
    } else {
        $_SESSION['error_message'] = "Kullanıcı silinirken bir hata oluştu.";
    }
}

header("Location: user_list.php");
exit();