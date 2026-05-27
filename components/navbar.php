<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>

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
                    <a href="../auth/logout.php" class="btn btn-outline-success my-2 my-sm-0" type="submit" style="font-weight:bolder;color:white">logout</a>
                </form>
            </div>

        </div>
    </nav>

</body>

</html>