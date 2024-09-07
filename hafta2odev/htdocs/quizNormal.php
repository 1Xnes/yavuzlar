<?php
session_start();
include "functions/functions.php";

if (!isset($_SESSION['id']) && !isset($_SESSION['username'])) {
    header("Location: login.php?message=You are not logged in!");
    exit();
    }

$id = $_SESSION['id'];

if (isset($_GET['reset'])) {
    
    unset($_SESSION['questions']);
    unset($_SESSION['currentQIndex']);
    unset($_SESSION['score']);
    unset($_SESSION['quiz_started']);
    
    header("Location: index.php");
    
    exit();
    }

$listOfQuestions = GetQuestions();
if ($listOfQuestions == array()) {
        echo "<link rel='stylesheet' href='style.css'>";
        echo "<div class='navbarContainer'>";
        echo "<h1>Veritabanında soru bulunmamaktadır!</h1>";
        echo "<br> <h2>Yakında soru eklenecektir.</h2>";
        echo "<a href='index.php' class='questionListButton'>Ana Sayfaya Dön</a>";
        echo "</div>";
        exit();
        }



if (!isset($_SESSION['quiz_started'])) {
    $_SESSION['questions'] = getStudentSubmissionsNot($id);
    if (empty($_SESSION['questions'])) {
        echo '<link rel="stylesheet" href="style.css">';
        echo '<div class="navbarContainer"><h1>Quiz Uygulaması</h1>';
        echo "<p>Tüm soruları çözdünüz! Tebrikler!</p><br>";
        echo "<a href='index.php' class='questionListButton'>Ana Sayfaya Dön</a>";
        echo '</div>';
        exit();
        }
    $_SESSION['currentQIndex'] = 0;
    $_SESSION['score'] = 0;
    $_SESSION['quiz_started'] = true;
    }

if (isset($_POST['answer'])) {
    $currentQuestion = $_SESSION['questions'][$_SESSION['currentQIndex']];
    $isCorrect = ($_POST['answer'] == $currentQuestion['ranswer']) ? 1 : 0;

    if ($isCorrect) {
        $_SESSION['score'] += getDifficultyScore($currentQuestion['difficulty']);
        }

    updateScore($id, $currentQuestion['indeks'], $isCorrect, $currentQuestion['difficulty']);
    $_SESSION['currentQIndex']++;

    }


if (isset($_POST['skip'])) {
    $currentQuestion = $_SESSION['questions'][$_SESSION['currentQIndex']];
    updateScore($id, $currentQuestion['indeks'], 0, $currentQuestion['difficulty']);
    $_SESSION['currentQIndex']++;
    }

if ($_SESSION['currentQIndex'] >= count($_SESSION['questions'])) {
    
    echo '<link rel="stylesheet" href="style.css">';
    echo '<div class="navbarContainer"><h1>Quiz Uygulaması</h1>';
    echo "Quiz tamamlandı. Toplam puanınız: " . $_SESSION['score'];
    echo "<br><a href='quizNormal.php?reset=1' class='questionListButton'>Ana Sayfaya Dön</a>";
    echo '</div>';

    unset($_SESSION['questions']);
    unset($_SESSION['currentQIndex']);
    unset($_SESSION['score']);
    unset($_SESSION['quiz_started']);

    exit();
    }

$currentQuestion = $_SESSION['questions'][$_SESSION['currentQIndex']];



?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Quiz Uygulaması</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="navbarContainer">
    <h1>Quiz Uygulaması</h1>
    <div class="quiz">

        <h2>Soru <?php echo $_SESSION['currentQIndex'] + 1; ?>: <?php echo $currentQuestion['question']; ?></h2>
        
        <form method="post">
            
            <button type="submit" name="answer" value="1"><?php echo $currentQuestion['answer1']; ?></button>
            <button type="submit" name="answer" value="2"><?php echo $currentQuestion['answer2']; ?></button>
            <button type="submit" name="answer" value="3"><?php echo $currentQuestion['answer3']; ?></button>
            <button type="submit" name="answer" value="4"><?php echo $currentQuestion['answer4']; ?></button>
            <br><br>
            <button type="submit" name="skip" value="1">Soruyu Atla</button>
        </form>


        <p>Zorluk: <?php echo $currentQuestion['difficulty']; ?></p>
        <p>Mevcut Puan: <?php echo $_SESSION['score']; ?></p>
    
    
    </div>
    
    <br><br><br>
    
    <a href='quizNormal.php?reset=1' class='questionListButton'>Ana Sayfaya Git</a>
</div>
</body>
</html>