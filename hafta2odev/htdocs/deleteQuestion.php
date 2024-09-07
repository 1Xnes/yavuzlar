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

    $questionId = $_GET["id"];

    DeleteQuestion($questionId);
    header("Location: listQuestion.php?message=Soru başarıyla silindi!");
    exit();
?>