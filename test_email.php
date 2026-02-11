<?php

$mail = require __DIR__ . "/mailer.php";

$mail->setFrom("skendripeza0@gmail.com");
$mail->addAddress("test@example.com"); // Replace with a test email
$mail->Subject = "Test Email";
$mail->Body = "This is a test email.";

try {
    $mail->send();
    echo "Email sent successfully.";
} catch (Exception $e) {
    echo "Email could not be sent. Error: {$mail->ErrorInfo}";
}
