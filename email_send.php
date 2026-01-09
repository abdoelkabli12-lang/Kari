<?php

require __DIR__ . '/../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
//use Random\RandomException;
session_start();

///**
// * @throws RandomException
// */
function sendOTP($email) {
    $otp = random_int(100000, 999999);
    $_SESSION['otp'] = $otp;
    $_SESSION['otp_time'] = time();

    $mail = new PHPMailer(true);

    try {
        $mail->SMTPDebug = 2;
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'abdo.el.kabli12@gmail.com';
        $mail->Password   = 'qdxfpxgbguvillak';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('abdo.el.kabli12@gmail.com', 'Money Track');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Your OTP Code';
        $mail->Body    = "<h2>Your OTP is: <strong>$otp</strong></h2>";

        $mail->send();
    } catch (Exception $e) {
        return false;
    }
}
