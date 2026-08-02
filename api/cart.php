<?php

session_start();

require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/api_helpers.php';
require __DIR__ . '/../includes/cart_helpers.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $items = [];
    $cart = cart_items();

    if ($cart) {
        $ids = array_values(array_unique(array_map(static fn ($item) => (int) $item['product_id'], $cart)));
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $types = str_repeat('i', count($ids));
        $stmt = $linkConnect->prepare("SELECT id, title, price, image, category FROM products WHERE status = 'active' AND id IN ($placeholders)");
        $stmt->bind_param($types, ...$ids);
        $stmt->execute();
        $result = $stmt->get_result();
        $products = [];

        while ($product = $result->fetch_assoc()) {
            $products[(int) $product['id']] = $product;
        }

        $cleanCart = [];
        foreach ($cart as $key => $cartItem) {
            $productId = (int) $cartItem['product_id'];
            if (!isset($products[$productId])) {
                continue;
            }
            $item = array_merge($cartItem, $products[$productId]);
            $item['key'] = $key;
            $items[] = $item;
            $cleanCart[$key] = $cartItem;
        }
        $_SESSION['cart'] = $cleanCart;
    }

    send_json(200, ['success' => true, 'data' => $items, 'count' => cart_item_count()]);
}

if ($method !== 'POST') {
    send_json(405, ['success' => false, 'message' => 'Method not allowed.']);
}

$action = $_POST['action'] ?? '';

if ($action === 'add') {
    $productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT) ?: 1;

    if (!$productId || $productId < 1) {
        send_json(422, ['success' => false, 'message' => 'Choose a valid product.']);
    }

    $stmt = $linkConnect->prepare("SELECT id FROM products WHERE id = ? AND status = 'active'");
    $stmt->bind_param('i', $productId);
    $stmt->execute();

    if (!$stmt->get_result()->fetch_assoc()) {
        send_json(404, ['success' => false, 'message' => 'This product is no longer available.']);
    }

    set_cart_item($productId, $quantity, (string) ($_POST['size'] ?? ''), (string) ($_POST['color'] ?? ''));
    send_json(200, ['success' => true, 'message' => 'Added to cart.', 'count' => cart_item_count()]);
}

if ($action === 'update' || $action === 'remove') {
    $key = (string) ($_POST['key'] ?? '');
    update_cart_item($key, $action === 'remove' ? 0 : (int) ($_POST['quantity'] ?? 0));
    send_json(200, ['success' => true, 'count' => cart_item_count()]);
}

send_json(422, ['success' => false, 'message' => 'Unknown cart action.']);
