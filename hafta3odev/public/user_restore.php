<?php
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/includes/functions.php';

requireLogin();
requireAdmin();

$userId = $_GET['id'] ?? 0;

if (restoreUser($pdo, $userId)) {
    $_SESSION['success_message'] = "Kullanıcı başarıyla geri yüklendi.";
} else {
    $_SESSION['error_message'] = "Kullanıcı geri yüklenirken bir hata oluştu.";
}

header("Location: user_list.php");
exit();

function restoreUser($pdo, $userId) {
    $sql = "UPDATE users SET deleted_at = NULL WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$userId]);
}
?>