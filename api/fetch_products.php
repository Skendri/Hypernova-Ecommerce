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

$scope = $_GET['scope'] ?? 'all';
$products = [];
$productId = isset($_GET['id']) && ctype_digit((string) $_GET['id'])
    ? (int) $_GET['id']
    : 0;
$category = trim($_GET['category'] ?? '');
$minPrice = trim($_GET['min_price'] ?? '');
$maxPrice = trim($_GET['max_price'] ?? '');
$search = trim($_GET['q'] ?? '');
$status = trim($_GET['status'] ?? '');
$limit = isset($_GET['limit']) && ctype_digit((string) $_GET['limit'])
    ? min((int) $_GET['limit'], 100)
    : 0;
$conditions = [];
$params = [];
$types = '';
$currentUserId = current_user_id();

if ($productId > 0) {
    $conditions[] = 'products.id = ?';
    $params[] = $productId;
    $types .= 'i';

    if ($currentUserId) {
        $conditions[] = '(products.status = "active" OR products.user_id = ?)';
        $params[] = $currentUserId;
        $types .= 'i';
    } else {
        $conditions[] = 'products.status = "active"';
    }
} elseif ($scope !== 'mine') {
    $conditions[] = 'products.status = "active"';
}

if ($scope === 'mine') {
    $userId = require_user_id();

    $conditions[] = 'products.user_id = ?';
    $params[] = $userId;
    $types .= 'i';

    if ($status !== '' && in_array($status, PRODUCT_STATUSES, true)) {
        $conditions[] = 'products.status = ?';
        $params[] = $status;
        $types .= 's';
    }
}

if ($category !== '') {
    $conditions[] = 'products.category = ?';
    $params[] = $category;
    $types .= 's';
}

if ($minPrice !== '' && is_numeric($minPrice)) {
    $conditions[] = 'products.price >= ?';
    $params[] = (float) $minPrice;
    $types .= 'd';
}

if ($maxPrice !== '' && is_numeric($maxPrice)) {
    $conditions[] = 'products.price <= ?';
    $params[] = (float) $maxPrice;
    $types .= 'd';
}

if ($search !== '') {
    $searchTerm = '%' . $search . '%';
    $conditions[] = '(products.title LIKE ? OR products.description LIKE ?)';
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= 'ss';
}

$sql = "SELECT products.*, userdata.username AS owner_name,
        CASE WHEN products.user_id = ? THEN 1 ELSE 0 END AS is_owner
    FROM products
    LEFT JOIN userdata ON products.user_id = userdata.id";
$ownerCheckId = $currentUserId ?? 0;
array_unshift($params, $ownerCheckId);
$types = 'i' . $types;

if (!empty($conditions)) {
    $sql .= ' WHERE ' . implode(' AND ', $conditions);
}

$sql .= ' ORDER BY products.id DESC';

if ($limit > 0) {
    $sql .= ' LIMIT ' . $limit;
}

$stmt = $linkConnect->prepare($sql);

if (!$stmt) {
    send_json(500, [
        'success' => false,
        'message' => 'Could not prepare products query.',
    ]);
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

send_json(200, [
    'success' => true,
    'data' => $products,
]);
