<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/feature.css">
    <title>Document</title>
</head>

<body class="feature-page">

    <?php include '../components/navbar.php'; ?>

    <!-- Animated particle network background -->
    <div id="particles-js" aria-hidden="true"></div>

    <!-- news api are displayed here from javascript file -->
    <main class="container text-center feature-content">
        <div id="news-container"></div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/gh/VincentGarreau/particles.js@2.0.0/particles.min.js"></script>
    <script src="../assets/js/feature.js"></script>
    <script src="../assets/js/home.js"></script>
</body>

</html>