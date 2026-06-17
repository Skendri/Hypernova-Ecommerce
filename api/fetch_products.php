<?php

session_start();

include __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

$scope = $_GET['scope'] ?? 'all';
$products = [];
$category = trim($_GET['category'] ?? '');
$minPrice = trim($_GET['min_price'] ?? '');
$maxPrice = trim($_GET['max_price'] ?? '');
$search = trim($_GET['q'] ?? '');
$conditions = [];
$params = [];
$types = '';

if ($scope === 'mine') {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Please log in to view your products.']);
        exit();
    }

    $userId = (int) $_SESSION['user_id'];
    $conditions[] = 'products.user_id = ?';
    $params[] = $userId;
    $types .= 'i';
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

$sql = "SELECT products.*, userdata.username AS owner_name
    FROM products
    LEFT JOIN userdata ON products.user_id = userdata.id";

if (!empty($conditions)) {
    $sql .= ' WHERE ' . implode(' AND ', $conditions);
}

$sql .= ' ORDER BY products.id DESC';

$stmt = $linkConnect->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Could not prepare products query.']);
    exit();
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

echo json_encode($products);
