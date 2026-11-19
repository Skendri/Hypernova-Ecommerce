<?php
require_once __DIR__ . '/../config/database.php';

$token = (string) ($_GET['token'] ?? '');
$message = 'This verification link is invalid or has expired. Please register again or contact support.';
$success = false;

if (preg_match('/^[a-f0-9]{64}$/i', $token)) {
    $tokenHash = hash('sha256', $token);
    $now = date('Y-m-d H:i:s');
    $stmt = $linkConnect->prepare(
        'UPDATE userdata
         SET email_verified_at = ?, email_verification_token_hash = NULL, email_verification_expires_at = NULL
         WHERE email_verification_token_hash = ? AND email_verification_expires_at > ? AND email_verified_at IS NULL'
    );
    if ($stmt) {
        $stmt->bind_param('sss', $now, $tokenHash, $now);
        $stmt->execute();
        $success = $stmt->affected_rows === 1;
        $stmt->close();
    }
    if ($success) {
        $message = 'Your email is verified. You can now log in.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Email verification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <main class="container py-5 text-center">
        <h1>Email verification</h1>
        <p><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
        <a class="btn btn-primary" href="login.php">Go to login</a>
    </main>
</body>
</html>
