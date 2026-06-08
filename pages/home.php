<?php

session_start();

require __DIR__ . "/../config/database.php";

if (isset($_SESSION["user_id"])) {

    $sql = "SELECT * FROM userdata
            WHERE id = {$_SESSION["user_id"]}";

    $result = $linkConnect->query($sql);

    $user = $result->fetch_assoc();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/home.css">
    <title>Dashboard</title>
</head>

<body>
    <?php include '../components/navbar.php'; ?>

    <!-- fillimi i kontentit te faqes -->
    <div>
        <h2 class="p-4 mt-5">Welcome To Dashboard <?php echo  htmlspecialchars($user["username"], ENT_QUOTES, 'UTF-8'); ?> </h2>
    </div>

    <div id="news-post"></div>

    <div class="container my-5">
        <div class="home-products-heading">
            <div>
                <p class="home-products-eyebrow">Marketplace</p>
                <h3>Products from sellers</h3>
            </div>
            <a class="btn btn-primary" href="allProducts.php">See all products</a>
        </div>
        <div id="user-products" class="row g-4"></div>
        <div class="home-products-heading">
            <div></div>
            <a class="btn btn-primary" href="./sellProduct.php">go to create products</a>
        </div>
    </div>

    <div class="container my-5">
        <div id="api-anotherPage"></div>
    </div>

    <div class="container my-5">
        <div id="apple-api"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous">
    </script>
    <script src="../assets/js/home.js?v=blog-posts-2"></script>
</body>

</html>