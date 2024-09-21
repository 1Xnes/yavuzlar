<?php
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/includes/functions.php';

requireLogin();
requireAdmin();

$companyId = $_GET['id'] ?? 0;

if ($companyId) {
    if (deleteCompany($pdo, $companyId)) {
        $_SESSION['success_message'] = "Firma başarıyla silindi.";
    } else {
        $_SESSION['error_message'] = "Firma silinirken bir hata oluştu.";
    }
}

header("Location: company_list.php");
exit();