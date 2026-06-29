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

$stmt = $linkConnect->prepare(
    "SELECT id, title, description, price, category, image, phone, status, created_at, updated_at
    FROM products
    WHERE user_id = ?
    ORDER BY id DESC"
);

if (!$stmt) {
    send_json(500, [
        'success' => false,
        'message' => 'Could not prepare dashboard query.',
    ]);
}

$stmt->bind_param("i", $userId);

if (!$stmt->execute()) {
    send_json(500, [
        'success' => false,
        'message' => 'Could not load dashboard data.',
    ]);
}

$result = $stmt->get_result();

$products = [];
$totalValue = 0;
$categoryTotals = [];
$monthlyTotals = [];
$statusTotals = [];
$latestDate = null;

while ($row = $result->fetch_assoc()) {
    $price = (float) $row['price'];
    $category = $row['category'] ?: 'Uncategorized';
    $createdAt = $row['created_at'] ?? null;
    $monthKey = $createdAt ? date('M Y', strtotime($createdAt)) : 'No date';

    $totalValue += $price;
    $categoryTotals[$category] = ($categoryTotals[$category] ?? 0) + 1;
    $monthlyTotals[$monthKey] = ($monthlyTotals[$monthKey] ?? 0) + 1;
    $status = $row['status'] ?: 'active';
    $statusTotals[$status] = ($statusTotals[$status] ?? 0) + 1;

    if ($createdAt && (!$latestDate || strtotime($createdAt) > strtotime($latestDate))) {
        $latestDate = $createdAt;
    }

    $products[] = $row;
}

$averagePrice = count($products) ? $totalValue / count($products) : 0;

echo json_encode([
    'success' => true,
    'summary' => [
        'total_products' => count($products),
        'total_value' => round($totalValue, 2),
        'average_price' => round($averagePrice, 2),
        'latest_listing' => $latestDate,
    ],
    'categories' => $categoryTotals,
    'monthly' => $monthlyTotals,
    'statuses' => $statusTotals,
    'products' => $products,
]);
