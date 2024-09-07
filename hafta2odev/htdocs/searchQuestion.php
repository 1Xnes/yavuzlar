<?php
  session_start();
  include "functions/functions.php";
  if (!$_SESSION['isAdmin']) {
    header("Location: index.php?message=You are not authorized to view this page!");
    die();
  }
  if (!isset($_SESSION['id']) && !isset($_SESSION['username'])) {
    header("Location: login.php?message=You are not logged in!");
  }
?>

<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Soru Ara</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="navbarContainer">
    <h1>Soru Ara</h1>
    
    <form id="searchQuestionForm" action="searchQuestionQuery.php" method="GET">
      <input type="text" id="searchKeyword" name="keyword" class="button2" placeholder="Aranacak kelimeyi girin" required>
      <button type="submit" class="button2">Ara</button>
    </form>
    
    <br><br>
    <a href="listQuestion.php" class="questionListButton">Soru Listesine Dön</a>
    <a href="index.php" class="questionListButton">Ana Sayfaya Git</a>
  </div>
</body>
</html>