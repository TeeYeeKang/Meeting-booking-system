<?php

if(isset($_POST["reset_password_submit"])){

    $selector = $_POST["selector"];
    $validator = $_POST["validator"];
    $password = $_POST["pwd"];
    $passwordRepeat = $_POST["pwd_repeat"];

    if(empty($password) || empty($passwordRepeat)){
        header("Location: http://localhost/meetme/pages/Login.php?newpwd=empty");
        exit();
    } else if($password != $passwordRepeat) {
        header("Location: http://localhost/meetme/pages/Login.php?newpwd=pwdNotSame");
        exit();
    } 

    // used to check if the token is expired
    $currentDate = date("U");

    require 'dbh.inc.php';

    $sql = "SELECT * FROM pwdreset WHERE pwdResetSelector=? AND pwdResetExpires >= ?";
    $stmt = mysqli_stmt_init($conn);  // stmt stand for statement

    if(!mysqli_stmt_prepare($stmt, $sql)){
        echo "There was an error!";
        exit();
    } else {
        mysqli_stmt_bind_param($stmt, "ss", $selector, $currentDate);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        if(!$row = mysqli_fetch_assoc($result)){
            echo "You need to re-submit your reset request.";
            exit();
        } else {
            
            $tokenBin = hex2bin($validator);
            $tokenCheck = password_verify($tokenBin, $row["pwdResetToken"]);
            
            if($tokenCheck === false){
                echo "You need to re-submit your reset request";
                exit();
            } else if($tokenCheck === true){

                $tokenEmail = $row['murdoch_email'];

                $sql = "SELECT * FROM useraccount WHERE murdoch_email=?";
                $stmt = mysqli_stmt_init($conn);  // stmt stand for statement

                if(!mysqli_stmt_prepare($stmt, $sql)){
                    echo "There was an error!";
                    exit();
                } else {
                    mysqli_stmt_bind_param($stmt, "s", $tokenEmail);
                    mysqli_stmt_execute($stmt);

                    $result = mysqli_stmt_get_result($stmt);
                    if(!$row = mysqli_fetch_assoc($result)){
                        echo "There was an error !";
                        exit();
                    } else {

                        $sql = "UPDATE useraccount SET pass=? WHERE murdoch_email=?";
                        $stmt = mysqli_stmt_init($conn); 

                        if(!mysqli_stmt_prepare($stmt, $sql)){
                            echo "There was an error!";
                            exit();
                        } else {
                            #$newPwdHash = password_hash($password, PASSWORD_DEFAULT);
                            mysqli_stmt_bind_param($stmt, "ss", $password, $tokenEmail);
                            mysqli_stmt_execute($stmt);

                            $sql = "DELETE FROM pwdreset WHERE murdoch_email=?";
                            $stmt = mysqli_stmt_init($conn);  

                            if(!mysqli_stmt_prepare($stmt, $sql)){
                                echo "There was an error!";
                                exit();
                            } else {
                                mysqli_stmt_bind_param($stmt, "s", $tokenEmail);
                                mysqli_stmt_execute($stmt);
                                header("Location: http://localhost/meetme/pages/Login.php?newpwd=passwordupdated");
                            }
                        }
                    } 
                }
            }
        }
    }

} else {
    header("Location: ../pages/Login.php");
}