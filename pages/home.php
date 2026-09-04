<?php

require __DIR__ . "/../config/database.php";
session_start();

if (isset($_SESSION["user_id"])) {

    $stmt = $linkConnect->prepare(
        "SELECT * FROM userdata WHERE id = ?"
    );

    $stmt->bind_param("i", $_SESSION["user_id"]);
    $stmt->execute();

    $result = $stmt->get_result();

    $user = $result->fetch_assoc();

    if (!$user) {
        // removes all session variables before destroying the session
        session_unset();
        session_destroy();
        header("Location: ../login.php");
        exit();
    }

    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/home.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <title>Dashboard</title>
</head>

<body>
    <?php include '../components/navbar.php'; ?>

    <!-- product for selling -->
    <div class="container my-5">
        <div class="bg-primary position-absolute top-1 rounded animate__animated animate__delay-1s animate__backInLeft" style="width: 68%; height: 1%; left: 304px;"></div>
        <div class="home-products-heading animate__animated animate__delay-1s animate__backInLeft">
            <div class="my-2">
                <p class="home-products-eyebrow">Marketplace</p>
                <h3>Products from sellers</h3>
            </div>
            <a class="btn btn-primary" href="allProducts.php">See all products</a>
        </div>
        <div id="user-products" class="row g-4"></div>
        <div class="home-products-heading animate__animated animate__delay-1s animate__backInLeft">
            <div></div>
            <a class="btn btn-outline-primary text-white" href="./sellProduct.php">create product</a>
        </div>
    </div>

    <!-- blog posts -->
    <section class="container my-5" id="news-post">
        <div class="bg-success position-absolute top-1 rounded animate__animated animate__delay-1s animate__backInLeft" style="width: 68%; height: 1%; left: 304px;"></div>
        <div class="home-blog-heading animate__animated animate__delay-1s animate__backInLeft">
            <div class="my-2">
                <p class="home-blog-eyebrow">Latest posts</p>
                <h3>News from Albania</h3>
            </div>
            <div class="home-blog-actions">
                <a class="btn btn-success" href="allBlogPost.php">See all posts</a>
            </div>
        </div>
        <div class="home-blog-grid" id="home-blog-grid">
            <div class="home-blog-empty">Loading posts...</div>
        </div>
        <div class="home-blog-heading animate__animated animate__delay-1s animate__backInLeft">
            <div></div>
            <div class="home-blog-actions">
                <a class="btn btn-outline-success" href="pricing.php">Create post</a>
            </div>
        </div>
    </section>

    <!-- news around the world -->
    <div class="container my-5">
        <div class="position-absolute top-1 rounded" style="width: 68%; height: 1%; left: 304px; background-color: #b45309;"></div>
        <div id="api-anotherPage"></div>
    </div>

    <!-- news from Apple -->
    <div class="container my-5">
        <div class="bg-warning position-absolute top-1 rounded" style="width: 68%; height: 1%; left: 304px;"></div>
        <div id="apple-api"></div>
    </div>

    <?php include '../components/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous">
    </script>
    <script src="../assets/js/home.js?v=marketplace-status-1"></script>
</body>

</html>