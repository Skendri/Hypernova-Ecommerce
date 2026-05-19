<?php

include __DIR__ . '/../database.php';

$sql = "SELECT * FROM products ORDER BY id DESC";

$result = $linkConnect->query($sql);

$products = [];

while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

header('Content-Type: application/json');

echo json_encode($products);
