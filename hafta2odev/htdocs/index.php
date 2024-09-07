<?php
session_start();


if (!isset($_SESSION['id']) && !isset($_SESSION['username'])) {
  header("Location: login.php?message=You are not logged in!");
} else {
?>
  <!DOCTYPE html>
  <html lang="tr">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anasayfa</title>
    <link rel="stylesheet" href="style.css">
  </head>

  <body>

    <div class="navbarContainer">
      <form action="logout.php" method="post">
        <button class="logout" id="logoutButton">Çıkış Yap</button>
      </form>
      
      <div class="header">
        <h1>Yavuzlar Quiz Platformu</h1>
      </div>
      <div class="navbar">
        
        <?php if (!$_SESSION['isAdmin']):?>        
        <a href="enterQuiz.php">
          <div class="navbarButton">
            Quize Gir
          </div>
        </a>
        <?php endif?>

        <a href="userList.php">
          <div class="navbarButton">
            Üyeler
          </div>
        </a>
        <a href="scoreboard.php">
          <div class="navbarButton">
            Skor Tablosu
          </div>
        </a>
        <?php if ($_SESSION['isAdmin']):?>
         
        <a href="listQuestion.php">
          <div class="navbarButton">
            Soruları Düzenle
          </div>
        </a>
        
        
        <?php endif?>
      </div>
    </div>




    </div>
    </div>

    <script src="script.js"></script>

  </body>

  </html>
<?php } ?>