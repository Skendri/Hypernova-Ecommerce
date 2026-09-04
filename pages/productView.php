<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/productView.css">
    <title>Product Details | Hypernova</title>
</head>

<body>
    <?php include '../components/navbar.php'; ?>

    <main class="product-page">
        <a class="btn btn-outline-primary position-fixed" href="allProducts.php">Back home</a>
        <section class="product-shell" aria-live="polite">
            <div class="product-gallery reveal">
                <div class="gallery-stage">
                    <span class="product-badge" id="productCategory">Featured</span>
                    <button class="gallery-control previous" type="button" aria-label="Previous product image">
                        <span>&lsaquo;</span>
                    </button>
                    <img id="mainProductImage" src="https://placehold.co/900x900?text=Product" alt="Product image">
                    <button class="gallery-control next" type="button" aria-label="Next product image">
                        <span>&rsaquo;</span>
                    </button>
                </div>

                <div class="thumbnail-strip" id="thumbnailStrip"></div>
            </div>

            <div class="product-info reveal delay-1">
                <div class="eyebrow">Hypernova Market</div>
                <h1 id="productTitle">Premium Wireless Headphones</h1>

                <div class="rating-row">
                    <span class="rating-score">4.8</span>
                    <span class="stars" aria-label="Rated 4.8 out of 5">★★★★★</span>
                    <a href="#reviews">Ratings & Reviews</a>
                    <span class="soft-divider"></span>
                    <span>223 views</span>
                    <span>120 likes</span>
                </div>

                <div class="price-panel">
                    <span class="price-label">Special Price</span>
                    <div>
                        <strong id="productPrice">$180.00</strong>
                        <span class="old-price" id="oldPrice">$250.00</span>
                        <span class="discount-chip">28% Off</span>
                    </div>
                </div>

                <div class="product-description" id="productDescription">
                    Clean sound, soft cushions, and all-day battery life made for work, travel, and everyday listening.
                </div>

                <div class="availability-card">
                    <div>
                        <h2>Check Availability</h2>
                        <p>Standard delivery in 3-7 days.</p>
                    </div>
                    <form class="pincode-form" id="pincodeForm">
                        <input type="text" inputmode="numeric" maxlength="6" placeholder="Enter pincode" aria-label="Enter pincode">
                        <button type="submit">Check</button>
                    </form>
                    <small id="deliveryMessage">Enter pincode for exact details.</small>
                </div>

                <div class="option-grid">
                    <div>
                        <h2>Choose size</h2>
                        <div class="size-options" role="group" aria-label="Choose size">
                            <button type="button">S</button>
                            <button type="button" class="active">M</button>
                            <button type="button">L</button>
                        </div>
                    </div>

                    <div>
                        <h2>Choose Color</h2>
                        <div class="color-options" role="group" aria-label="Choose color">
                            <button type="button" class="color-choice" style="--swatch:#f4f4f2" aria-label="Pearl"></button>
                            <button type="button" class="color-choice" style="--swatch:#8d6757" aria-label="Mocha"></button>
                            <button type="button" class="color-choice active" style="--swatch:#48a9c5" aria-label="Sky"></button>
                            <button type="button" class="color-choice" style="--swatch:#1f7a3c" aria-label="Forest"></button>
                            <button type="button" class="color-choice" style="--swatch:#33343a" aria-label="Graphite"></button>
                        </div>
                    </div>
                </div>

                <div class="quantity-row">
                    <span>Quantity</span>
                    <div class="quantity-control" aria-label="Choose quantity">
                        <button type="button" id="decreaseQuantity">-</button>
                        <strong id="quantityValue">1</strong>
                        <button type="button" id="increaseQuantity">+</button>
                    </div>
                </div>

                <div class="action-row">
                    <button class="cart-button" type="button" id="addToCart">Add To Cart</button>
                    <button class="buy-button" type="button" id="buyNow">Buy Now</button>
                </div>
            </div>
        </section>

        <section class="details-band reveal delay-2">
            <article>
                <h2>Product Details</h2>
                <div id="productLongDescription">
                    Built for daily use with premium materials, smooth controls, and a checkout-ready shopping flow.
                </div>
            </article>
            <article>
                <h2>Why shoppers like it</h2>
                <ul>
                    <li>Fast product preview with multiple photos.</li>
                    <li>Clear price, discount, and availability information.</li>
                    <li>Responsive layout for phone, tablet, and desktop.</li>
                </ul>
            </article>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/productView.js"></script>
</body>

</html>