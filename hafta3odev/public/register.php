<?php
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/includes/functions.php';

if (isLoggedIn()) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $surname = $_POST['surname'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (registerUser($pdo, $name, $surname, $username, $password)) {
        header("Location: login.php");
        exit();
    } else {
        $error = "Error registering user.";
    }
}
?>

<!DOCTYPE html>
<div class="bgspecial">
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="container">
    <h1>Kaydol</h1>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?= $error ?></p>
    <?php endif; ?>
    <form method="POST">
        <input type="text" name="name" placeholder="Ad" required><br>
        <input type="text" name="surname" placeholder="Soyad" required><br>
        <input type="text" name="username" placeholder="Kullanıcı adı" required><br>
        <input type="password" name="password" placeholder="Şifre" required><br>
        <input type="submit" value="Kaydol" class="btn">
    </form>
    <br><br><br><br>
    <p><a href="login.php" class="btn">Giriş yap</a></p>
    <p><a href="index.php" class="btn">Ana Sayfa</a></p>
    </div>
</body></div>
</html><?php include './footer.php'; ?>