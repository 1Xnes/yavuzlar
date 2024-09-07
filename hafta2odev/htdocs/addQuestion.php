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
?><!DOCTYPE html>
<html lang="tr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Soru Ekle</title>
  
  <link rel="stylesheet" href="style.css">

</head>

<body>

  <div class="navbarContainer">
    <h1>Yeni Soru Ekle</h1>
    
    <form id="addQuestionForm" action="addQuestionQuery.php" method="POST">
      <label for="question">Soru:</label><br>
      <input type="text" id="question" name="question" class="button2" placeholder="Soruyu girin" required><br>
      
      <input type="text" id="answer1" name="answer1" class="button2" placeholder="Cevap 1'i girin" required>
      
      <input type="text" id="answer2" name="answer2" class="button2" placeholder="Cevap 2'yi girin" required>
      <input type="text" id="answer3" name="answer3" class="button2" placeholder="Cevap 3'ü girin" required>
     
      <input type="text" id="answer4" name="answer4" class="button2" placeholder="Cevap 4'ü girin" required>
      
      <label for="rightAnswer">Doğru Cevap:</label><br>
      <input type="number" id="rightAnswer" name="ranswer" class="button2" placeholder="Doğru cevap numarasını girin" min="1" max="4" required><br><br>
      
      <label for="difficulty">Zorluk:</label><br>
      <select name="difficulty" class="button2" id="difficulty" required>
        <option value="" selected disabled>Zorluğu Seçiniz</option>
        <option value="easy">Kolay</option>
        <option value="normal">Normal</option>
        <option value="hard">Zor</option>
      </select><br><br>
      
      <button type="submit" class="button2">Soruyu Ekle</button>
    </form>
    <br><br>
    <a href="questionList.html" class="questionListButton">Geri Dön</a>
    <a href="index.php" class="questionListButton">Ana Sayfaya Git.</a>
    <br>
  </div>

</body>
</html>