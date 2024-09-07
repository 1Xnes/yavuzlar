<?php 
  session_start();
  include "functions/functions.php";
  
  if (!isset($_SESSION['id']) && !isset($_SESSION['username']) ) {
    header("Location: login.php?message=You are not logged in!");
  }

  $listOfUsers = GetUsers();


  //echo "<pre>";
  //print_r($listOfUsers);
  //echo"</pre>";

?>
<!DOCTYPE html>
<html lang="tr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Üyeler</title>

  <link rel="stylesheet" href="style.css">

</head>

<style>


</style>

<body>
  <div class="userList">
    <a href="index.php">
          <div class="navbarButton">
            Anasayfa
          </div>
        </a>  
    
    <table>
      <thead>
        <h1>Kullanıcı Listesi</h1>
        <tr>
          <?php
          if($_SESSION['isAdmin']):?>
            <th>ID</th>
          <?php endif?>
            <th>Adı Soyadı</th>
            <th>Kullanıcı Adı</th>
            <th>Rolü</th>
          <?php if( $_SESSION['isAdmin']):?>
            <th>Admin Mi</th>
            <th colspan="2">İşlemler</th>
          <?php endif?>
        </tr>
      </thead>
      <tbody>
        <?php foreach($listOfUsers as $user):?>
          <tr>
           
            <?php if($_SESSION['isAdmin']):?>
              <td><?php echo $user['id'];?></td>
            <?php endif?>
              <td><?php echo $user['name'] . " " . $user['surname'];?></td>
              <td><?php echo $user['nickname'];?></td>
              <td><?php echo $user['role'];?></td>
            <?php if( $_SESSION['isAdmin']):?>
              <td><?php $a = $user['isAdmin'] ? "Evet" : "Hayir"; echo $a; ?></td>
              <td><a href='deleteUser.php?id=<?php echo $user["id"]?>'>Sil</a></td>
            <?php endif?>
          </tr>
        <?php endforeach?>
        
      </tbody>
      
    </table>
    <?php if( $_SESSION['isAdmin']):?>
    <a href="addUser.php">
          <div class="navbarButton">
            Üye Ekle
          </div>
        </a>  
    <?php endif?>
  </div>

  <script src="script.js"></script>
</body>

</html>
