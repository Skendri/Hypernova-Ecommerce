<?php

require __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

function getMailer()
{
    $mail = new PHPMailer(true);

    try {
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = $_ENV['SMTP_HOST'];
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['SMTP_USERNAME'];
        $mail->Password = $_ENV['SMTP_PASSWORD'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $_ENV['SMTP_PORT'];
        $mail->isHtml(true);

        return $mail;
    } catch (Exception $e) {
        throw new Exception("Mailer setup error: " . $e->getMessage());
    }
}
