<?php

include __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed.');
}

$token = (string) ($_POST['token'] ?? '');
$password = (string) ($_POST['password'] ?? '');
$confirmPassword = (string) ($_POST['confirm_password'] ?? '');

if (!preg_match('/^[a-f0-9]{32}$/i', $token)) {
    http_response_code(400);
    exit('Invalid or expired password reset link.');
}

$token_hash = hash("sha256", $token);

$sql = "SELECT * FROM userdata
        WHERE reset_token_hash = ?";

$stmt = $linkConnect->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    exit('Password reset is temporarily unavailable.');
}

$stmt->bind_param("s", $token_hash);

$stmt->execute();

$result = $stmt->get_result();

$user = $result->fetch_assoc();

if ($user === null) {
    die("token not found");
}

if (empty($user['reset_token_expires_at']) || strtotime($user['reset_token_expires_at']) <= time()) {
    http_response_code(400);
    exit('Invalid or expired password reset link.');
}

if (strlen($password) < 12 || !preg_match('/[a-z]/i', $password) || !preg_match('/[0-9]/', $password)) {
    http_response_code(400);
    exit('Password must be at least 12 characters and include a letter and a number.');
}

if ($password !== $confirmPassword) {
    http_response_code(400);
    exit('Passwords must match.');
}

$password_hash = password_hash($password, PASSWORD_DEFAULT);
$now = date('Y-m-d H:i:s');

$sql = "UPDATE userdata
        SET password = ?,
            reset_token_hash = NULL,
            reset_token_expires_at = NULL
        WHERE id = ? AND reset_token_hash = ? AND reset_token_expires_at > ?";

$stmt = $linkConnect->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    exit('Password reset is temporarily unavailable.');
}

$stmt->bind_param('siss', $password_hash, $user['id'], $token_hash, $now);

$stmt->execute();

if ($stmt->affected_rows !== 1) {
    http_response_code(400);
    exit('Invalid or expired password reset link.');
}

echo "Password updated. You can now login.";
