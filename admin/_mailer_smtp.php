<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require_once '../vendor/autoload.php';
require_once '../_config.php';
$db = new mysqli(server,username,password,dbname);
$settings = $db->query('SELECT * FROM `settings` WHERE 1')->fetch_object();

$mail = new PHPMailer(true);

    $mail->SMTPDebug = 0;
    $mail->isSMTP();
    $mail->SMTPAuth   = true;
    $mail->Host = $settings->smtp_host;
    $mail->Port = $settings->smtp_port;
    $mail->Username = $settings->smtp_username;
    $mail->Password = $settings->smtp_password;
    $mail->SMTPSecure = $settings->smtp_security;

    //Recipients
    $mail->setFrom($settings->smtp_username, $settings->smtp_sendername);
    $mail->addAddress($receiver_email);
    //Content
    $mail->isHTML(true);
    $mail->Subject = $title;
    $mail->Body    = $views;

    $mail->send();