<?php

require __DIR__ . "/../config/database.php";

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

<nav class="navbar navbar-expand-lg bg-primary" data-bs-theme="dark">

    <div class="container">
        <!-- Logo navbar -->
        <a class="navbar-brand" href="#">Navbar</a>
        <!-- links navbar -->
        <ul class="navbar-nav .d-md-flex">
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
            <button class="btn btn-outline-dark" type="submit" style="color: whitesmoke;">Search</button>
        </form>

        <!-- logout button -->
        <div class="dropdown h-auto">
            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2"
                href="#"
                role="button"
                data-bs-toggle="dropdown">

                <i class="fa-regular fa-circle-user fa-2xl"></i>

                <span>
                    <?php echo htmlspecialchars($user["username"], ENT_QUOTES, 'UTF-8'); ?>
                </span>

            </a>

            <ul class="dropdown-menu mt-2">
                <li><a class="dropdown-item" href="../auth/logout.php">Logout</a></li>
                <li><a class="dropdown-item" href="../pages/dashboard.php">Dashboard</a></li>
            </ul>
        </div>

    </div>
</nav>