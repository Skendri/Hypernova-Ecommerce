<?php
session_start();

header('Content-Type: application/json');

include __DIR__ . '/../config/database.php';

function sendJsonResponse(int $statusCode, string $message, array $extra = []): void
{
    http_response_code($statusCode);
    echo json_encode(array_merge(['message' => $message], $extra));
    exit();
}

if (!isset($_SESSION['user_id'])) {
    sendJsonResponse(401, 'Please log in before publishing a blog post.');
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

$userId = (int) $_SESSION['user_id'];
$title = trim($_POST['title'] ?? '');
$excerpt = trim($_POST['excerpt'] ?? '');
$content = trim($_POST['content'] ?? '');
$status = $_POST['status'] ?? 'published';
$allowedStatuses = ['draft', 'published'];
$coverImagePath = null;

if ($title === '' || strlen($title) > 160) {
    sendJsonResponse(400, 'Title is required and must be 160 characters or fewer.');
}

if ($excerpt === '' || strlen($excerpt) > 255) {
    sendJsonResponse(400, 'Excerpt is required and must be 255 characters or fewer.');
}

if (strlen($content) < 50) {
    sendJsonResponse(400, 'Post content must be at least 50 characters.');
}

if (!in_array($status, $allowedStatuses, true)) {
    $status = 'published';
}

if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($_FILES['cover_image']['error'] !== UPLOAD_ERR_OK) {
        sendJsonResponse(400, 'The cover image could not be uploaded.');
    }

    if ($_FILES['cover_image']['size'] > 3 * 1024 * 1024) {
        sendJsonResponse(400, 'Cover image must be 3MB or smaller.');
    }

    $fileInfo = pathinfo($_FILES['cover_image']['name']);
    $extension = strtolower($fileInfo['extension'] ?? '');
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (!in_array($extension, $allowedExtensions, true)) {
        sendJsonResponse(400, 'Only JPG, PNG, GIF, and WEBP cover images are allowed.');
    }

    $uploadDir = __DIR__ . '/../assets/uploads/blog';

    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0777, true)) {
            sendJsonResponse(500, 'Could not create the blog uploads folder.');
        }
    }

    $imageName = time() . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
    $imageTarget = $uploadDir . '/' . $imageName;

    if (!move_uploaded_file($_FILES['cover_image']['tmp_name'], $imageTarget)) {
        sendJsonResponse(500, 'Could not save the cover image.');
    }

    $coverImagePath = '../assets/uploads/blog/' . $imageName;
}

$stmt = $linkConnect->prepare(
    "INSERT INTO blog_posts (user_id, title, excerpt, content, cover_image, status)
     VALUES (?, ?, ?, ?, ?, ?)"
);

if (!$stmt) {
    sendJsonResponse(500, 'Could not prepare blog post insert: ' . $linkConnect->error);
}

$stmt->bind_param('isssss', $userId, $title, $excerpt, $content, $coverImagePath, $status);

if (!$stmt->execute()) {
    sendJsonResponse(500, 'Could not save blog post: ' . $stmt->error);
}

echo json_encode([
    'message' => $status === 'draft' ? 'Draft saved successfully.' : 'Blog post published successfully.',
    'post_id' => $stmt->insert_id
]);
