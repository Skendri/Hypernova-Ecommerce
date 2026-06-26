<?php
session_start();

include __DIR__ . '/../config/database.php';
include __DIR__ . '/../includes/api_helpers.php';
include __DIR__ . '/../includes/product_helpers.php';

header('Content-Type: application/json');

try {
    ensure_products_schema($linkConnect);
} catch (RuntimeException $error) {
    send_json(500, [
        'success' => false,
        'message' => $error->getMessage(),
    ]);
}

$productId = (int) ($_POST['id'] ?? 0);
$userId = require_user_id();

if ($productId < 1) {
    send_json(400, [
        'success' => false,
        'message' => 'Missing product id.',
    ]);
}

$stmt = $linkConnect->prepare(
    "DELETE FROM products
    WHERE id = ? AND user_id = ?"
);
$stmt->bind_param("ii", $productId, $userId);
$stmt->execute();

if ($stmt->affected_rows < 1) {
    send_json(404, [
        'success' => false,
        'message' => 'Product was not found.',
    ]);
}

send_json(200, [
    'success' => true,
    'message' => 'Product deleted successfully.',
]);
