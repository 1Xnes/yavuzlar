<?php
session_start();
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $user = loginUser($pdo, $username, $password);
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_name'] = $user['name'];
        
        // Kullanıcı rolüne göre yönlendirme
        if ($user['role'] === 'admin') {
            header("Location: admin.php");
        } elseif ($user['role'] === 'company') {
            header("Location: company_dashboard.php");
        } else {
            header("Location: index.php");
        }
        exit();
    } else {
        $error = "Geçersiz kullanıcı adı veya şifre, ya da hesabınız silinmiş olabilir.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="bgspecial">
<div class="container">
    <h1>Giriş Yap</h1>
    <?php if (isset($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="POST">
        <input type="text" name="username" placeholder="Kullanıcı Adı" required><br>
        <input type="password" name="password" placeholder="Şifre" required><br>
        <input type="submit" value="Giriş Yap" class="btn">
    </form>
    <p><a href="index.php" class="btn">Ana Sayfa</a></p>
</div>
</div>
</body>
</html><?php include './footer.php'; ?>