<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';
require '../phpmailer/src/Exception.php';
require 'dbh.inc.php';

if(isset($_POST["sendEmail"])){

    $url = "http://localhost/meetme/pages/bookings.php";
    $mail= new PHPMailer();  
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'ict302meetme@gmail.com';
    $mail->Password = 'xyaascvkduakovsi';
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;
    $mail->isHTML(true);

    $mail->setFrom('ict302meetme@gmail.com');
    $sql = "SELECT student_email FROM meetings";
    $result = $conn->query($sql);
    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            $to = $row['student_email'];
            $mail->addBCC($to);
        }

        $message = '<p>Dear Student, </p>';
        $message .= '<p>You may book your meeting time slot now.</p><br>';
        $message .= '<p> </p>';
        $message .= '<p>Click the link below to book your meeting time slot</p>';
        $message .= '<a href="' . $url . '">' . "Meetme Booking Time Slots" . '</a></p>';

        $mail->Subject = 'Book your Meetme Time Slot';
        $mail->Body = $message;

        if($mail->send()){
            header("Location: http://localhost/meetme/pages/upload.php?emailSend=success");
        } else {
            header("Location: http://localhost/meetme/pages/upload.php?emailSend=failed");
        }
    }
}
?>