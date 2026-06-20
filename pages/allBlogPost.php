<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/allBlogPost.css">
    <title>All Blog Posts | Hypernova</title>
</head>

<body>
    <?php include '../components/navbar.php'; ?>

    <main class="all-blog-page">
        <section class="all-blog-hero">
            <div class="container">
                <p class="all-blog-eyebrow">Hypernova Journal</p>
                <div class="all-blog-heading">
                    <div>
                        <h1>All blog posts</h1>
                        <p>Read every published post from sellers, including product stories, announcements, and updates.</p>
                    </div>
                    <div class="all-blog-actions">
                        <a class="btn btn-primary" href="pricing.php">Create post</a>
                        <a class="btn btn-outline-primary" href="home.php">Back home</a>
                    </div>
                </div>
            </div>
        </section>

        <section class="container py-5">
            <div id="all-blog-posts" class="all-blog-grid" aria-live="polite">
                <div class="all-blog-empty">Loading blog posts...</div>
            </div>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous">
    </script>
    <script src="../assets/js/allBlogPost.js?v=rich-posts-1"></script>
</body>

</html>
