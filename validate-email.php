<?php

$linkConnect = require __DIR__ . "/database.php";

$sql = sprintf(
    "SELECT * FROM userdata
                WHERE email = '%s'",
    $linkConnect->real_escape_string($_GET["email"])
);

$result = $mylinkConnectsqli->query($sql);

$is_available = $result->num_rows === 0;

header("Content-Type: application/json");

echo json_encode(["available" => $is_available]);
