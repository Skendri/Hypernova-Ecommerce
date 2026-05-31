<?php
session_start();

include __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Please log in before deleting a product.']);
    exit();
}

$productId = (int) ($_POST['id'] ?? 0);
$userId = (int) $_SESSION['user_id'];

if ($productId < 1) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing product id.']);
    exit();
}

$stmt = $linkConnect->prepare(
    "DELETE FROM products
    WHERE id = ? AND user_id = ?"
);
$stmt->bind_param("ii", $productId, $userId);
$stmt->execute();

if ($stmt->affected_rows < 1) {
    http_response_code(404);
    echo json_encode(['error' => 'Product was not found.']);
    exit();
}

echo json_encode(['message' => 'Product deleted successfully.']);
