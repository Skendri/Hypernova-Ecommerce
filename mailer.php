<?php

require __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

function getMailer()
{
    $mail = new PHPMailer(true);
    // $mail->SMTPDebug = 3;  // Enable verbose debug output

    try {


        // SERVER SETTINGS
        $mail->SMTPDebug = 0;  // Set to 0 to disable debug output
        $mail->isSMTP();
        $mail->Host = $_ENV['SMTP_HOST'];
        $mail->SMTPAuth = true;

        // YOUR GMAIL
        $mail->Username = $_ENV['SMTP_USERNAME'];
        $mail->Password = $_ENV['SMTP_PASSWORD'];

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $_ENV['SMTP_PORT'];
        $mail->isHtml(true);

        return $mail;
    } catch (Exception $e) {
        throw new Exception("Mailer setup error: " . $mail->ErrorInfo);
    }
}
