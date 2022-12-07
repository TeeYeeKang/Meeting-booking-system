<?php

    include_once 'dbh.inc.php';

    $MurdochID = '0'; // default value
    $username = $_POST['username'];
    $emailAddress = $_POST['email'];
    $Pass = $_POST['psw2'];   

    $sql = "INSERT INTO useraccount (murdoch_id, username, murdoch_email, pass) 
            VALUES ('$MurdochID', '$username', '$emailAddress', '$Pass');";

    $result = mysqli_query($conn, $sql);

    header("Location: http://localhost/meetme/pages/Login.php?newAccount=success");