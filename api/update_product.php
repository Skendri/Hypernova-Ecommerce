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

$userId = require_user_id();
$productId = (int) ($_POST['id'] ?? 0);

if ($productId < 1) {
    send_json(400, [
        'success' => false,
        'message' => 'Missing product id.',
    ]);
}

$product = validate_product_input(false);
$uploadedImages = save_uploaded_product_images();

if (!empty($uploadedImages)) {
    $imagesJson = json_encode($uploadedImages);
    $stmt = $linkConnect->prepare(
        "UPDATE products
         SET title = ?, description = ?, price = ?, category = ?, image = ?, phone = ?, status = ?
         WHERE id = ? AND user_id = ?"
    );

    if (!$stmt) {
        send_json(500, [
            'success' => false,
            'message' => 'Could not prepare product update.',
        ]);
    }

    $stmt->bind_param(
        'ssdssssii',
        $product['title'],
        $product['description'],
        $product['price'],
        $product['category'],
        $imagesJson,
        $product['phone'],
        $product['status'],
        $productId,
        $userId
    );
} else {
    $stmt = $linkConnect->prepare(
        "UPDATE products
         SET title = ?, description = ?, price = ?, category = ?, phone = ?, status = ?
         WHERE id = ? AND user_id = ?"
    );

    if (!$stmt) {
        send_json(500, [
            'success' => false,
            'message' => 'Could not prepare product update.',
        ]);
    }

    $stmt->bind_param(
        'ssdsssii',
        $product['title'],
        $product['description'],
        $product['price'],
        $product['category'],
        $product['phone'],
        $product['status'],
        $productId,
        $userId
    );
}

if (!$stmt->execute()) {
    send_json(500, [
        'success' => false,
        'message' => 'Could not update product.',
    ]);
}

if ($stmt->affected_rows < 1) {
    $ownershipCheck = $linkConnect->prepare('SELECT id FROM products WHERE id = ? AND user_id = ?');

    if (!$ownershipCheck) {
        send_json(500, [
            'success' => false,
            'message' => 'Could not verify product ownership.',
        ]);
    }

    $ownershipCheck->bind_param('ii', $productId, $userId);
    if (!$ownershipCheck->execute()) {
        send_json(500, [
            'success' => false,
            'message' => 'Could not verify product ownership.',
        ]);
    }

    $result = $ownershipCheck->get_result();

    if (!$result->fetch_assoc()) {
        send_json(404, [
            'success' => false,
            'message' => 'Product was not found.',
        ]);
    }
}

send_json(200, [
    'success' => true,
    'message' => 'Product updated successfully.',
    'product_id' => $productId,
]);

