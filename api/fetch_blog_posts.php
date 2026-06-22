<?php
session_start();

header('Content-Type: application/json');

include __DIR__ . '/../config/database.php';
include __DIR__ . '/blog_post_schema.php';

function sendJsonResponse(int $statusCode, string $message): void
{
    http_response_code($statusCode);
    echo json_encode(['message' => $message]);
    exit();
}

try {
    ensureBlogPostsTable($linkConnect);
} catch (RuntimeException $error) {
    sendJsonResponse(500, $error->getMessage());
}

$scope = $_GET['scope'] ?? 'published';
$posts = [];

if ($scope === 'mine') {
    if (!isset($_SESSION['user_id'])) {
        sendJsonResponse(401, 'Please log in to view your posts.');
    }

    $stmt = $linkConnect->prepare(
        "SELECT bp.id, bp.title, bp.excerpt, bp.content, bp.cover_image, bp.status, bp.view_count, bp.created_at,
                COALESCE(u.username, 'Unknown author') AS author_name
         FROM blog_posts bp
         LEFT JOIN userdata u ON u.id = bp.user_id
         WHERE bp.user_id = ?
         ORDER BY bp.created_at DESC"
    );

    $stmt->bind_param('i', $_SESSION['user_id']);
} else {
    $stmt = $linkConnect->prepare(
        "SELECT bp.id, bp.title, bp.excerpt, bp.content, bp.cover_image, bp.status, bp.view_count, bp.created_at,
                COALESCE(u.username, 'Unknown author') AS author_name
         FROM blog_posts bp
         LEFT JOIN userdata u ON u.id = bp.user_id
         WHERE bp.status = 'published'
         ORDER BY bp.created_at DESC"
    );
}

if (!$stmt) {
    sendJsonResponse(500, 'Could not prepare blog posts query: ' . $linkConnect->error);
}

$stmt->execute() || sendJsonResponse(500, 'Could not load blog posts: ' . $stmt->error);
$result = $stmt->get_result();

if (!$result) {
    sendJsonResponse(500, 'Could not read blog posts result: ' . $stmt->error);
}

while ($row = $result->fetch_assoc()) {
    $posts[] = $row;
}

echo json_encode($posts);
