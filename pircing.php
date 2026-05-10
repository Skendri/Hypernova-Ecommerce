<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}
include './components/navbar.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pricing - Sell Your Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="./css folder/pircing.css">
</head>

<body>
    <div class="hero-section">
        <div class="container">
            <h1 class="hero-title">Sell Your Product</h1>
            <p class="hero-subtitle">Upload images, add description, and list it instantly!</p>
        </div>
    </div>

    <div class="container my-5">
        <!-- Upload Form -->
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="upload-card">
                    <h3>Upload New Product</h3>
                    <form id="productForm">
                        <div class="mb-3">
                            <label for="title" class="form-label">Product Title</label>
                            <input type="text" class="form-control" id="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" rows="4" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="images" class="form-label">Product Images (Max 5, <2MB each)</label>
                                    <input type="file" class="form-control" id="images" accept="image/*" multiple>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">List Product</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <h2 class="my-5 text-center">Your Listed Products</h2>
        <div id="productsGrid" class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-4">
            <!-- Products rendered by JS -->
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="./js folder/pircing.js"></script>
</body>

</html>