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
        <form id="productFilters">
            <div class="catalog-layout">
                <aside class="catalog-sidebar" id="catalogSidebar">
                    <section class="filter-panel">
                        <div class="filter-panel-heading"><h2>Product Categories</h2><span id="productCount">All</span></div>
                        <nav class="category-list" aria-label="Product categories">
                            <button class="category-option is-active" type="button" data-category=""><span>All products</span><span>All</span></button>
                            <button class="category-option" type="button" data-category="Electronics"><span>Electronics</span><span>--</span></button>
                            <button class="category-option" type="button" data-category="Fashion"><span>Fashion</span><span>--</span></button>
                            <button class="category-option" type="button" data-category="Gaming"><span>Gaming</span><span>--</span></button>
                            <button class="category-option" type="button" data-category="Home"><span>Home</span><span>--</span></button>
                            <button class="category-option" type="button" data-category="Sports"><span>Sports</span><span>--</span></button>
                        </nav>
                    </section>
                    <section class="filter-panel">
                        <div class="filter-panel-heading"><h2>Pricing</h2><span>$</span></div>
                        <div class="price-inputs">
                            <label>Min <input type="number" id="minPrice" name="min_price" min="0" step="0.01" placeholder="0"></label>
                            <label>Max <input type="number" id="maxPrice" name="max_price" min="0" step="0.01" placeholder="500"></label>
                        </div>
                        <div class="price-rule"><span></span></div><div class="price-range-labels"><span>$0</span><span>$500+</span></div>
                    </section>
                    <section class="filter-panel"><div class="filter-panel-heading"><h2>Item Size</h2></div><div class="size-options"><span>XS</span><span>S</span><span class="is-selected">M</span><span>L</span><span>XL</span><span>XXL</span></div></section>
                    <section class="filter-panel"><div class="filter-panel-heading"><h2>Color</h2></div><div class="color-options" aria-label="Available colors"><span class="color-swatch is-selected" style="--swatch:#ed562d"></span><span class="color-swatch" style="--swatch:#ef82a8"></span><span class="color-swatch" style="--swatch:#cf1d60"></span><span class="color-swatch" style="--swatch:#5b37a9"></span><span class="color-swatch" style="--swatch:#1d211f"></span><span class="color-swatch" style="--swatch:#008e83"></span><span class="color-swatch" style="--swatch:#9bd3a5"></span><span class="color-swatch" style="--swatch:#419de7"></span><span class="color-swatch" style="--swatch:#7952bd"></span><span class="color-swatch" style="--swatch:#f9cf28"></span></div></section>
                    <section class="filter-panel"><div class="filter-panel-heading"><h2>Brand</h2></div><div class="brand-options"><span>Nike</span><span>Adidas</span><span>Denim</span><span>Puma</span><span>Gucci</span></div></section>
                </aside>
                <section class="catalog-content">
                    <div class="catalog-toolbar">
                        <label class="catalog-search" for="productSearch"><i class="fa-solid fa-magnifying-glass"></i><input type="search" id="productSearch" name="q" placeholder="Search for products"></label>
                        <div class="toolbar-actions"><button class="toolbar-button mobile-filter-button" id="toggleFilters" type="button"><i class="fa-solid fa-sliders"></i> Filter</button><button class="toolbar-button" type="submit"><span>Sort by</span><i class="fa-solid fa-chevron-down"></i></button></div>
                    </div>
                    <div class="catalog-heading"><div><p class="all-products-eyebrow">Hypernova Marketplace</p><h1>All seller products</h1></div><button class="clear-filters" id="clearFilters" type="button">Clear filters</button></div>
                    <div id="all-products" class="product-grid" aria-live="polite"><div class="all-products-empty">Loading products...</div></div>
                </section>
            </div>
            <select class="visually-hidden" id="productCategory" name="category" aria-label="Product category"><option value="">All categories</option><option value="Electronics">Electronics</option><option value="Fashion">Fashion</option><option value="Gaming">Gaming</option><option value="Home">Home</option><option value="Sports">Sports</option></select>
        </form>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous">
    </script>
    <script src="../assets/js/allProducts.js"></script>
</body>

</html>