<?php
session_start();

header('Content-Type: application/json');

include __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['message' => 'Please log in before publishing a blog post.']);
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

$linkConnect->query($createTableSql);

$userId = (int) $_SESSION['user_id'];
$title = trim($_POST['title'] ?? '');
$excerpt = trim($_POST['excerpt'] ?? '');
$content = trim($_POST['content'] ?? '');
$status = $_POST['status'] ?? 'published';
$allowedStatuses = ['draft', 'published'];
$coverImagePath = null;

if ($title === '' || strlen($title) > 160) {
    http_response_code(400);
    echo json_encode(['message' => 'Title is required and must be 160 characters or fewer.']);
    exit();
}

if ($excerpt === '' || strlen($excerpt) > 255) {
    http_response_code(400);
    echo json_encode(['message' => 'Excerpt is required and must be 255 characters or fewer.']);
    exit();
}

if (strlen($content) < 50) {
    http_response_code(400);
    echo json_encode(['message' => 'Post content must be at least 50 characters.']);
    exit();
}

if (!in_array($status, $allowedStatuses, true)) {
    $status = 'published';
}

if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($_FILES['cover_image']['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo json_encode(['message' => 'The cover image could not be uploaded.']);
        exit();
    }

    if ($_FILES['cover_image']['size'] > 3 * 1024 * 1024) {
        http_response_code(400);
        echo json_encode(['message' => 'Cover image must be 3MB or smaller.']);
        exit();
    }

    $fileInfo = pathinfo($_FILES['cover_image']['name']);
    $extension = strtolower($fileInfo['extension'] ?? '');
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (!in_array($extension, $allowedExtensions, true)) {
        http_response_code(400);
        echo json_encode(['message' => 'Only JPG, PNG, GIF, and WEBP cover images are allowed.']);
        exit();
    }

    $uploadDir = __DIR__ . '/../assets/uploads/blog';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $imageName = time() . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
    $imageTarget = $uploadDir . '/' . $imageName;

    if (!move_uploaded_file($_FILES['cover_image']['tmp_name'], $imageTarget)) {
        http_response_code(500);
        echo json_encode(['message' => 'Could not save the cover image.']);
        exit();
    }

    $coverImagePath = '../assets/uploads/blog/' . $imageName;
}

$stmt = $linkConnect->prepare(
    "INSERT INTO blog_posts (user_id, title, excerpt, content, cover_image, status)
     VALUES (?, ?, ?, ?, ?, ?)"
);

$stmt->bind_param('isssss', $userId, $title, $excerpt, $content, $coverImagePath, $status);
$stmt->execute();

echo json_encode([
    'message' => $status === 'draft' ? 'Draft saved successfully.' : 'Blog post published successfully.',
    'post_id' => $stmt->insert_id
]);
