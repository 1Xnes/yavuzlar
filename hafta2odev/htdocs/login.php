<?php 
  session_start();
  if (isset($_SESSION['id']) && isset($_SESSION['username']) ) {
    header("Location: index.php?message=You are already logged in!");
  }

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Giriş Sayfası</title>
  <link rel="stylesheet" href="style.css">

</head>

<body>

  <div class="container">
    <div class="login">
    <h1>Yavuzlar Quiz Sistemi</h1>
    <p>(admin icin admin:admin)</p>
    <p>(ogrenci icin ogrenci:ogrenci)</p>
      <div class="logo">
        <img src="logo.png" alt="image" style="background-color: black; width: 400px;height: auto;">
      </div>
      
      <div class="loginForm">
        <form action="loginQuery.php" method="post">
          <input class="loginInput" type="text" name="username" placeholder="Kullanıcı Adı" required>
          <input class="loginInput" type="password" name="password" placeholder="Şifre" required>
          <button type="submit">Giriş Yap</button>
        </form>
      </div>
    </div>

  </div>

</body>

</html>