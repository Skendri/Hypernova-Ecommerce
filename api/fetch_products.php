<?php

session_start();

include __DIR__ . '/../database.php';

header('Content-Type: application/json');

$scope = $_GET['scope'] ?? 'all';
$products = [];

if ($scope === 'mine') {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Please log in to view your products.']);
        exit();
    }

    $userId = (int) $_SESSION['user_id'];

    $stmt = $linkConnect->prepare(
        "SELECT products.*, userdata.username AS owner_name
        FROM products
        LEFT JOIN userdata ON products.user_id = userdata.id
        WHERE products.user_id = ?
        ORDER BY products.id DESC"
    );
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $linkConnect->query(
        "SELECT products.*, userdata.username AS owner_name
        FROM products
        LEFT JOIN userdata ON products.user_id = userdata.id
        ORDER BY products.id DESC"
    );
}

while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

echo json_encode($products);
