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
    <link rel="stylesheet" href="../assets/css/sellProduct.css">
    <title>Edit Product | Hypernova</title>
</head>

<body>
    <?php include '../components/navbar.php'; ?>

    <div class="hero-section">
        <div class="container text-center">
            <h1 class="hero-title">Edit Product</h1>
            <p class="hero-subtitle">
                Update your listing details, images, and visibility.
            </p>
        </div>
    </div>

    <div class="container py-5">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-7">
                <div class="upload-card">
                    <h3 class="mb-4">Product Details</h3>

                    <form id="editProductForm" enctype="multipart/form-data">
                        <input type="hidden" name="id" id="productId">

                        <div class="mb-3">
                            <label class="form-label">Product Title</label>
                            <input type="text" class="form-control" name="title" id="productTitle" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select class="form-select" name="category" id="productCategory" required>
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
                            <input type="number" step="0.01" class="form-control" name="price" id="productPrice" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Listing Status</label>
                            <select class="form-select" name="status" id="productStatus" required>
                                <option value="active">Active</option>
                                <option value="draft">Draft</option>
                                <option value="hidden">Hidden</option>
                                <option value="sold">Sold</option>
                            </select>
                            <div class="form-text">Only active listings are visible to shoppers.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" name="phone" id="productPhone" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" id="editor" name="description" rows="5"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Replace Product Images</label>
                            <input type="file" class="form-control" name="images[]" id="imageInput" accept="image/*" multiple>
                            <div class="form-text">Optional. Choose up to 5 new images to replace the current images.</div>
                        </div>

                        <div id="previewContainer" class="preview-container mb-4"></div>

                        <div class="d-grid gap-2">
                            <button class="btn btn-primary w-100" type="submit">
                                Save Changes
                            </button>
                            <a class="btn btn-light" id="viewProductLink" href="dashboard.php">Back to Dashboard</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
    <script src="../assets/js/editProduct.js"></script>
</body>

</html>

