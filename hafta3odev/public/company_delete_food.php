<?php
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/includes/functions.php';

requireLogin();
requireRole('company');

$userId = $_SESSION['user_id'];
$companyInfo = getCompanyInfoByUserId($pdo, $userId);

$foodId = $_GET['id'] ?? 0;
$food = getFoodById($pdo, $foodId);

if (!$food || !isCompanyOwnFood($pdo, $companyInfo['id'], $foodId)) {
    $_SESSION['error_message'] = "Geçersiz yemek veya yetkiniz yok.";
    header("Location: company_foods.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = softDeleteFood($pdo, $foodId);
    if ($result) {
        $_SESSION['success_message'] = "Yemek başarıyla silindi.";
    } else {
        $_SESSION['error_message'] = "Yemek silinirken bir hata oluştu.";
    }
    header("Location: company_foods.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yemek Sil</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="bgspecial">
<div class="container">
    <h1>Yemek Sil</h1>
    
    <p>Bu yemeği silmek istediğinizden emin misiniz?</p>
    <p><strong><?= htmlspecialchars($food['name']) ?></strong></p>
    
    <form method="POST">
        <button type="submit" class="btn">Evet, Sil</button>
        <a href="company_foods.php" class="btn">İptal</a>
    </form>
</div>
</div>
</body>
</html><?php include './footer.php'; ?>