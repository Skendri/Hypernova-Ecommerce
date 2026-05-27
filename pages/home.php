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
        <h2 class="p-4 mt-5">Welcome To Dashboard <?php echo $_SESSION['email']; ?> </h2>
    </div>

    <div class="container my-5">
        <h3 class="mb-4">Products from sellers</h3>
        <div id="user-products" class="row g-4"></div>
    </div>

    <div class="container text-center">
        <div class="row wrapper row-cols-4">
            <!-- all cards here -->

            <!-- all cards here -->
        </div>
        <!-- butoni load more dhe spinner -->
        <button type="button" id="load-more" class="btn btn-outline-primary btn-lg">Load more...</button>
        <div class="text-center">

            <div class="visually-hidden spinner-border me-2 my-3" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous">
    </script>
    <script src="../assets/js/home.js"></script>
</body>

</html>
