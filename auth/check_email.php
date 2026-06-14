<?php
// this file is for Checks email while user is typing. User experience.
include __DIR__ . '/../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    $stmt = $linkConnect->prepare("SELECT 1 FROM userdata WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo 'exists';
    } else {
        echo 'not exists';
    }

    // Clean up // This closes the prepared statement and database connection.
    $stmt->close();
    $linkConnect->close();
}
