<?php
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/includes/functions.php';

requireLogin();
requireAdmin();

$companyId = $_GET['id'] ?? 0;
$company = getCompanyDetails($pdo, $companyId);

if (!$company) {
    $_SESSION['error_message'] = "Firma bulunamadı.";
    header("Location: company_list.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $logo = $_FILES['logo'] ?? null;

    if (updateCompany($pdo, $companyId, $name, $description, $logo)) {
        $_SESSION['success_message'] = "Firma başarıyla güncellendi.";
        header("Location: company_list.php");
        exit();
    } else {
        $error = "Firma güncellenirken bir hata oluştu.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Firma Düzenle</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="bgspecial">
<div class="container">
    <h1>Firma Düzenle</h1>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?= $error ?></p>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="name" value="<?= htmlspecialchars($company['name']) ?>" placeholder="Firma Adı" required><br>
        <textarea name="description" placeholder="Firma Açıklaması" required><?= htmlspecialchars($company['description']) ?></textarea><br>
        <input type="file" name="logo" accept="image/*"><br>
        <?php if ($company['logo_path']): ?>
            <img src="<?= htmlspecialchars($company['logo_path']) ?>" alt="Mevcut Logo" style="max-width: 200px;"><br>
        <?php endif; ?>
        <input type="submit" value="Güncelle" class="btn">
    </form>
    <p><a href="company_list.php" class="btn">Firma Listesine Dön</a></p>
</div>
</div>
</body>
</html><?php include './footer.php'; ?>