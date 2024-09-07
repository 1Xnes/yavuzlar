<!-- 
bu kodu yazarken kodların ne işe yaradığını bir ben bir Allah biliyordu.
Şimdi sadece Allah biliyor.  
Wasted Hours:23
-->
<?php

function Login($nickname, $passwd) {
    include "db.php";
    
    $query = "SELECT *, COUNT(*) as count FROM users WHERE nickname = :nickname AND passwd = :passwd";
    
    $statement = $pdo->prepare($query);
    $statement->execute(['nickname' => $nickname, 'passwd' => $passwd]);
    
    $result = $statement->fetch();
    
    return $result;
}

function GetUsers() {
    include "db.php";
    
    $query = "SELECT * FROM users";
    
    $statement = $pdo->prepare($query);
    $statement->execute();
    
    $result = $statement->fetchAll();
    
    return $result;
}

function AddUser($name, $surname, $username, $password, $isAdmin, $role) {
    include "db.php";
    
    $query = "INSERT INTO users(name, surname, nickname, passwd, isAdmin, role) VALUES(:name, :surname, :username, :passwd, :isAdmin, :role)";
    
    $statement = $pdo->prepare($query);
    $statement->execute(['name' => $name, 'surname' => $surname, 'username' => $username, 'passwd' => $password, 'isAdmin' => $isAdmin, 'role' => $role]);
}

function DeleteUser($id) {
    include "db.php";
    
    $query = "DELETE FROM users WHERE id = :id";
    
    $statement = $pdo->prepare($query);
    $statement->execute(['id' => $id]);
}

function DeleteQuestion($id) {
    include "db.php";
    
    $query = "DELETE FROM questions WHERE indeks = :id";
    
    $statement = $pdo->prepare($query);
    $statement->execute(['id' => $id]);
}

function htmlclean($text){
    $text = preg_replace("'<script[^>]*>.*?</script>'si", '', $text );
    $text = preg_replace('/<a\s+.*?href="([^"]+)"[^>]*>([^<]+)<\/a>/is', '\2 (\1)', $text );
    $text = preg_replace('/<!--.+?-->/', '', $text ); 
    $text = preg_replace('/{.+?}/', '', $text ); 
    $text = preg_replace('/&nbsp;/', ' ', $text );
    $text = preg_replace('/&amp;/', ' ', $text ); 
    $text = preg_replace('/&quot;/', ' ', $text );
    $text = strip_tags($text);
    $text = htmlspecialchars($text); 

    return $text;
}

function secureFileUpload($file, $target_dir, $allowed_types = ['jpg', 'jpeg', 'png', 'gif']) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['status' => false, 'message' => "File Upload Error!"];
    }

    $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($file_ext, $allowed_types)) {
        return ['status' => false, 'message' => "Invalid file type!"];
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    $allowed_types = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif'
    ];

    if (!array_key_exists($mime, $allowed_types)) {
        return ['status' => false, 'message' => "Invalid MIME Type!"];
    }

    $magic_bytes = [
        'jpg' => "\xFF\xD8\xFF",
        'jpeg' => "\xFF\xD8\xFF",
        'png' => "\x89\x50\x4E\x47\x0D\x0A\x1A\x0A",
        'gif' => "GIF"
    ];

    $fh = fopen($file['tmp_name'], 'rb');
    $bytes = fread($fh, 8);
    fclose($fh);

    if (strpos($bytes, $magic_bytes[$file_ext]) !== 0) {
        return ['status' => false, 'message' => "File failed magic byte check!"];
    }

    $random_number = rand(1, 1000);
    $new_filename = $random_number . '_' . basename($file['name']);
    $target_file = $target_dir . $new_filename;

    if (!move_uploaded_file($file['tmp_name'], $target_file)) {
        return ['status' => false, 'message' => "Error moving the uploaded file!"];
    }

    return ['status' => true, 'filename' => $new_filename];
}

function GetQuestions() {
    include "db.php";
    
    $query = "SELECT * FROM questions";
    
    $statement = $pdo->prepare($query);
    $statement->execute();
    
    $result = $statement->fetchAll();
    
    return $result;
}

function AddQuestion($question, $answer1, $answer2, $answer3, $answer4, $ranswer, $difficulty) {
    include "db.php";
    
    $query = "INSERT INTO questions(question, answer1, answer2, answer3, answer4, ranswer, difficulty) VALUES(:question, :answer1, :answer2, :answer3, :answer4, :ranswer, :difficulty)";
    
    $statement = $pdo->prepare($query);
    $statement->execute(['question' => $question, 'answer1' => $answer1, 'answer2' => $answer2, 'answer3' => $answer3, 'answer4' => $answer4, 'ranswer' => $ranswer, 'difficulty' => $difficulty]);
}



function getStudentSubmissionsNot($id) {
    include "db.php";
    $query = "SELECT * FROM questions WHERE indeks NOT IN (SELECT questId FROM submissions WHERE solverId = :id) ORDER BY indeks ASC";
    $statement = $pdo->prepare($query);
    $statement->execute(['id' => $id]);
    return $statement->fetchAll();
}
function SearchQuestions($keyword) {
    include "db.php";
    
    $query = "SELECT * FROM questions WHERE question LIKE :keyword OR answer1 LIKE :keyword OR answer2 LIKE :keyword OR answer3 LIKE :keyword OR answer4 LIKE :keyword";
    
    $statement = $pdo->prepare($query);

    $statement->execute(['keyword' => '%' . $keyword . '%']);
    
    $result = $statement->fetchAll();

    return $result;
}
function updateScore($solverId, $questId, $isCorrect, $difficulty) {
    include "db.php";

    $query = "INSERT INTO submissions (solverId, questId, isCorrect) VALUES (:solverId, :questId, :isCorrect)";
    $statement = $pdo->prepare($query);
    $statement->execute(['solverId' => $solverId, 'questId' => $questId, 'isCorrect' => $isCorrect]);

    if ($isCorrect) {
        $points = getDifficultyScore($difficulty);
        $query = "SELECT * FROM scoreboard WHERE userId = :solverId";
        $statement = $pdo->prepare($query);
        $statement->execute(['solverId' => $solverId]);
        $result = $statement->fetch();
        if ($result) {
            $query = "UPDATE scoreboard SET score = score + :points WHERE userId = :solverId";
            $statement = $pdo->prepare($query);
            $statement->execute(['points' => $points, 'solverId' => $solverId]);
        } else {
            $query = "INSERT INTO scoreboard (userId, score) VALUES (:solverId, :points)";
            $statement = $pdo->prepare($query);
            $statement->execute(['solverId' => $solverId, 'points' => $points]);
        }
    }
}



function getDifficultyScore($difficulty) {
    switch ($difficulty) {
        case 'easy': return 1;
        case 'normal': return 2;
        case 'hard': return 3;
        default: return 0;
    }
}

function getUserById($userId) {
    include "db.php";
    
    $query = "SELECT nickname FROM users WHERE id = :userId";
    
    $statement = $pdo->prepare($query);
    $statement->execute(['userId' => $userId]);
    
    $result = $statement->fetch();
    
    return $result;
}


function getScoreboard() {
    include "db.php";
    
    $query = "SELECT s.id, s.userId, s.score, u.nickname FROM scoreboard s LEFT JOIN users u ON s.userId = u.id  ORDER BY s.score DESC";
    
    $statement = $pdo->prepare($query);

    $statement->execute();
    
    $result = $statement->fetchAll();
    
    return $result;
}
function GetQuestionById($id) {
    include "db.php";
    
    $query = "SELECT * FROM questions WHERE indeks = :id";
    
    $statement = $pdo->prepare($query);

    $statement->execute(['id' => $id]);
    
    $result = $statement->fetch ();

    return $result;
}

function UpdateQuestion($id, $question, $answer1, $answer2, $answer3, $answer4, $ranswer, $difficulty) {
    include "db.php";
    
    $query = "UPDATE questions SET question = :question, answer1 = :answer1, answer2 = :answer2, answer3 = :answer3, answer4 = :answer4, ranswer = :ranswer, difficulty = :difficulty WHERE indeks = :id";
    
    $statement = $pdo->prepare($query);
    
    $statement->execute(['id' => $id,'question' => $question,'answer1' => $answer1,'answer2' => $answer2,'answer3' => $answer3,'answer4' => $answer4,'ranswer' => $ranswer,'difficulty' => $difficulty]);
}




?>