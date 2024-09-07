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

  if (isset($_GET['id'])) {
    $id = $_GET['id'];
    } 
  
  if (!$id) {
    header("Location: listQuestion.php?message=No question ID provided!");
    exit;
    }


  $question = GetQuestionById($id);
  
  if (!$question) {
    header("Location: listQuestion.php?message=Question not found!");
    exit;
    }


?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Soru Düzenle</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="navbarContainer">
    <h1>Soru Düzenle</h1>
    
    <form id="editQuestionForm" action="editQuestionQuery.php" method="POST">
      <input type="hidden" name="id" value="<?php echo $question['indeks']; ?>">
      
      <label for="question">Soru:</label><br>
      <input type="text" id="question" name="question" class="button2" value="<?php echo ($question['question']); ?>" required><br>
      
      <input type="text" id="answer1" name="answer1" class="button2" value="<?php echo ($question['answer1']); ?>" required>
      <input type="text" id="answer2" name="answer2" class="button2" value="<?php echo ($question['answer2']); ?>" required>
      <input type="text" id="answer3" name="answer3" class="button2" value="<?php echo ($question['answer3']); ?>" required>
      <input type="text" id="answer4" name="answer4" class="button2" value="<?php echo ($question['answer4']); ?>" required>
      
      <label for="rightAnswer">Doğru Cevap:</label><br>
      <input type="number" id="rightAnswer" name="ranswer" class="button2" value="<?php echo $question['ranswer']; ?>" min="1" max="4" required><br><br>
      
      <label for="difficulty">Zorluk:</label><br>
      <select name="difficulty" class="button2" id="difficulty" required>
        <option value="easy" <?php echo $question['difficulty'] == 'easy' ? 'selected' : ''; ?>>Kolay</option>
        <option value="normal" <?php echo $question['difficulty'] == 'normal' ? 'selected' : ''; ?>>Normal</option>
        <option value="hard" <?php echo $question['difficulty'] == 'hard' ? 'selected' : ''; ?>>Zor</option>
      </select><br><br>
      
      <button type="submit" class="button2">Soruyu Kaydet</button>
    </form>
    <br><br>
    <a href="listQuestion.php" class="questionListButton">Geri Dön</a>
    <a href="index.php" class="questionListButton">Ana Sayfaya Git</a>
    <br>
  </div>
</body>
</html>