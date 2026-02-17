<?php


require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// $db_server = $_ENV['DB_SERVER'];
// $db_user = $_ENV['DB_USER'];
// $db_pass = $_ENV['DB_PASS'];
// $db_name = $_ENV['DB_NAME'];
// $linkConnect = "";

// Create connection
$linkConnect = mysqli_connect($_ENV['DB_SERVER'],  $_ENV['DB_USER'],  $_ENV['DB_PASS'], $_ENV['DB_NAME']);
// $linkConnect = new mysqli($db_server,  $db_user,  $db_pass, $db_name);

if ($linkConnect->connect_error) {

    die("nuk u lidhe me databazen: " . $linkConnect->connect_error);
    echo "nuk u lidhe me databazen";
}
// echo "ti je lidh me databazen";
