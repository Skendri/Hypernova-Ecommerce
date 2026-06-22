<?php

function ensureBlogPostsTable(mysqli $linkConnect): void
{
    $createTableSql = "
        CREATE TABLE IF NOT EXISTS blog_posts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            title VARCHAR(160) NOT NULL,
            excerpt VARCHAR(255) NOT NULL,
            content TEXT NOT NULL,
            cover_image VARCHAR(255) DEFAULT NULL,
            status ENUM('draft', 'published') NOT NULL DEFAULT 'published',
            view_count INT UNSIGNED NOT NULL DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_blog_posts_user_id (user_id),
            INDEX idx_blog_posts_status_created_at (status, created_at)
        )
    ";

    if (!$linkConnect->query($createTableSql)) {
        throw new RuntimeException('Could not create blog_posts table: ' . $linkConnect->error);
    }

    $columnResult = $linkConnect->query("SHOW COLUMNS FROM blog_posts LIKE 'view_count'");

    if (!$columnResult) {
        throw new RuntimeException('Could not inspect blog_posts table: ' . $linkConnect->error);
    }

    if ($columnResult->num_rows === 0) {
        $alterSql = "ALTER TABLE blog_posts ADD view_count INT UNSIGNED NOT NULL DEFAULT 0 AFTER status";

        if (!$linkConnect->query($alterSql)) {
            throw new RuntimeException('Could not add blog post view counter: ' . $linkConnect->error);
        }
    }
}
