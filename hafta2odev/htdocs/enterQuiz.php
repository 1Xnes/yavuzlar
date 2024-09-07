<?php
session_start();
include "functions/functions.php";

if (!isset($_SESSION['id']) && !isset($_SESSION['username'])) {
  header("Location: login.php?message=You are not logged in!");
} 

?>


<!DOCTYPE html>
<html lang="tr">

<html>
    <head>
        <meta charset="UTF-8">
        <title>Quiz Uygulaması</title>
        <link rel="stylesheet" href="style.css">
      
      </head>
    <body>
        <div class="quizContainer">
            
            <h1>Basit Quiz Sayfası</h1>
            <h2 id="question">Soru puanları:</h2>
            <h2 id="question">Kolay:1<br>Normal:2<br>Zor:3</h2>
            <br><br><br>
            
              <a href="quizNormal.php" class="quizButton">Quize Gir</a>
              <br><br><br><br>
              <a href="index.php" class="quizButton">Ana Sayfaya Git.</a>


            </div>
        </div>
    </body>
</html>