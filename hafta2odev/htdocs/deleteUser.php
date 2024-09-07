<?php

    include "functions/functions.php";

    $userId = $_GET["id"];

    DeleteUser($userId);
    header("Location:userList.php");
    exit();

?>