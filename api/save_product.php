<?php
session_start();

include __DIR__ . '/../config/database.php';
include __DIR__ . '/../includes/api_helpers.php';
include __DIR__ . '/../includes/product_helpers.php';

try {
    ensure_products_schema($linkConnect);
} catch (RuntimeException $error) {
    send_json(500, [
        'success' => false,
        'message' => $error->getMessage(),
    ]);
}

$user_id = require_user_id();
$product = validate_product_input(true);
$uploadedImages = save_uploaded_product_images();
$imagesJson = json_encode($uploadedImages);

$stmt = $linkConnect->prepare(
    "INSERT INTO products
    (user_id, title, description, price, category, image, phone, status)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
);

if (!$stmt) {
    send_json(500, [
        'success' => false,
        'message' => 'Could not prepare product insert.',
    ]);
}

$stmt->bind_param(
    "issdssss",
    $user_id,
    $product['title'],
    $product['description'],
    $product['price'],
    $product['category'],
    $imagesJson,
    $product['phone'],
    $product['status']
);

if (!$stmt->execute()) {
    send_json(500, [
        'success' => false,
        'message' => 'Could not publish product.',
    ]);
}

send_json(201, [
    'success' => true,
    'message' => 'Product saved successfully.',
    'product_id' => $stmt->insert_id,
]);
