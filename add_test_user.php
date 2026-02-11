<?php
require 'database.php';

$email = 'test@example.com';
$password = password_hash('testpass', PASSWORD_DEFAULT);

$sql = "INSERT INTO userdata (email, password) VALUES (?, ?)";
$stmt = $linkConnect->prepare($sql);
$stmt->bind_param("ss", $email, $password);

if ($stmt->execute()) {
    echo "Test user added successfully.";
} else {
    echo "Error adding test user: " . $stmt->error;
}

$stmt->close();
$linkConnect->close();
