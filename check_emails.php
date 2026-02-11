<?php
require __DIR__ . "/database.php";

$sql = "SELECT email FROM userdata LIMIT 5";
$result = $linkConnect->query($sql);

if ($result->num_rows > 0) {
    echo "Existing emails in database:\n";
    while ($row = $result->fetch_assoc()) {
        echo $row["email"] . "\n";
    }
} else {
    echo "No emails found in database.\n";
}

$linkConnect->close();
