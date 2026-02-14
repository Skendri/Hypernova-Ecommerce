<?php

require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

function getMailer()
{
    $mail = new PHPMailer(true);
    $mail->SMTPDebug = 3;

    try {


        // SERVER SETTINGS
        $mail->SMTPDebug = 0;  // Set to 0 to disable debug output
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;

        // YOUR GMAIL
        $mail->Username = 'skendri.peza@umsh.edu.al';
        $mail->Password = 'uedhjiimssoqeufx';

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->isHtml(true);

        return $mail;
    } catch (Exception $e) {
        throw new Exception("Mailer setup error: " . $mail->ErrorInfo);
    }
}
