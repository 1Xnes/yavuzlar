<?php
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/includes/functions.php';

requireLogin();
requireRole('company');

$userId = $_SESSION['user_id'];
$companyInfo = getCompanyInfoByUserId($pdo, $userId);

// Arama parametrelerini al
$search = $_GET['search'] ?? '';
$minPrice = $_GET['min_price'] ?? '';
$maxPrice = $_GET['max_price'] ?? '';

// Firma restoranlarını al
$restaurants = getRestaurantsByCompanyId($pdo, $companyInfo['id']);

// Seçili restoran
$selectedRestaurantId = $_GET['restaurant_id'] ?? '';

// Yemekleri al
$foods = getFoodsByCompanyIdWithDeleted($pdo, $companyInfo['id'], $selectedRestaurantId, $search, $minPrice, $maxPrice);
// Restoran adlarını bir diziye alalım
$restaurantNames = [];
foreach ($restaurants as $restaurant) {
    $restaurantNames[$restaurant['id']] = $restaurant['name'];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yemek Yönetimi</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="bgspecial">
<div class="container">
    <h1>Yemek Yönetimi</h1>

    <form method="GET" action="">
    <select name="restaurant_id">
        <option value="">Tüm Restoranlar</option>
        <?php foreach ($restaurants as $restaurant): ?>
            <option value="<?= $restaurant['id'] ?>" <?= $restaurant['id'] == $selectedRestaurantId ? 'selected' : '' ?>>
                <?= htmlspecialchars($restaurant['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>
        <input type="text" name="search" placeholder="Yemek Adı" value="<?= htmlspecialchars($search) ?>">
        <input type="number" name="min_price" placeholder="Min Fiyat" value="<?= htmlspecialchars($minPrice) ?>">
        <input type="number" name="max_price" placeholder="Max Fiyat" value="<?= htmlspecialchars($maxPrice) ?>">
        <button type="submit" class="btn">Ara</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Yemek Adı</th>
                <th>Restoran</th>
                <th>Açıklama</th>
                <th>Fiyat</th>
                <th>İndirim</th>
                <th>Durum</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($foods as $food): ?>
                <tr>
                    <td><?= htmlspecialchars($food['name']) ?></td>
                    <td><?= htmlspecialchars($restaurantNames[$food['restaurant_id']] ?? 'Bilinmeyen Restoran') ?></td>
                    <td><?= htmlspecialchars($food['description']) ?></td>
                    <td><?= number_format($food['price'], 2) ?> TL</td>
                    <td><?= number_format($food['discount'], 2) ?>%</td>
                    <td><?= $food['deleted_at'] ? 'Silinmiş' : 'Aktif' ?></td>
                    <td>
                        <?php if (!$food['deleted_at']): ?>
                            <a href="company_edit_food.php?id=<?= $food['id'] ?>" class="btn">Düzenle</a><br><br>
                            <a href="company_delete_food.php?id=<?= $food['id'] ?>" class="btn" onclick="return confirm('Bu yemeği silmek istediğinizden emin misiniz?')">Sil</a>
                         <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <p><a href="company_add_food.php" class="btn">Yeni Yemek Ekle</a></p>
    <p><a href="company_dashboard.php" class="btn">Firma Paneline Dön</a></p>
</div>
</div>
</body>
</html><?php include './footer.php'; ?>