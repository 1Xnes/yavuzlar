<?php
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/includes/functions.php';

requireLogin();
requireRole('admin'); // Yorumları sadece admin silmelidir

$commentId = $_GET['id'] ?? 0;

if (deleteComment($pdo, $commentId)) {
    $_SESSION['success_message'] = "Yorum başarıyla silindi.";
} else {
    $_SESSION['error_message'] = "Yorum silinirken bir hata oluştu.";
}

header("Location: comment_list.php");
exit();
?>