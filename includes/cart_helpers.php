<?php

function cart_items(): array
{
    return isset($_SESSION['cart']) && is_array($_SESSION['cart']) ? $_SESSION['cart'] : [];
}

function cart_item_count(): int
{
    return array_sum(array_map(static fn ($item) => (int) ($item['quantity'] ?? 0), cart_items()));
}

function cart_key(int $productId, string $size, string $color): string
{
    return $productId . ':' . strtolower($size) . ':' . strtolower($color);
}

function set_cart_item(int $productId, int $quantity, string $size = '', string $color = ''): void
{
    $quantity = max(1, min(9, $quantity));
    $key = cart_key($productId, $size, $color);
    $cart = cart_items();

    if (isset($cart[$key])) {
        $quantity = min(9, $quantity + (int) $cart[$key]['quantity']);
    }

    $cart[$key] = [
        'product_id' => $productId,
        'quantity' => $quantity,
        'size' => substr(trim($size), 0, 30),
        'color' => substr(trim($color), 0, 30),
    ];
    $_SESSION['cart'] = $cart;
}

function update_cart_item(string $key, int $quantity): void
{
    $cart = cart_items();

    if (!isset($cart[$key])) {
        return;
    }

    if ($quantity < 1) {
        unset($cart[$key]);
    } else {
        $cart[$key]['quantity'] = min(9, $quantity);
    }

    $_SESSION['cart'] = $cart;
}
