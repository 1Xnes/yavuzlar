<?php

    session_start();
    include "functions/functions.php";
    if(!isset($_POST['username']) || !isset($_POST['password'])) {
        header("Location: login.php?message=Kullanıcı adı ve şifre boş bırakılamaz!");
        die();
    }else {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $userIP = $_SERVER['REMOTE_ADDR'];

        if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
            $userIP = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }

        include "functions/db.php";

        $query = "SELECT isBanned, attempt_count FROM ip_ban WHERE ip = :ip";
        $statement = $pdo->prepare($query);
        $statement -> execute(['ip' => $userIP]);
        $ipInfo = $statement -> fetch();

        if($ipInfo){
            if($ipInfo['isBanned']){
                header("Location: login.php?=message=IP Adresiniz Yasaklandı!");
                die();
            }

            $attempt_count = $ipInfo['attempt_count'] + 1;
            $isBanned = $attempt_count >= 20 ? 1:0;
            $query = 'UPDATE ip_ban SET attempt_count = :attempt_count, isBanned = :isBanned WHERE ip = :ip';
            $statement = $pdo -> prepare($query);
            $statement -> execute([
                'attempt_count' => $attempt_count,
                'isBanned' => $isBanned,
                'ip' => $userIP
            ]);

        }else{
            $query = 'INSERT INTO ip_ban (ip, attempt_count) VALUES (:ip, 1)';
            $statement = $pdo -> prepare($query);
            $statement -> execute(['ip' => $userIP]);
        }


        $result = Login($username,$password);
        $rowCount = $result['count'];
    
        if($rowCount == 1){
            $query = 'DELETE FROM ip_ban WHERE ip = :ip';
            $statement = $pdo -> prepare($query);
            $statement -> execute(['ip' => $userIP]);
            $userInfo = $result;
            
            $_SESSION["id"] = $result["id"];
            $_SESSION["isAdmin"] = $result["isAdmin"];
            $_SESSION["username"] = $result["nickname"];
            
            header("Location:index.php");
            exit();
        }else{
            header("Location:login.php");
            exit();
        }

        echo "<pre>";
        print_r($result);
        echo"</pre>";

        die();
    
    }
?>