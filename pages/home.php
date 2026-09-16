<?php

require __DIR__ . "/../config/database.php";
session_start();

if (isset($_SESSION["user_id"])) {

    $stmt = $linkConnect->prepare(
        "SELECT * FROM userdata WHERE id = ?"
    );

    $stmt->bind_param("i", $_SESSION["user_id"]);
    $stmt->execute();

    $result = $stmt->get_result();

    $user = $result->fetch_assoc();

    if (!$user) {
        // removes all session variables before destroying the session
        session_unset();
        session_destroy();
        header("Location: ../login.php");
        exit();
    }

    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/home.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <title>Hypernova | Daily precision, thoughtfully made</title>
</head>

<body>
    <?php include '../components/navbar.php'; ?>

    <main class="home-shell">
        <section class="home-section product-section" id="products-section">
            <div class="section-heading">
                <div>
                    <p class="section-kicker blue-kicker">Explore collection</p>
                    <h1>Products engineered for daily precision</h1>
                </div>
                <div class="section-actions" aria-label="Product categories">
                    <!-- ky vend eshte reservuar nese ne te ardhmen dua te shtoj nje element -->
                </div>
            </div>
            <div id="user-products" class="product-grid"></div>
            <a class="text-link" href="allProducts.php">View all products <span aria-hidden="true">&#8594;</span></a>
        </section>

        <section class="home-section stories-section" id="news-post">
            <div class="section-heading">
                <div>
                    <p class="section-kicker blue-kicker">The editorial &amp; community</p>
                    <h2>Creator stories &amp; case studies</h2>
                </div>
                <a class="text-link" href="allBlogPost.php">View all stories <span aria-hidden="true">&#8594;</span></a>
            </div>
            <div class="stories-layout" id="home-blog-grid">
                <div class="home-blog-empty">Loading posts...</div>
            </div>
            <a class="text-link" href="pricing.php">Create a post <span aria-hidden="true">&#8594;</span></a>
        </section>

        <section class="home-section pricing-section" id="pricing-section">
            <div class="pricing-intro">
                <p class="section-kicker blue-kicker">Flexible memberships</p>
                <h2>Choose your studio elevation tier</h2>
                <p>Scale seamlessly from individual creative setups to global studio deployments.</p>
            </div>
            <div class="pricing-grid">
                <article class="plan-card"><span class="plan-number">01</span><h3>Essentials</h3><p>For remote creators building their initial clean desk setup.</p><strong>$19 <small>/ month</small></strong><ul><li>Curated catalog access</li><li>1 year hardware warranty</li><li>Standard concierge support</li></ul><a href="pricing.php">Start free trial</a></article>
                <article class="plan-card featured-plan"><span class="plan-badge">Most popular</span><span class="plan-number">02</span><h3>Professional</h3><p>For power creators, designers, and boutique freelancers.</p><strong>$49 <small>/ month</small></strong><ul><li>Priority equipment drops</li><li>3 year extended warranty</li><li>24/7 dedicated concierge</li><li>Annual hardware refresh</li></ul><a href="pricing.php">Select professional</a></article>
                <article class="plan-card"><span class="plan-number">03</span><h3>Studio collective</h3><p>Tailored for creative agencies and collaborative studios.</p><strong>$129 <small>/ month</small></strong><ul><li>Multi-seat license</li><li>Custom room CAD modeling</li><li>Acoustic optimization audit</li></ul><a href="pricing.php">Contact sales</a></article>
                <article class="plan-card"><span class="plan-number">04</span><h3>Custom enterprise</h3><p>Full architectural rollout and customized hardware fleet.</p><strong>Custom <small>/ billed annually</small></strong><ul><li>Bespoke finish materials</li><li>On-site acoustic installation</li><li>Dedicated account executive</li></ul><a href="pricing.php">Inquire fleet</a></article>
            </div>
        </section>

        <section class="home-section carousel-section" id="api-anotherPage"></section>
        <section class="home-section carousel-section" id="apple-api"></section>
    </main>

    <?php include '../components/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous">
    </script>
    <script src="../assets/js/home.js?v=marketplace-status-1"></script>
</body>

</html>