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


  if (isset($_POST['question']) && isset($_POST['answer1']) && isset($_POST['answer2']) && isset($_POST['answer3']) && isset($_POST['answer4']) && isset($_POST['ranswer']) && isset($_POST['difficulty'])) {
    $question = htmlclean($_POST['question']);
    $answer1 = htmlclean($_POST['answer1']);
    $answer2 = htmlclean($_POST['answer2']);
    $answer3 = htmlclean($_POST['answer3']);
    $answer4 = htmlclean($_POST['answer4']);
    $ranswer = (int) $_POST['ranswer'];
    $difficulty = htmlclean($_POST['difficulty']);
  
    if ($ranswer < 1 || $ranswer > 4) {
      header("Location: addQuestion.php?message=Lütfen 1 ile 4 arasında bir doğru cevap numarası girin!");
      exit;
    }
  
    AddQuestion($question, $answer1, $answer2, $answer3, $answer4, $ranswer, $difficulty);
  
    echo '<!DOCTYPE html>
    <html lang="tr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Soru Eklendi</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
      <div class="profileContainer">
          <div class="profileBar">';
    echo "
              <p><i><b>Soru: </b></i> $question </p>
              <p><i><b>Cevap 1: </b>$answer1 </p>
              <p><i><b>Cevap 2: </b>$answer2 </p>
              <p><i><b>Cevap 3: </b>$answer3 </p>
              <p><i><b>Cevap 4: </b>$answer4 </p>
              <p><i><b>Doğru Cevap: </b>$ranswer </p>
              <p><i><b>Zorluk Seviyesi: </b>$difficulty </p>
              ";
    echo '  </div>
          <button style="width: 200px;" id="homePageButton" onclick="goToHomePage()">Anasayfa</button>
      </div>
      <script src="script.js"></script>
  </body>
  </html>';
  } else {
    header("Location: addQuestion.php?message=Tüm alanları doldurmalısınız!");
  }
?>