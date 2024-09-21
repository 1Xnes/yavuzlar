<?php
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/includes/functions.php';

requireLogin();
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $surname = $_POST['surname'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';
    
    if ($role === 'company') {
        $companyName = $_POST['company_name'] ?? '';
        $companyDescription = $_POST['company_description'] ?? '';
        $companyLogo = $_FILES['company_logo'] ?? null;
        
        $result = addCompanyUser($pdo, $name, $surname, $username, $password, $companyName, $companyDescription, $companyLogo);
    } else {
        $result = addUser($pdo, $name, $surname, $username, $password, $role);
    }
    
    if ($result) {
        header("Location: user_list.php");
        exit();
    } else {
        $error = "Kullanıcı eklenirken bir hata oluştu.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kullanıcı Ekle</title>
    <link rel="stylesheet" href="/css/style.css">
    <script>
        function toggleCompanyFields() {
            var role = document.getElementById('role').value;
            var companyFields = document.getElementById('companyFields');
            companyFields.style.display = (role === 'company') ? 'block' : 'none';
        }
    </script>
</head>
<body>
<div class="container">
    <h1>Kullanıcı Ekle</h1>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?= $error ?></p>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="Ad" required><br>
        <input type="text" name="surname" placeholder="Soyad" required><br>
        <input type="text" name="username" placeholder="Kullanıcı Adı" required><br>
        <input type="password" name="password" placeholder="Şifre" required><br>
        <select name="role" id="role" onchange="toggleCompanyFields()" required>
            <option value="">Rol Seçin</option>
            <option value="admin">Admin</option>
            <option value="company">Şirket</option>
            <option value="customer">Müşteri</option>
        </select><br>
        <div id="companyFields" style="display: none;">
            <input type="text" name="company_name" placeholder="Şirket Adı"><br>
            <textarea name="company_description" placeholder="Şirket Açıklaması"></textarea><br>
            <input type="file" name="company_logo" accept="image/*"><br>
        </div>
        <input type="submit" value="Kullanıcı Ekle" class="btn">
    </form>
    <p><a href="admin.php" class="btn">Admin Paneline Dön</a></p>
</div>
</body>
</html><?php include './footer.php'; ?>