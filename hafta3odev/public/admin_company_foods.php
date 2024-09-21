<?php
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/includes/functions.php';

requireLogin();
requireAdmin();

$companyId = $_GET['id'] ?? 0;
$company = getCompanyDetailsAdmin($pdo, $companyId);

if (!$company) {
    $_SESSION['error_message'] = "Firma bulunamadı.";
    header("Location: company_list.php");
    exit();
}

$foods = getFoodsByCompanyIdAdmin($pdo, $companyId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($company['name']) ?> - Yemekler</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="bgspecial">
<div class="container">
    <h1><?= htmlspecialchars($company['name']) ?> - Yemekler</h1>

    <?php if (isset($_SESSION['success_message'])): ?>
        <p class="success"><?= htmlspecialchars($_SESSION['success_message']) ?></p>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <p class="error"><?= htmlspecialchars($_SESSION['error_message']) ?></p>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <?php if (empty($foods)): ?>
        <p>Bu firmada henüz yemek eklenmemiş.</p>
    <?php else: ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Yemek Adı</th>
                <th>Açıklama</th>
                <th>Fiyat</th>
                <th>İndirim</th>
            </tr>
            <?php foreach ($foods as $food): ?>
            <tr>
                <td><?= htmlspecialchars($food['id']) ?></td>
                <td><?= htmlspecialchars($food['name']) ?></td>
                <td><?= htmlspecialchars($food['description']) ?></td>
                <td><?= number_format($food['price'], 2) ?> TL</td>
                <td><?= number_format($food['discount'], 2) ?> %</td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <p><a href="company_list.php" class="btn">Firma Listesine Dön</a></p>
</div>
</div>
</body>
</html>

<?php include './footer.php'; ?>