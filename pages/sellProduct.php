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
    <title>Sell Product</title>
</head>

<body>
    <?php include '../components/navbar.php'; ?>

    <main class="studio-shell">
        <div class="studio-container">
            <section class="studio-header">
                <div>
                    <div class="eyebrow"><span class="live-dot"></span> Atelier suite <span>•</span> Direct merchant protocol</div>
                    <h1 class="hero-title">Creator &amp; Seller<br>Studio</h1>
                    <p class="hero-subtitle">Manage your listings, mint trust with every detail, and reach high-intent buyers.</p>
                </div>
                <div class="metric-ribbon" aria-label="Seller metrics">
                    <div class="metric-card"><span>Total listings</span><strong id="totalListings">0</strong><small>in your studio</small></div>
                    <div class="metric-card"><span>Active listings</span><strong id="activeListings">0</strong><small>live now</small></div>
                    <div class="metric-card"><span>Catalog value</span><strong id="catalogValue">$0</strong><small>current inventory</small></div>
                    <div class="metric-card"><span>Media slots</span><strong>5</strong><small>per listing</small></div>
                </div>
            </section>

            <nav class="studio-toolbar" aria-label="Seller studio navigation">
                <button class="studio-tab is-active" type="button" data-scroll-target="publish-panel"><i class="fa-solid fa-plus"></i> Publish new product</button>
                <button class="studio-tab" type="button" data-scroll-target="inventory-panel"><i class="fa-solid fa-boxes-stacked"></i> Manage inventory <b id="inventoryCount">0</b></button>
                <div class="category-rail" role="group" aria-label="Filter inventory by category">
                    <span>Filter:</span>
                    <button class="filter-chip is-active" type="button" data-category-filter="All">All</button>
                    <button class="filter-chip" type="button" data-category-filter="Electronics">Electronics</button>
                    <button class="filter-chip" type="button" data-category-filter="Fashion">Fashion</button>
                    <button class="filter-chip" type="button" data-category-filter="Gaming">Gaming</button>
                    <button class="filter-chip" type="button" data-category-filter="Home">Home</button>
                    <button class="filter-chip" type="button" data-category-filter="Sports">Sports</button>
                </div>
            </nav>

            <section class="studio-workspace">
                <div class="studio-form-column" id="publish-panel">
                    <div class="studio-card asset-card">
                        <div class="card-heading"><div><i class="fa-regular fa-images"></i><h2>Digital assets &amp; gallery</h2></div><span id="imageCount">0 / 5 slots filled</span></div>
                        <label class="upload-zone" for="imageInput">
                            <span class="upload-icon"><i class="fa-solid fa-cloud-arrow-up"></i></span>
                            <strong>Drop up to 5 product images</strong>
                            <small>JPG, PNG, GIF, or WEBP up to 5MB each</small>
                        </label>
                        <div id="previewContainer" class="preview-container"></div>
                    </div>

                    <div class="studio-card form-card">
                        <div class="card-heading"><div><i class="fa-solid fa-sliders"></i><h2>Listing parameters</h2></div><span class="secure-label"><i class="fa-solid fa-shield-halved"></i> Protected checkout</span></div>

                    <form id="productForm" enctype="multipart/form-data">

                        <div class="mb-3">
                            <label class="form-label" for="productTitle">Product title</label>
                            <input id="productTitle" type="text" class="form-control" name="title" placeholder="Name your product" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="productCategory">Marketplace segment</label>

                            <select id="productCategory" class="form-select" name="category" required>
                                <option value="">Select category</option>
                                <option>Electronics</option>
                                <option>Fashion</option>
                                <option>Gaming</option>
                                <option>Home</option>
                                <option>Sports</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="productPrice">List price</label>
                            <div class="price-input"><span>$</span><input id="productPrice" type="number" step="0.01" class="form-control" name="price" placeholder="0.00" min="0" required></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="productStatus">Listing visibility state</label>
                            <select id="productStatus" class="form-select" name="status" required>
                                <option value="active">Active</option>
                                <option value="draft">Draft</option>
                                <option value="hidden">Hidden</option>
                                <option value="sold">Sold</option>
                            </select>
                            <div class="form-text">Only active listings are visible to shoppers.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="productPhone">Verified courier dispatch line</label>
                            <input id="productPhone" type="tel" class="form-control" name="phone" placeholder="Your contact number" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="editor">Editorial specification &amp; narrative</label>
                            <textarea class="form-control" id="editor" name="description" rows="5"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="visually-hidden" for="imageInput">Product images</label>
                            <input class="visually-hidden" type="file" name="images[]" id="imageInput" accept="image/*" multiple required>
                        </div>

                        <div class="form-actions"><button class="btn btn-primary" type="submit"><i class="fa-solid fa-rocket"></i> Publish to marketplace</button><button class="draft-button" type="button" data-draft-button>Save draft</button></div>

                    </form>
                </div>
            </div>
                <aside class="live-preview-column">
                    <div class="preview-heading"><span><i class="fa-solid fa-circle"></i> Live shopper preview</span><small>Updates dynamically</small></div>
                    <div class="shopper-preview">
                        <div class="shopper-image-wrap"><img id="shopperPreviewImage" src="https://placehold.co/800x620/11131d/cbbeff?text=Your+Product" alt="Product preview"><span>LIVE LISTING</span></div>
                        <div class="shopper-copy"><span class="preview-category" id="shopperPreviewCategory">SELECT A CATEGORY</span><h3 id="shopperPreviewTitle">Your product title</h3><p id="shopperPreviewPrice">$0.00</p><div class="preview-meta"><span><i class="fa-solid fa-shield-halved"></i> Protected listing</span><span><i class="fa-solid fa-truck"></i> Ships worldwide</span></div></div>
                    </div>
                    <div class="preview-note"><i class="fa-regular fa-lightbulb"></i><span>Your listing preview helps buyers understand the product before they open the full detail page.</span></div>
                </aside>
            </section>

            <section class="inventory-section" id="inventory-panel">
                <div class="inventory-heading"><div><div class="eyebrow"><span class="live-dot"></span> Live inventory</div><h2>Your products inventory</h2><p>Real-time valuation and listing lifecycle controls.</p></div><div class="inventory-count"><strong id="inventoryHeadingCount">0</strong> active cards</div></div>
                <div class="row g-4" id="productsGrid"></div>
            </section>
        </div>
    </main>

    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
    <script src="../assets/js/sellProduct.js"></script>

</body>

</html>
