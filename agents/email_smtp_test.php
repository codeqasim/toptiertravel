<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require_once '../vendor/autoload.php';

// //
//     // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                   //Enable verbose debug output
//     $mail->SMTPDebug = 0;
//     $mail->isSMTP();                                            //Send using SMTP
//     $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
//     $mail->Host = "sandbox.smtp.mailtrap.io";
//     $mail->Port = "25";
//     $mail->Username = "8546f1ffe88bc7";
//     $mail->Password = "48aa26531cab6b";
//     $mail->SMTPSecure = "tls";

    $mail = new PHPMailer();

    try {

    $mail->SMTPDebug = SMTP::DEBUG_SERVER;

    $mail->isSMTP();
    $mail->Host = $_POST['hostname'];
    $mail->SMTPAuth = true;
    $mail->Port = $_POST['port'];
    $mail->Username = $_POST['username'];
    $mail->Password = $_POST['password'];

    // echo $_POST['hostname'];
    // echo $_POST['port'];
    // echo $_POST['username'];
    // echo $_POST['password'];

    //Recipients
    $mail->setFrom($_POST['from_email']);
    $mail->addAddress($_POST['to_email']);                     //Name is optional

    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = "Test Email";
    $mail->Body    = "<p>This is the test email to make sure your settings are working fine</p>";

    // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    $mail->send();

    // echo 'Message has been sent';
    } catch (Exception $e) {
        echo json_decode($mail->ErrorInfo);
    }