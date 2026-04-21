<?php

session_start();

$linkConnect = require __DIR__ . "/database.php";

if (isset($_SESSION["user_id"])) {

    $sql = "SELECT * FROM userdata
            WHERE id = {$_SESSION["user_id"]}";

    $result = $linkConnect->query($sql);

    $user = $result->fetch_assoc();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="home.css">
    <title>Dashboard</title>
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-primary" data-bs-theme="dark">

        <div class="container">
            <!-- Logo navbar -->
            <a class="navbar-brand" href="#">Navbar</a>
            <!-- links navbar -->
            <ul class="navbar-nav .d-md-flex">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="#">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Features</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Pricing</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Link
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <sapn class="dropdown-item" href="#">Action</sapn>
                        </li>
                        <li>
                            <sapn class="dropdown-item" href="#">Another action</sapn>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><span class="dropdown-item" href="#">Something else here</span></li>
                    </ul>
                </li>
            </ul>

            <!-- search form -->
            <form class="d-flex" role="search">
                <input name="search" class="form-control me-2" type="search" placeholder="Search" aria-label="Search" />
                <button class="btn btn-outline-dark" type="submit" style="color: whitesmoke;">Search</button>
            </form>

            <!-- logout button -->
            <div class="" id="collapsibleNavId">
                <form class="d-flex my-lg-0">
                    <a href="./logout.php" class="btn btn-outline-success my-2 my-sm-0"
                        type="submit" style="font-weight:bolder;color:white">
                        logout</a>
                </form>
            </div>

        </div>
    </nav>

    <!-- fillimi i kontentit te faqes -->
    <div>
        <h2 class="p-4 mt-5">Welcome To Dashboard <?php echo $_SESSION['email']; ?> </h2>
    </div>

    <div class="container text-center">
        <div class="row wrapper row-cols-4">
            <!-- all cards here -->

            <!-- all cards here -->
        </div>
        <!-- butoni load more dhe spinner -->
        <button type="button" id="load-more" class="btn btn-outline-primary btn-lg">Load more...</button>
        <div class="text-center">

            <div class="visually-hidden spinner-border me-2 my-3" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>

    <!-- news api are displayed here from javascript file -->
    <div class="container text-center">
        <div id="news-container">
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous">
    </script>
    <script src="./home.js"></script>
</body>

</html>