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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="home.css">
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-primary" data-bs-theme="dark">

        <div class="container-fluid">
            <!-- Logo navbar -->
            <a class="navbar-brand" href="#">Navbar</a>
            <!-- links navbar -->
            <ul class="navbar-nav me-auto">
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
            <!-- <div class="col">
                <div class="p-3">
                    <div class="card" style="width: 18rem;">
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSTnnSQ7Zmby7ZBYpSVhZve4cCBfySL3BHQZQ&s" class="card-img-top" alt="">
                        <div class="card-body">
                            <h5 class="card-title">Card title</h5>
                            <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card’s content.</p>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">An item</li>
                            <li class="list-group-item">A second item</li>
                            <li class="list-group-item">A third item</li>
                        </ul>
                        <div class="card-body">
                            <a href="#" class="card-link">Card link</a>
                            <a href="#" class="card-link">Another link</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="p-3">
                    <div class="card" style="width: 18rem;">
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSTnnSQ7Zmby7ZBYpSVhZve4cCBfySL3BHQZQ&s" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">Card title</h5>
                            <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card’s content.</p>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">An item</li>
                            <li class="list-group-item">A second item</li>
                            <li class="list-group-item">A third item</li>
                        </ul>
                        <div class="card-body">
                            <a href="#" class="card-link">Card link</a>
                            <a href="#" class="card-link">Another link</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="p-3">
                    <div class="card" style="width: 18rem;">
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSTnnSQ7Zmby7ZBYpSVhZve4cCBfySL3BHQZQ&s" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">Card title</h5>
                            <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card’s content.</p>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">An item</li>
                            <li class="list-group-item">A second item</li>
                            <li class="list-group-item">A third item</li>
                        </ul>
                        <div class="card-body">
                            <a href="#" class="card-link">Card link</a>
                            <a href="#" class="card-link">Another link</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="p-3">
                    <div class="card" style="width: 18rem;">
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSTnnSQ7Zmby7ZBYpSVhZve4cCBfySL3BHQZQ&s" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">Card title</h5>
                            <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card’s content.</p>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">An item</li>
                            <li class="list-group-item">A second item</li>
                            <li class="list-group-item">A third item</li>
                        </ul>
                        <div class="card-body">
                            <a href="#" class="card-link">Card link</a>
                            <a href="#" class="card-link">Another link</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="p-3">
                    <div class="card" style="width: 18rem;">
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSTnnSQ7Zmby7ZBYpSVhZve4cCBfySL3BHQZQ&s" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">Card title</h5>
                            <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card’s content.</p>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">An item</li>
                            <li class="list-group-item">A second item</li>
                            <li class="list-group-item">A third item</li>
                        </ul>
                        <div class="card-body">
                            <a href="#" class="card-link">Card link</a>
                            <a href="#" class="card-link">Another link</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="p-3">
                    <div class="card" style="width: 18rem;">
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSTnnSQ7Zmby7ZBYpSVhZve4cCBfySL3BHQZQ&s" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">Card title</h5>
                            <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card’s content.</p>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">An item</li>
                            <li class="list-group-item">A second item</li>
                            <li class="list-group-item">A third item</li>
                        </ul>
                        <div class="card-body">
                            <a href="#" class="card-link">Card link</a>
                            <a href="#" class="card-link">Another link</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="p-3">
                    <div class="card" style="width: 18rem;">
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSTnnSQ7Zmby7ZBYpSVhZve4cCBfySL3BHQZQ&s" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">Card title</h5>
                            <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card’s content.</p>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">An item</li>
                            <li class="list-group-item">A second item</li>
                            <li class="list-group-item">A third item</li>
                        </ul>
                        <div class="card-body">
                            <a href="#" class="card-link">Card link</a>
                            <a href="#" class="card-link">Another link</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="p-3">
                    <div class="card" style="width: 18rem;">
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSTnnSQ7Zmby7ZBYpSVhZve4cCBfySL3BHQZQ&s" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">Card title</h5>
                            <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card’s content.</p>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">An item</li>
                            <li class="list-group-item">A second item</li>
                            <li class="list-group-item">A third item</li>
                        </ul>
                        <div class="card-body">
                            <a href="#" class="card-link">Card link</a>
                            <a href="#" class="card-link">Another link</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="p-3">
                    <div class="card" style="width: 18rem;">
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSTnnSQ7Zmby7ZBYpSVhZve4cCBfySL3BHQZQ&s" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">Card title</h5>
                            <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card’s content.</p>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">An item</li>
                            <li class="list-group-item">A second item</li>
                            <li class="list-group-item">A third item</li>
                        </ul>
                        <div class="card-body">
                            <a href="#" class="card-link">Card link</a>
                            <a href="#" class="card-link">Another link</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="p-3">
                    <div class="card" style="width: 18rem;">
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSTnnSQ7Zmby7ZBYpSVhZve4cCBfySL3BHQZQ&s" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">Card title</h5>
                            <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card’s content.</p>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">An item</li>
                            <li class="list-group-item">A second item</li>
                            <li class="list-group-item">A third item</li>
                        </ul>
                        <div class="card-body">
                            <a href="#" class="card-link">Card link</a>
                            <a href="#" class="card-link">Another link</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="p-3">
                    <div class="card" style="width: 18rem;">
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSTnnSQ7Zmby7ZBYpSVhZve4cCBfySL3BHQZQ&s" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">Card title</h5>
                            <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card’s content.</p>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">An item</li>
                            <li class="list-group-item">A second item</li>
                            <li class="list-group-item">A third item</li>
                        </ul>
                        <div class="card-body">
                            <a href="#" class="card-link">Card link</a>
                            <a href="#" class="card-link">Another link</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="p-3">
                    <div class="card" style="width: 18rem;">
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSTnnSQ7Zmby7ZBYpSVhZve4cCBfySL3BHQZQ&s" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">Card title</h5>
                            <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card’s content.</p>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">An item</li>
                            <li class="list-group-item">A second item</li>
                            <li class="list-group-item">A third item</li>
                        </ul>
                        <div class="card-body">
                            <a href="#" class="card-link">Card link</a>
                            <a href="#" class="card-link">Another link</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="p-3">
                    <div class="card" style="width: 18rem;">
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSTnnSQ7Zmby7ZBYpSVhZve4cCBfySL3BHQZQ&s" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">Card title</h5>
                            <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card’s content.</p>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">An item</li>
                            <li class="list-group-item">A second item</li>
                            <li class="list-group-item">A third item</li>
                        </ul>
                        <div class="card-body">
                            <a href="#" class="card-link">Card link</a>
                            <a href="#" class="card-link">Another link</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="p-3">
                    <div class="card" style="width: 18rem;">
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSTnnSQ7Zmby7ZBYpSVhZve4cCBfySL3BHQZQ&s" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">Card title</h5>
                            <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card’s content.</p>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">An item</li>
                            <li class="list-group-item">A second item</li>
                            <li class="list-group-item">A third item</li>
                        </ul>
                        <div class="card-body">
                            <a href="#" class="card-link">Card link</a>
                            <a href="#" class="card-link">Another link</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="p-3">
                    <div class="card" style="width: 18rem;">
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSTnnSQ7Zmby7ZBYpSVhZve4cCBfySL3BHQZQ&s" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">Card title</h5>
                            <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card’s content.</p>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">An item</li>
                            <li class="list-group-item">A second item</li>
                            <li class="list-group-item">A third item</li>
                        </ul>
                        <div class="card-body">
                            <a href="#" class="card-link">Card link</a>
                            <a href="#" class="card-link">Another link</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="p-3">
                    <div class="card" style="width: 18rem;">
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSTnnSQ7Zmby7ZBYpSVhZve4cCBfySL3BHQZQ&s" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">Card title</h5>
                            <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card’s content.</p>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">An item</li>
                            <li class="list-group-item">A second item</li>
                            <li class="list-group-item">A third item</li>
                        </ul>
                        <div class="card-body">
                            <a href="#" class="card-link">Card link</a>
                            <a href="#" class="card-link">Another link</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="p-3">
                    <div class="card" style="width: 18rem;">
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSTnnSQ7Zmby7ZBYpSVhZve4cCBfySL3BHQZQ&s" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">Card title</h5>
                            <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card’s content.</p>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">An item</li>
                            <li class="list-group-item">A second item</li>
                            <li class="list-group-item">A third item</li>
                        </ul>
                        <div class="card-body">
                            <a href="#" class="card-link">Card link</a>
                            <a href="#" class="card-link">Another link</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="p-3">
                    <div class="card" style="width: 18rem;">
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSTnnSQ7Zmby7ZBYpSVhZve4cCBfySL3BHQZQ&s" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">Card title</h5>
                            <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card’s content.</p>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">An item</li>
                            <li class="list-group-item">A second item</li>
                            <li class="list-group-item">A third item</li>
                        </ul>
                        <div class="card-body">
                            <a href="#" class="card-link">Card link</a>
                            <a href="#" class="card-link">Another link</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="p-3">
                    <div class="card" style="width: 18rem;">
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSTnnSQ7Zmby7ZBYpSVhZve4cCBfySL3BHQZQ&s" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">Card title</h5>
                            <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card’s content.</p>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">An item</li>
                            <li class="list-group-item">A second item</li>
                            <li class="list-group-item">A third item</li>
                        </ul>
                        <div class="card-body">
                            <a href="#" class="card-link">Card link</a>
                            <a href="#" class="card-link">Another link</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="p-3">
                    <div class="card" style="width: 18rem;">
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSTnnSQ7Zmby7ZBYpSVhZve4cCBfySL3BHQZQ&s" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">Card title</h5>
                            <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card’s content.</p>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">An item</li>
                            <li class="list-group-item">A second item</li>
                            <li class="list-group-item">A third item</li>
                        </ul>
                        <div class="card-body">
                            <a href="#" class="card-link">Card link</a>
                            <a href="#" class="card-link">Another link</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="p-3">
                    <div class="card" style="width: 18rem;">
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSTnnSQ7Zmby7ZBYpSVhZve4cCBfySL3BHQZQ&s" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">Card title</h5>
                            <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card’s content.</p>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">An item</li>
                            <li class="list-group-item">A second item</li>
                            <li class="list-group-item">A third item</li>
                        </ul>
                        <div class="card-body">
                            <a href="#" class="card-link">Card link</a>
                            <a href="#" class="card-link">Another link</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="p-3">
                    <div class="card" style="width: 18rem;">
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSTnnSQ7Zmby7ZBYpSVhZve4cCBfySL3BHQZQ&s" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">Card title</h5>
                            <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card’s content.</p>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">An item</li>
                            <li class="list-group-item">A second item</li>
                            <li class="list-group-item">A third item</li>
                        </ul>
                        <div class="card-body">
                            <a href="#" class="card-link">Card link</a>
                            <a href="#" class="card-link">Another link</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="p-3">
                    <div class="card" style="width: 18rem;">
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSTnnSQ7Zmby7ZBYpSVhZve4cCBfySL3BHQZQ&s" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">Card title</h5>
                            <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card’s content.</p>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">An item</li>
                            <li class="list-group-item">A second item</li>
                            <li class="list-group-item">A third item</li>
                        </ul>
                        <div class="card-body">
                            <a href="#" class="card-link">Card link</a>
                            <a href="#" class="card-link">Another link</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="p-3">
                    <div class="card" style="width: 18rem;">
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSTnnSQ7Zmby7ZBYpSVhZve4cCBfySL3BHQZQ&s" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">Card title</h5>
                            <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card’s content.</p>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">An item</li>
                            <li class="list-group-item">A second item</li>
                            <li class="list-group-item">A third item</li>
                        </ul>
                        <div class="card-body">
                            <a href="#" class="card-link">Card link</a>
                            <a href="#" class="card-link">Another link</a>
                        </div>
                    </div>
                </div>
            </div> -->
            <!-- end of all cards here -->
        </div>
        <!-- butoni load more dhe spinner -->
        <button type="button" id="load-more" class="btn btn-outline-primary btn-lg">Load more...</button>
        <div class="text-center">

            <div class="visually-hidden spinner-border me-2 my-3" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous">
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const containerItems = document.querySelector(".wrapper");
            // let div = document.querySelectorAll(".col");
            let LoadMoreButton = document.getElementById("load-more");



            fetch("https://fakestoreapi.com/products")
                .then(response => response.json())
                .then(products => {
                    console.log(products);
                    products.forEach((product, id) => {
                        let div = document.createElement("div");
                        div.classList.add("col");
                        console.log(div);
                        div.innerHTML += `
                        <div class="col" id="${id}">
                            <div class="p-3">
                                <div class="card" style="width: 18rem;">
                                    <img src="${product.image}" class="card-img-top" alt="">
                                    <div class="card-body">
                                        <h5 class="card-title">${product.title}</h5>
                                        <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card’s content.</p>
                                    </div>
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item">An item</li>
                                            <li class="list-group-item">A second item</li>
                                            <li class="list-group-item">A third item</li>
                                        </ul>
                                        <div class="card-body">
                                            <a href="#" class="card-link">Card link</a>
                                            <a href="#" class="card-link">Another link</a>
                                        </div>
                                </div>
                            </div>
                        </div>`;
                        containerItems.append(div);
                        console.log(div);
                    })
                })


            // sa items do te shfaqen ne fillim 
            let initialItems = 8;
            // sa items do te shfaqen me klikim ne butonin load more
            let loadItems = initialItems;

            // sa karta do shfaqen pa u shtypur butoni load more
            div.forEach((item, index) => {
                if (index >= initialItems) {
                    item.style.display = "none";
                    console.log(div)
                }
            });

            // funksioni qe kur shtypet butoni nga useri te shfaqi dhe 8 karta te tjera per cdo klikim
            function showMoreItem() {
                initialItems += loadItems;
                div.forEach((item, index) => {
                    if (index < initialItems) {
                        item.style.display = "block";
                    }
                });


                if (initialItems >= div.length) {
                    LoadMoreButton.style.display = "none";
                }
            };

            LoadMoreButton.addEventListener("click", showMoreItem)
                // load more button function
                .catch(error => {
                    console.error("Error fetching data:", error);
                });





        }); // DOMContentLoaded event listener
    </script>

</body>

</html>