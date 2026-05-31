<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

$email = $_SESSION['email'] ?? 'Seller';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <title>Seller Dashboard | Hypernova</title>
</head>

<body>
    <?php include '../components/navbar.php'; ?>

    <main class="dashboard-page">
        <section class="dashboard-hero">
            <div class="container">
                <div class="hero-layout">
                    <div>
                        <p class="eyebrow">Seller Dashboard</p>
                        <h1>Manage products and track performance.</h1>
                        <p class="hero-copy">Welcome back, <?php echo htmlspecialchars($email); ?>.</p>
                    </div>
                    <a class="btn btn-light dashboard-action" href="sellProduct.php">Add Product</a>
                </div>
            </div>
        </section>

        <section class="container dashboard-content">
            <div class="metrics-grid" aria-live="polite">
                <article class="metric-card">
                    <span>Total Products</span>
                    <strong id="totalProducts">0</strong>
                </article>
                <article class="metric-card">
                    <span>Catalog Value</span>
                    <strong id="totalValue">$0.00</strong>
                </article>
                <article class="metric-card">
                    <span>Average Price</span>
                    <strong id="averagePrice">$0.00</strong>
                </article>
                <article class="metric-card">
                    <span>Latest Listing</span>
                    <strong id="latestListing">-</strong>
                </article>
            </div>

            <div class="analytics-grid">
                <section class="dashboard-panel">
                    <div class="panel-heading">
                        <h2>Category Mix</h2>
                        <span id="categoryCount">0 categories</span>
                    </div>
                    <div class="category-bars" id="categoryBars"></div>
                </section>

                <section class="dashboard-panel">
                    <div class="panel-heading">
                        <h2>Listings By Month</h2>
                        <span>Recent activity</span>
                    </div>
                    <div class="month-chart" id="monthChart"></div>
                </section>
            </div>

            <section class="dashboard-panel product-manager">
                <div class="manager-heading">
                    <div>
                        <h2>Your Products</h2>
                        <p>Search, review, open, or remove your listings.</p>
                    </div>
                    <input type="search" class="form-control" id="productSearch" placeholder="Search products">
                </div>

                <div class="table-responsive">
                    <table class="table align-middle product-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Posted</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="productRows"></tbody>
                    </table>
                </div>

                <div class="empty-dashboard" id="emptyDashboard">
                    <h3>No products yet</h3>
                    <p>Publish your first item to start building analytics.</p>
                    <a class="btn btn-primary" href="sellProduct.php">Publish Product</a>
                </div>
            </section>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/dashboard.js"></script>
</body>

</html>
