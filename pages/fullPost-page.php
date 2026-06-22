<?php
session_start();

include __DIR__ . '/../config/database.php';
include __DIR__ . '/../api/blog_post_schema.php';

try {
    ensureBlogPostsTable($linkConnect);
} catch (RuntimeException $error) {
    http_response_code(500);
    die($error->getMessage());
}

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function formatPostDate(?string $dateValue): string
{
    if (!$dateValue) {
        return '';
    }

    $timestamp = strtotime($dateValue);

    if (!$timestamp) {
        return $dateValue;
    }

    return date('F d, Y', $timestamp);
}

function normalizePostImage(?string $imagePath): string
{
    if (!$imagePath) {
        return 'https://placehold.co/1200x700?text=Blog+Post';
    }

    if (strpos($imagePath, 'http') === 0 || strpos($imagePath, 'data:') === 0) {
        return $imagePath;
    }

    $imagePath = preg_replace('/^uploads\//', '../assets/uploads/', $imagePath);
    $imagePath = preg_replace('/^assets\/uploads\//', '../assets/uploads/', $imagePath);

    return $imagePath;
}

$postId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$post = null;
$relatedPosts = [];

if ($postId) {
    $viewStmt = $linkConnect->prepare(
        "UPDATE blog_posts SET view_count = view_count + 1 WHERE id = ? AND status = 'published'"
    );

    if ($viewStmt) {
        $viewStmt->bind_param('i', $postId);
        $viewStmt->execute();
        $viewStmt->close();
    }

    $postStmt = $linkConnect->prepare(
        "SELECT bp.id, bp.title, bp.excerpt, bp.content, bp.cover_image, bp.view_count, bp.created_at,
                COALESCE(u.username, 'Unknown author') AS author_name
         FROM blog_posts bp
         LEFT JOIN userdata u ON u.id = bp.user_id
         WHERE bp.id = ? AND bp.status = 'published'
         LIMIT 1"
    );

    if ($postStmt) {
        $postStmt->bind_param('i', $postId);
        $postStmt->execute();
        $post = $postStmt->get_result()->fetch_assoc();
        $postStmt->close();
    }

    $relatedStmt = $linkConnect->prepare(
        "SELECT id, title, excerpt, cover_image, created_at
         FROM blog_posts
         WHERE status = 'published' AND id <> ?
         ORDER BY created_at DESC
         LIMIT 3"
    );

    if ($relatedStmt) {
        $relatedStmt->bind_param('i', $postId);
        $relatedStmt->execute();
        $relatedResult = $relatedStmt->get_result();

        while ($row = $relatedResult->fetch_assoc()) {
            $relatedPosts[] = $row;
        }

        $relatedStmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $post ? e($post['title']) . ' | Hypernova' : 'Post not found | Hypernova'; ?></title>
    <link rel="stylesheet" href="../assets/css/fullPost-page.css">
</head>
<body>

<main class="container">
    <a class="back-link" href="allBlogPost.php">Back to all posts</a>

    <?php if (!$post): ?>
        <section class="empty-post">
            <h1>Post not found</h1>
            <p>This post may have been removed, unpublished, or opened with the wrong link.</p>
        </section>
    <?php else: ?>
        <article class="featured-post">
            <img
                class="featured-image"
                src="<?php echo e(normalizePostImage($post['cover_image'])); ?>"
                alt="<?php echo e($post['title']); ?>"
            >

            <div class="featured-content">
                <span class="date">
                    <?php echo e(formatPostDate($post['created_at'])); ?>
                    <span><?php echo (int) $post['view_count']; ?> views</span>
                </span>

                <h1><?php echo e($post['title']); ?></h1>

                <p class="excerpt"><?php echo e($post['excerpt']); ?></p>
                <p class="author">By <?php echo e($post['author_name']); ?></p>
            </div>
        </article>

        <section class="post-content">
            <?php echo nl2br(e($post['content'])); ?>
        </section>

        <?php if (count($relatedPosts) > 0): ?>
            <section class="related-section">
                <h2>Latest posts</h2>

                <div class="blog-grid">
                    <?php foreach ($relatedPosts as $relatedPost): ?>
                        <a class="card-link" href="fullPost-page.php?id=<?php echo (int) $relatedPost['id']; ?>">
                            <article class="card">
                                <img
                                    class="card-image"
                                    src="<?php echo e(normalizePostImage($relatedPost['cover_image'])); ?>"
                                    alt="<?php echo e($relatedPost['title']); ?>"
                                >
                                <span class="date"><?php echo e(formatPostDate($relatedPost['created_at'])); ?></span>
                                <h3><?php echo e($relatedPost['title']); ?></h3>
                                <p><?php echo e($relatedPost['excerpt']); ?></p>
                            </article>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    <?php endif; ?>
</main>

</body>
</html>
