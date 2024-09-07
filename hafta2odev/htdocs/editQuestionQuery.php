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

  if (isset($_POST['id']) && isset($_POST['question']) && isset($_POST['answer1']) && isset($_POST['answer2']) && isset($_POST['answer3']) && isset($_POST['answer4']) && isset($_POST['ranswer']) && isset($_POST['difficulty'])) {
    $id = (int) $_POST['id'];
    $question = htmlclean($_POST['question']);
    $answer1 = htmlclean($_POST['answer1']);
    $answer2 = htmlclean($_POST['answer2']);
    $answer3 = htmlclean($_POST['answer3']);
    $answer4 = htmlclean($_POST['answer4']);
    $ranswer = (int) $_POST['ranswer'];
    $difficulty = htmlclean($_POST['difficulty']);
  
    if ($ranswer < 1 || $ranswer > 4) {
      header("Location: editQuestion.php?id=$id&message=Lütfen 1 ile 4 arasında bir doğru cevap numarası girin!");
      exit;
    }
  
    UpdateQuestion($id, $question, $answer1, $answer2, $answer3, $answer4, $ranswer, $difficulty);
  
    header("Location: listQuestion.php?message=Soru başarıyla düzenlendi!");
  } else {
    header("Location: listQuestion.php?message=boş alan bırakma");
  }
?>