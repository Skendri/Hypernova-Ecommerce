<?php

$email = $_POST["email"];

$token = bin2hex(random_bytes(16));

$token_hash = hash("sha256", $token);

$expiry = date("Y-m-d H:i:s", time() + 60 * 30);

$mysqli_connect = require __DIR__ . "/database.php";

require __DIR__ . "/mailer.php";

$sql = "UPDATE userdata
        SET reset_token_hash = ?,
            reset_token_expires_at = ?
        WHERE email = ?";

$stmt = $linkConnect->prepare($sql);

$stmt->bind_param("sss", $token_hash, $expiry, $email);

$stmt->execute();

if ($linkConnect->affected_rows) {

    $mail = getMailer();

    $mail->setFrom("skendri.peza@umsh.edu.al");
    $mail->addAddress($email);
    $mail->Subject = "Password Reset";
    $mail->Body = <<<END

    Click <a href="http://localhost/Hypernova-Ecommerce/resetpassword.php?token=$token">here</a>
    to reset your password.

    END;

    try {

        $mail->send();
        echo "Message sent, please check your inbox.";
    } catch (Exception $e) {

        echo "Message could not be sent. Mailer error: {$mail->ErrorInfo}";
    }
} else {
    echo "Email not found or no changes made.";
}
