<?php
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/includes/functions.php';

requireLogin();
requireAdmin();

$userId = $_GET['id'] ?? 0;
$user = getUserById($pdo, $userId);

if (!$user) {
    $_SESSION['error_message'] = "Kullanıcı bulunamadı.";
    header("Location: user_list.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $surname = $_POST['surname'] ?? '';
    $username = $_POST['username'] ?? '';
    $role = $_POST['role'] ?? '';

    if (updateUser($pdo, $userId, $name, $surname, $username, $role)) {
        $_SESSION['success_message'] = "Kullanıcı başarıyla güncellendi.";
        header("Location: user_list.php");
        exit();
    } else {
        $error = "Kullanıcı güncellenirken bir hata oluştu.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kullanıcı Düzenle</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="bgspecial">
<div class="container">
    <h1>Kullanıcı Düzenle</h1>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?= $error ?></p>
    <?php endif; ?>
    <form method="POST">
        <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" placeholder="Ad" required><br>
        <input type="text" name="surname" value="<?= htmlspecialchars($user['surname']) ?>" placeholder="Soyad" required><br>
        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" placeholder="Kullanıcı Adı" required><br>
        <select name="role" required>
            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
            <option value="company" <?= $user['role'] === 'company' ? 'selected' : '' ?>>Şirket</option>
            <option value="customer" <?= $user['role'] === 'customer' ? 'selected' : '' ?>>Müşteri</option>
        </select><br>
        <input type="submit" value="Güncelle" class="btn">
    </form>
    <p><a href="user_list.php" class="btn">Kullanıcı Listesine Dön</a></p>
</div>
</div>
</body>
</html><?php include './footer.php'; ?>