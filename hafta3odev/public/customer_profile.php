<?php
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/includes/functions.php';

requireLogin();
requireRole('customer');

$userId = $_SESSION['user_id'];
$user = getUserById($pdo, $userId);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $name = $_POST['name'] ?? '';
        $surname = $_POST['surname'] ?? '';
        $username = $_POST['username'] ?? '';

        if (updateUserProfile($pdo, $userId, $name, $surname, $username)) {
            $_SESSION['success_message'] = "Profil bilgileriniz güncellendi.";
            $user = getUserById($pdo, $userId); // Güncellenmiş bilgileri al
        } else {
            $_SESSION['error_message'] = "Profil güncellenirken bir hata oluştu.";
        }
    } elseif (isset($_POST['add_balance'])) {
        $amount = $_POST['amount'] ?? 0;
        if ($amount > 0) {
            if (addUserBalance($pdo, $userId, $amount)) {
                $_SESSION['success_message'] = "Bakiyeniz başarıyla güncellendi.";
                $user = getUserById($pdo, $userId); // Güncellenmiş bilgileri al
            } else {
                $_SESSION['error_message'] = "Bakiye eklenirken bir hata oluştu.";
            }
        } else {
            $_SESSION['error_message'] = "Geçersiz bakiye miktarı.";
        }
    } elseif (isset($_POST['update_profile_picture'])) {
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
            // Pseudo olarak dosya yükleme işlemini simüle ediyoruz
            $uploadDir = __DIR__ . '/uploads/profile_pictures/';
            $uploadFile = $uploadDir . basename($_FILES['profile_picture']['name']);

            // Pseudo olarak dosya yükleme işlemini simüle ediyoruz
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadFile)) {
                // Pseudo olarak profil resmi güncelleme işlemini simüle ediyoruz
                $_SESSION['success_message'] = "Profil resmi başarıyla güncellendi.";
            } else {
                $_SESSION['error_message'] = "Dosya yüklenirken bir hata oluştu.";
            }
        } else {
            $_SESSION['error_message'] = "Geçersiz dosya.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="bgspecial">
<div class="container">
    <h1>Profil</h1>

    <?php if (isset($_SESSION['success_message'])): ?>
        <p class="success"><?= $_SESSION['success_message'] ?></p>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <p class="error"><?= $_SESSION['error_message'] ?></p>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <h2>Profil Bilgileri</h2>
    <form method="POST">
        <input type="hidden" name="update_profile" value="1">
        <label for="name">Ad:</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
        
        <label for="surname">Soyad:</label>
        <input type="text" id="surname" name="surname" value="<?= htmlspecialchars($user['surname']) ?>" required>
        <br>
        <label for="username">Kullanıcı Adı:</label>
        <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
        <br><br><br>
        <button type="submit" class="btn">Profili Güncelle</button>
    </form>

    <h2>Profil Resmi</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="update_profile_picture" value="1">
        <label for="profile_picture">Profil Resmi:</label>
        <input type="file" id="profile_picture" name="profile_picture" accept="image/*" required>
        <br><br><br>
        <button type="submit" class="btn">Profil Resmini Güncelle</button>
    </form>

    <h2>Şifre Değiştir</h2>
    <p><a href="change_password.php" class="btn">Şifre Değiştir</a></p>

    <h2>Bakiye</h2>
    <p>Mevcut Bakiye: <?= number_format($user['balance'], 2) ?> TL</p>
    <form method="POST">
        <input type="hidden" name="add_balance" value="1">
        <label for="amount">Eklenecek Miktar (TL):</label>
        <input type="number" id="amount" name="amount" min="1" step="0.01" required>
        
        <button type="submit" class="btn">Bakiye Ekle</button>
    </form>

    <p><a href="customer_dashboard.php" class="btn">Ana Sayfaya Dön</a></p>
</div>
</div>
</body>
</html>
<?php include './footer.php'; ?>