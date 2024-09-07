<?php 
  session_start();
  include "functions/functions.php";
  
  if (!isset($_SESSION['id']) && !isset($_SESSION['username']) ) {
    header("Location: login.php?message=You are not logged in!");
  }

  $scoreboard= getScoreboard();

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
        <h1>Skor Listesi</h1>
        <tr>
          <?php
          if($_SESSION['isAdmin']):?>
            <th>ID</th>
          <?php endif?>
            <th>Kullanıcı Id'si</th>
            <th>Kullanıcı adı</th>
            <th>Puanı</th>
            
        </tr>
      </thead>
      <tbody>
        <?php foreach($scoreboard as $listper):?>
          <tr>
           
            <?php if($_SESSION['isAdmin']):?>
              <td><?php echo $listper['id'];?></td>
            <?php endif?>
              <td><?php echo $listper['userId'];?></td>
              <td><?php echo $listper['nickname'];?></td>
              <td><?php echo $listper['score'];?></td>
              
            </tr>
        <?php endforeach?>
        
      </tbody>
      
    </table>
  </div>

  <script src="script.js"></script>
</body>

</html>
