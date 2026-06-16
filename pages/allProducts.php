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
    <link rel="stylesheet" href="../assets/css/allProducts.css">
    <title>All Products | Hypernova</title>
</head>

<body>
    <?php include '../components/navbar.php'; ?>

    <main class="all-products-page">
        <section class="all-products-hero">
            <div class="container">
                <p class="all-products-eyebrow">Hypernova Marketplace</p>
                <div class="all-products-heading">
                    <div>
                        <h1>All seller products</h1>
                        <p>Browse every product published by sellers and open any product to view details or buy.</p>
                    </div>
                    <a class="btn btn-outline-primary" href="home.php">Back home</a>
                </div>
            </div>
        </section>

        <section class="container py-5">
            <div id="all-products" class="row g-4" aria-live="polite">
                <div class="col-12">
                    <div class="all-products-empty">Loading products...</div>
                </div>
            </div>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous">
    </script>
    <script src="../assets/js/allProducts.js"></script>
</body>

</html>