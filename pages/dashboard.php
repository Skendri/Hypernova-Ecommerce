<?php
session_start();

require __DIR__ . "/../config/database.php";

$stmt = $linkConnect->prepare(
    "SELECT username FROM userdata WHERE id = ?"
);
$stmt->bind_param("i", $_SESSION["user_id"]);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

    if (!$user) {
        // removes all session variables before destroying the session
        session_unset();
        session_destroy();
        header("Location: ../login.php");
        exit();
    }

    $stmt->close();

$email = $_SESSION['email'] ?? 'Seller';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <title>Seller Dashboard | Hypernova</title>
</head>

<body class="premium-dashboard-body">

    <?php include '../components/navbar.php'; ?>


    <div class="dashboard-shell">
        <aside class="dashboard-sidebar">
            <div class="sidebar-brand">
                <div class="brand-logo">
                    <i class="fa-solid fa-bolt"></i>
                </div>
                <div class="brand-copy">
                    <div class="brand-title"> <?php echo htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'); ?> </div>
                    <div class="brand-plan">Pro Plan</div>
                </div>
                <div class="brand-chevron">
                    <i class="fa-solid fa-chevron-down"></i>
                </div>
            </div>

            <nav class="sidebar-nav">
                <a class="sidebar-link active" href="#">
                    <span class="sidebar-icon"><i class="fa-solid fa-gauge-high"></i></span>
                    <span class="sidebar-label">Dashboard</span>
                </a>
                <a class="sidebar-link" href="#">
                    <span class="sidebar-icon"><i class="fa-solid fa-dollar-sign"></i></span>
                    <span class="sidebar-label">Sales</span>
                    <span class="sidebar-badge">3</span>
                </a>
                <a class="sidebar-link" href="../index.php">
                    <span class="sidebar-icon"><i class="fa-solid fa-laptop"></i></span>
                    <span class="sidebar-label">View Site</span>
                </a>
                <a class="sidebar-link" href="#">
                    <span class="sidebar-icon"><i class="fa-solid fa-box"></i></span>
                    <span class="sidebar-label">Products</span>
                </a>
                <a class="sidebar-link" href="#">
                    <span class="sidebar-icon"><i class="fa-solid fa-tags"></i></span>
                    <span class="sidebar-label">Tags</span>
                </a>
                <a class="sidebar-link" href="#">
                    <span class="sidebar-icon"><i class="fa-solid fa-chart-column"></i></span>
                    <span class="sidebar-label">Analytics</span>
                </a>
                <a class="sidebar-link" href="#">
                    <span class="sidebar-icon"><i class="fa-solid fa-users"></i></span>
                    <span class="sidebar-label">Members</span>
                    <span class="sidebar-badge">12</span>
                </a>
            </nav>

            <div class="sidebar-section">
                <span class="sidebar-section-title">Account</span>
                <a class="sidebar-link" href="#">
                    <span class="sidebar-icon"><i class="fa-solid fa-gear"></i></span>
                    <span class="sidebar-label">Settings</span>
                </a>
                <a class="sidebar-link" href="#">
                    <span class="sidebar-icon"><i class="fa-solid fa-circle-question"></i></span>
                    <span class="sidebar-label">Help &amp; Support</span>
                </a>
            </div>

            <div class="sidebar-footer">
                <button class="sidebar-toggle" id="sidebar-toggle-btn">
                    <span class="collapse-icon">«</span>
                    <span class="sidebar-label">Hide</span>
                </button>
            </div>
        </aside>

        <main class="dashboard-main">
            <header class="dashboard-topbar">
                <div class="topbar-title">
                    <h1>Dashboard</h1>
                    <p>Welcome back to your dashboard</p>
                </div>
                <div class="topbar-actions">
                    <button class="topbar-icon">
                        <i class="fa-solid fa-bell"></i>
                    </button>
                    <button class="topbar-icon">
                        <i class="fa-solid fa-moon"></i>
                    </button>
                    <button class="topbar-icon">
                        <i class="fa-solid fa-user"></i>
                    </button>
                </div>
            </header>

            <section class="dashboard-content-dashboard">
                <div class="metrics-grid" aria-live="polite">
                    <article class="metric-card">
                        <div class="metric-card-head">
                            <span class="metric-icon"><i class="fa-solid fa-dollar-sign"></i></span>
                            <span class="metric-icon-arrow"><i class="fa-solid fa-arrow-trend-up"></i></span>
                        </div>
                        <span class="metric-label">Catalog Value</span>
                        <strong id="totalValue">$0.00</strong>
                        <span class="metric-subline">$0.00 current</span>
                    </article>
                    <article class="metric-card">
                        <div class="metric-card-head">
                            <span class="metric-icon users"><i class="fa-solid fa-users"></i></span>
                            <span class="metric-icon-arrow"><i class="fa-solid fa-arrow-trend-up"></i></span>
                        </div>
                        <span class="metric-label">Total Products</span>
                        <strong id="totalProducts">0</strong>
                        <span class="metric-subline">0 active</span>
                    </article>
                    <article class="metric-card">
                        <div class="metric-card-head">
                            <span class="metric-icon orders"><i class="fa-solid fa-bag-shopping"></i></span>
                            <span class="metric-icon-arrow"><i class="fa-solid fa-arrow-trend-up"></i></span>
                        </div>
                        <span class="metric-label">Average Price</span>
                        <strong id="averagePrice">$0.00</strong>
                        <span class="metric-subline">avg. listing</span>
                    </article>
                    <article class="metric-card">
                        <div class="metric-card-head">
                            <span class="metric-icon latest"><i class="fa-solid fa-box-open"></i></span>
                            <span class="metric-icon-arrow"><i class="fa-solid fa-arrow-trend-up"></i></span>
                        </div>
                        <span class="metric-label">Latest Listing</span>
                        <strong id="latestListing">-</strong>
                        <span class="metric-subline">newest post</span>
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
                                    <th>Status</th>
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/dashboard.js"></script>
</body>

</html>
