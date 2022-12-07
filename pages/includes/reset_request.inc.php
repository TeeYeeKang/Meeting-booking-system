<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../phpmailer/src/Exception.php';
require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';

if(isset($_POST["reset_request_submit"])){

    // Token - used to authenticate the correct user
    $selector = bin2hex(random_bytes(8));
    $token = random_bytes(32);

    $url = "http://localhost/meetme/pages/create_new_password.php?selector=" . $selector . "&validator=" . bin2hex($token);

    $expires = date("U") + 1800;

    require 'dbh.inc.php';

    $userEmail = $_POST["email"];

    $sql = "DELETE FROM pwdreset WHERE murdoch_email=?";
    $stmt = mysqli_stmt_init($conn);  // stmt stand for statement

    if(!mysqli_stmt_prepare($stmt, $sql)){
        echo "There was an error!";
        exit();
    } else {
        mysqli_stmt_bind_param($stmt, "s", $userEmail);
        mysqli_stmt_execute($stmt);
    }

    $sql = "INSERT INTO pwdreset (murdoch_email, pwdResetSelector, pwdResetToken, pwdResetExpires) VALUES (?, ?, ?, ?);";
    $stmt = mysqli_stmt_init($conn);  

    if(!mysqli_stmt_prepare($stmt, $sql)){
        echo "There was an error!";
        exit();
    } else {
        $hashedToken = password_hash($token, PASSWORD_DEFAULT);
        mysqli_stmt_bind_param($stmt, "ssss", $userEmail, $selector, $hashedToken, $expires);
        mysqli_stmt_execute($stmt);
    }

    mysqli_stmt_close($stmt);
    //mysqli_close();

    //$to = $userEmail;
    //$subject = 'Reset password';

    $message = '<p> Click the link to reset your password</p>';
    $message .= '<p> Here is your password reset link: </br>';
    $message .= '<a href="' . $url . '">' . $url . '</a></p>';
    // .= sign is means continue from the variable. But you can simply
    // continue writing from the 1st $message variable

    //$headers = "From: Meetme <ict302meetme@gmail.com>\r\n";
    //$headers .= "Reply-To: ict302meetme@gmail.com\r\n";
    //$headers .= "Content-type: text/html\r\n";
    //mail($to, $subject, $message, $headers);

    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'ict302meetme@gmail.com';
    $mail->Password = 'xyaascvkduakovsi'; //gmail app password
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;
    $mail->setFrom('ict302meetme@gmail.com');
    $mail->addAddress($_POST["email"]);
    $mail->isHTML(true);
    $mail->Subject = 'Reset Password';
    $mail->Body = $message;
    $mail->send();
    
    header("Location: http://localhost/meetme/pages/ForgetPassword.php?reset=success");

} else {
    header("Location: http://localhost/meetme/pages/Login.php");
}

