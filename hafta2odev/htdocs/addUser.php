<?php
  session_start();
  if (!$_SESSION['isAdmin']) {
    header("Location: index.php?message=You are not authorized to view this page!");
    die();
  }
  if (!isset($_SESSION['id']) && !isset($_SESSION['username'])) {
    header("Location: login.php?message=You are not logged in!");
  }
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kullanıcı Ekleme Formu</title>

  <link rel="stylesheet" href="style.css">

  <style>

  </style>

</head>
</head>

<body>
  <div class="container">
    <div class="addUserForm">
      <h2 style="margin-bottom: 20px;">Yeni Üye Ekleme Formu</h2>
      <form action="addUserQuery.php" method="POST" enctype="multipart/form-data">
        <input type="text" id="name" name="name" placeholder="İsim" required><br><br>

        <input type="text" id="surname" name="surname" placeholder="Soyisim" required><br><br>

        <input type="text" id="username" name="username" placeholder="Kullanıcı Adı" required><br><br>

        <input type="password" id="password" name="password" placeholder="Şifre" required><br><br>
       
        <label for="role">Rolü:</label>

        <select name="role" id="role" required>
          <option value="" selected disabled>Rolü Seçiniz</option>
          <option value="admin">admin</option>
          <option value="ogrenci">ogrenci</option>
        </select><br><br>
        <div class="admin-options">
          <label>Admin mi? </label>
          <label for="admin-yes">
            <input type="radio" id="admin-yes" name="isAdmin" value="1" required> Evet
          </label>
          <label for="admin-no">
            <input type="radio" id="admin-no" name="isAdmin" value="0" required> Hayır
          </label>
        </div>

        <button type="submit">Kullanıcı Ekle</button>
        <button style="margin-top: 5px;" id="homePageButton" type="button" onclick="goToHomePage()">Anasayfa</button>
      </form>

    </div>

  </div>
  <script src="script.js"></script>
</body>

</html>
