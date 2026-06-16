<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/pricing.css">
    <title>Publish Blog Post</title>
</head>

<body>
    <?php include '../components/navbar.php'; ?>

    <main class="blog-page">
        <section class="blog-hero">
            <div class="container">
                <div class="row align-items-end g-4">
                    <div class="col-lg-7">
                        <p class="eyebrow">Hypernova Journal</p>
                        <h1>Publish blog posts</h1>
                        <p class="hero-copy">
                            Write announcements, product stories, guides, and updates for your buyers.
                        </p>
                    </div>
                    <div class="col-lg-5">
                        <div class="hero-stats" aria-label="Blog publishing summary">
                            <div>
                                <span id="postCount">0</span>
                                <small>Your posts</small>
                            </div>
                            <div>
                                <span>3MB</span>
                                <small>Max cover image</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="container blog-workspace">
            <div class="row g-4 align-items-start">
                <div class="col-lg-5">
                    <form id="blogForm" class="blog-form" enctype="multipart/form-data">
                        <div class="form-heading">
                            <p class="eyebrow">Create</p>
                            <h2>New post</h2>
                        </div>

                        <div id="formMessage" class="form-message" role="status" aria-live="polite"></div>

                        <div class="mb-3">
                            <label class="form-label" for="title">Title</label>
                            <input type="text" class="form-control" id="title" name="title" maxlength="160" required>
                            <div class="field-counter"><span id="titleCount">0</span>/160</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="excerpt">Short excerpt</label>
                            <textarea class="form-control" id="excerpt" name="excerpt" rows="3" maxlength="255" required></textarea>
                            <div class="field-counter"><span id="excerptCount">0</span>/255</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="content">Post content</label>
                            <textarea class="form-control post-content" id="content" name="content" rows="9" required></textarea>
                            <div class="form-text">Minimum 50 characters.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="coverImage">Cover image</label>
                            <input type="file" class="form-control" id="coverImage" name="images[]" accept="image/jpeg,image/png,image/gif,image/webp" multiple>
                            <div class="form-text">JPG, PNG, GIF, or WEBP. Maximum 3MB.</div>
                        </div>

                        <div id="coverPreview" class="cover-preview" hidden></div>

                        <div class="publish-row">
                            <select class="form-select" name="status" id="status" aria-label="Post status">
                                <option value="published">Publish now</option>
                                <option value="draft">Save draft</option>
                            </select>
                            <button class="btn publish-btn" type="submit" id="publishButton">
                                Publish Post
                            </button>
                        </div>
                    </form>
                </div>

                <div class="col-lg-7">
                    <div class="posts-panel">
                        <div class="posts-header">
                            <div>
                                <p class="eyebrow">Manage</p>
                                <h2>Your posts</h2>
                            </div>
                            <button class="btn refresh-btn" type="button" id="refreshPosts">
                                Refresh
                            </button>
                        </div>

                        <div id="postsGrid" class="posts-grid"></div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script src="../assets/js/pricing.js?v=blog-posts-2"></script>
</body>

</html>