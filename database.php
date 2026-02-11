<?php
$db_server = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "provedb";
// $linkConnect = "";

// Create connection
$linkConnect = mysqli_connect($db_server,  $db_user,  $db_pass, $db_name);
// $linkConnect = new mysqli($db_server,  $db_user,  $db_pass, $db_name);

if ($linkConnect->connect_error) {

    die("nuk u lidhe me databazen: " . $linkConnect->connect_error);
    echo "nuk u lidhe me databazen";
}
// echo "ti je lidh me databazen";
