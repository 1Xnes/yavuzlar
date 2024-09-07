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

  if (isset($_GET['keyword'])) {
    $keyword = htmlclean($_GET['keyword']);
    } 
  else {
    $keyword = '';
    }
  $searchResults = SearchQuestions($keyword);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Arama Sonuçları</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="questListContainerd">
    <h1>Arama Sonuçları: "<?php echo $keyword; ?>"</h1>
    <div class="questList">
      <table>
        <thead>
          <tr>
            <th>Kaçıncı soru</th>
            <th>Soru</th>
            <th>Cevap 1</th>
            <th>Cevap 2</th>
            <th>Cevap 3</th>
            <th>Cevap 4</th>
            <th>Doğru Cevap</th>
            <th>Zorluk</th>
            <th>Soruyu Düzenle</th>
            <th>Soruyu Sil</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($searchResults as $question): ?>
            <tr>
              <td><?php echo $question['indeks'];?></td>
              <td><?php echo $question['question'];?></td>
              <td><?php echo $question['answer1'];?></td>
              <td><?php echo $question['answer2'];?></td>
              <td><?php echo $question['answer3'];?></td>
              <td><?php echo $question['answer4'];?></td>
              <td><?php echo $question['ranswer'];?></td>
              <td><?php echo $question['difficulty'];?></td>
              <td><a href='editQuestion.php?id=<?php echo $question["indeks"]?>'>Düzenle</a></td>
              <td><a href='deleteQuestion.php?id=<?php echo $question["indeks"]?>'>Sil</a></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <br><br>
    <a href="searchQuestion.php" class="questionListButton">Yeni Arama</a>
    <a href="listQuestion.php" class="questionListButton">Soru Listesine Dön</a>
    <a href="index.php" class="questionListButton">Ana Sayfaya Git</a>
  </div>
</body>
</html>