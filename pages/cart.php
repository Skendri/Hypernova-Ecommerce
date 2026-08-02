<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/cart.css">
    <title>Your Cart | Hypernova</title>
</head>
<body>
    <?php include '../components/navbar.php'; ?>
    <main class="container py-5 cart-page">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div><h1>Your cart</h1><p class="text-muted mb-0">Review your items before checkout.</p></div>
            <a class="btn btn-outline-primary" href="allProducts.php">Continue shopping</a>
        </div>
        <div class="row g-4">
            <section class="col-lg-8" id="cartItems" aria-live="polite"></section>
            <aside class="col-lg-4"><div class="card shadow-sm border-0"><div class="card-body">
                <h2 class="h4">Order summary</h2>
                <div class="d-flex justify-content-between mt-3"><span>Items</span><span id="cartQuantity">0</span></div>
                <div class="d-flex justify-content-between border-top pt-3 mt-3 fw-bold fs-5"><span>Total</span><span id="cartTotal">$0.00</span></div>
                <button class="btn btn-primary w-100 mt-4" id="checkoutButton" disabled>Checkout</button>
                <small class="text-muted d-block mt-3">Checkout and payments can be connected next.</small>
            </div></div></aside>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/cart.js"></script>
</body>
</html>
