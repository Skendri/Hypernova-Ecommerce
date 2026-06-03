<?php
session_start();

header('Content-Type: application/json');

include __DIR__ . '/../config/database.php';

function sendJsonResponse(int $statusCode, string $message): void
{
    http_response_code($statusCode);
    echo json_encode(['message' => $message]);
    exit();
}

$createTableSql = "
    CREATE TABLE IF NOT EXISTS blog_posts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        title VARCHAR(160) NOT NULL,
        excerpt VARCHAR(255) NOT NULL,
        content TEXT NOT NULL,
        cover_image VARCHAR(255) DEFAULT NULL,
        status ENUM('draft', 'published') NOT NULL DEFAULT 'published',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_blog_posts_user_id (user_id),
        INDEX idx_blog_posts_status_created_at (status, created_at)
    )
";

if (!$linkConnect->query($createTableSql)) {
    sendJsonResponse(500, 'Could not create blog_posts table: ' . $linkConnect->error);
}

$scope = $_GET['scope'] ?? 'published';
$posts = [];

if ($scope === 'mine') {
    if (!isset($_SESSION['user_id'])) {
        sendJsonResponse(401, 'Please log in to view your posts.');
    }

    $stmt = $linkConnect->prepare(
        "SELECT bp.id, bp.title, bp.excerpt, bp.content, bp.cover_image, bp.status, bp.created_at,
                COALESCE(u.username, 'Unknown author') AS author_name
         FROM blog_posts bp
         LEFT JOIN userdata u ON u.id = bp.user_id
         WHERE bp.user_id = ?
         ORDER BY bp.created_at DESC"
    );

    $stmt->bind_param('i', $_SESSION['user_id']);
} else {
    $stmt = $linkConnect->prepare(
        "SELECT bp.id, bp.title, bp.excerpt, bp.content, bp.cover_image, bp.status, bp.created_at,
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
