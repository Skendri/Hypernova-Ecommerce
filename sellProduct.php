<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./css folder/sellProduct.css">
    <title>Sell Product</title>
</head>

<body>
    <?php include './components/navbar.php'; ?>

    <div class="hero-section">
        <div class="container text-center">
            <h1 class="hero-title">Sell Your Product</h1>
            <p class="hero-subtitle">
                Upload products and start selling instantly.
            </p>
        </div>
    </div>

    <div class="container py-5">

        <div class="row justify-content-center mb-5">
            <div class="col-lg-7">

                <div class="upload-card">

                    <h3 class="mb-4">Upload Product</h3>

                    <form id="productForm" enctype="multipart/form-data">

                        <div class="mb-3">
                            <label class="form-label">Product Title</label>
                            <input type="text" class="form-control" name="title" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Category</label>

                            <select class="form-select" name="category" required>
                                <option value="">Select category</option>
                                <option>Electronics</option>
                                <option>Fashion</option>
                                <option>Gaming</option>
                                <option>Home</option>
                                <option>Sports</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Price</label>
                            <input type="number" step="0.01" class="form-control" name="price" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" name="phone" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="5" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Product Images</label>
                            <input type="file" class="form-control" name="images[]" id="imageInput" accept="image/*" multiple required>
                            <div class="form-text">Choose up to 5 images.</div>
                        </div>

                        <div id="previewContainer" class="preview-container mb-4"></div>

                        <button class="btn btn-primary w-100" type="submit">
                            Publish Product
                        </button>

                    </form>

                </div>

            </div>
        </div>


        <h2 class="section-title mb-4">Your Products</h2>

        <div class="row g-4" id="productsGrid"></div>

    </div>

    <script src="./js folder/sellProduct.js"></script>

</body>

</html>
