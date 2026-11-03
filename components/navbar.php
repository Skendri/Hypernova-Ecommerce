<?php

require __DIR__ . "/../config/database.php";
require_once __DIR__ . '/../includes/cart_helpers.php';

$stmt = $linkConnect->prepare(
    "SELECT username FROM userdata WHERE id = ?"
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

?>
<!-- css for navbar -->
<style>
    .glass-navbar {
        width: min(94%, 1280px);
        color: black;
        background: rgba(255, 255, 255, 0);
        border-radius: 16px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.18);
        backdrop-filter: blur(3px);
        -webkit-backdrop-filter: blur(9.1px);
        border: 1px solid rgba(255, 255, 255, 1);
        overflow: visible;
    }

    .navbar-size {
        display: flex;
        justify-content: center;
        position: sticky;
        top: 10px;
        z-index: 1000;
    }

    .glass-navbar .navbar-collapse {
        align-items: center;
        gap: 12px;
    }

    .glass-navbar .navbar-nav {
        gap: 2px;
    }

    .glass-navbar .nav-link {
        white-space: nowrap;
    }

    .glass-navbar form[role="search"] {
        flex: 1 1 180px;
        max-width: 260px;
        margin-left: auto;
        min-width: 160px;
    }

    .glass-navbar form[role="search"] input {
        min-width: 0;
    }

    .glass-navbar .dropdown-menu {
        left: auto;
        right: 0;
    }

    @media (max-width: 1199.98px) {
        .glass-navbar {
            width: min(96%, 1280px);
        }

        .glass-navbar .container {
            align-items: center;
        }

        .glass-navbar .navbar-collapse {
            align-items: stretch;
            margin-top: 12px;
            width: 100%;
        }

        .glass-navbar .navbar-nav {
            width: 100%;
        }

        .glass-navbar .nav-link {
            padding: 10px 12px;
        }

        .glass-navbar form[role="search"] {
            max-width: none;
            margin-left: 0;
            width: 100%;
        }

        .glass-navbar .dropdown {
            margin-top: 8px;
        }

        .glass-navbar .dropdown-menu {
            left: 0;
            right: auto;
            width: min(260px, 100%);
        }
    }

    @media (max-width: 575.98px) {
        .navbar-size {
            top: 6px;
        }

        .glass-navbar {
            width: calc(100% - 12px);
            border-radius: 12px;
        }

        .glass-navbar .container {
            padding-inline: 12px;
        }

        .glass-navbar form[role="search"] {
            display: grid !important;
            grid-template-columns: minmax(0, 1fr) auto;
        }
    }
</style>

<div class="navbar-size">
    <nav class="glass-navbar navbar py-3 navbar-expand-xl">
        <div class="container">
            <!-- Logo navbar -->
            <a class="navbar-brand" href="home.php">Hypernova</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#mainNavbar" aria-controls="mainNavbar"
                aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavbar">
                <!-- links navbar -->
                <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="home.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="feature.php">Features</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pricing.php">Pricing</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="sellProduct.php">sell products</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="productView.php">Product View</a>
                </li>
                </ul>

                <!-- search form -->
                <form class="d-flex" role="search">
                    <input name="search" class="form-control me-2" type="search" placeholder="Search" aria-label="Search" />
                    <button class="btn btn-outline-dark" type="submit">Search</button>
                </form>

                <!-- account and cart menu -->
                <div class="dropdown h-auto">
                <a class="nav-link dropdown-toggle d-flex align-items-center gap-2"
                    href="#"
                    role="button"
                    data-bs-toggle="dropdown">

                    <div class="">
                        <span class="position-relative badge rounded-pill text-bg-light text-primary" id="cartCount" style="bottom: 15px; left: 15px"><?php echo cart_item_count(); ?>
                        </span>
                        <i class="fa-regular fa-circle-user fa-2xl"></i>
                    </div>

                    <span>
                        <?php echo htmlspecialchars($user["username"], ENT_QUOTES, 'UTF-8'); ?>
                    </span>

                </a>

                <ul class="dropdown-menu mt-2">
                    <li class="nav-item">
                        <a class="dropdown-item" href="cart.php" aria-label="Shopping cart">
                            <i class="fa-solid fa-cart-shopping"></i> Cart
                            <span class="badge rounded-pill text-bg-light text-primary" id="cartCount"><?php echo cart_item_count(); ?></span>
                        </a>
                    </li>
                    <li><a class="dropdown-item" href="../pages/dashboard.php">Dashboard</a></li>
                    <li><a class="dropdown-item" href="../auth/logout.php">Logout</a></li>
                </ul>
                </div>
            </div>
        </div>
    </nav>
</div>