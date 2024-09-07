<?php
session_start();
include "functions/functions.php";

if (!$_SESSION['isAdmin']) {
  header("Location: index.php?message=You are not authorized to view this page!");
  die();
}
if (!isset($_SESSION['id']) && !isset($_SESSION['username'])) {
  header("Location: login.php?message=You are not logged in!");
  die();
}

if (isset($_POST['name']) && isset($_POST['surname']) && isset($_POST['username']) &&  isset($_POST['password']) && isset($_POST['role']) && isset($_POST['isAdmin']) ) {
  $name = htmlclean($_POST['name']);
  $surname = htmlclean($_POST['surname']);
  $username = htmlclean($_POST['username']);
  $password = htmlclean($_POST['password']);
  $role = htmlclean($_POST['role']);
  $isAdmin = htmlclean($_POST['isAdmin']);
  
  
  AddUser($name, $surname, $username, $password, $isAdmin, $role);
    
  if ($isAdmin==0) {
    $isAdmin="Hayır";
  }
  else if ($isAdmin==1) {
    $isAdmin="Evet";
  }

  echo '<!DOCTYPE html>
  <html lang="tr">
  <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Profile</title>
      <link rel="stylesheet" href="style.css">
  </head>
  <body>

    <div class="profileContainer">
        <div class="profileBar">';
  echo "
            <p><i><b>İsim Soyisim: </b></i> $name $surname</p>
            <p><i><b>Kullanıcı Adı: </b>$username</p>
            <p><i><b>Rolü: </b>$role</p>
            <p><b>Admin mi? : $isAdmin</b></p>";
  echo '  </div>
        <button style="width: 200px;" id="homePageButton" onclick="goToHomePage()">Anasayfa</button>
    </div>

    <script src="script.js"></script>
    
</body>
</html>';
} else {
  header("Location: addUser.php?message=You must fill all the fields!");
}
