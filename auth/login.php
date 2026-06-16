<?php

require_once __DIR__ . '/../config/database.php';
session_start();

$message = "";
$toastClass = "";
// all this logic code here is about to check if user is register before and if write correct data

// This if block ensures the code only runs when the user submits the login form.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // get users email and password from form on the login page
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // These will later store values from MySQL.
    $userId = null;
    $hashedPassword = null;

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format";
        $toastClass = "bg-danger";
    } else {
        // Prepare and execute
        $stmt = $linkConnect->prepare("SELECT id, password FROM userdata WHERE email = ?");
        if (!$stmt) {
            die("Database error");
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        // Check if email exists
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($userId, $hashedPassword);
            // fetch the row and store the values in $userId and $hashedPassword
            $stmt->fetch();

            if ($hashedPassword !== null && password_verify($password, $hashedPassword)) {
                $message = "Login successful";
                $toastClass = "bg-success";
                // This helps prevent Session Fixation attacks.
                session_regenerate_id(true);
                $_SESSION["user_id"] = $userId;
                $_SESSION['email'] = $email;
                // redirect to the dashboard or home page
                header("Location: ../pages/home.php");
                exit();
            } else {
                $message = "Invalid email or password";
                $toastClass = "bg-danger";
            }
        } else {
            $message = "Invalid email or password";
            $toastClass = "bg-danger";
        }

        $stmt->close();
        $linkConnect->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<style>
    @import url('https://fonts.googleapis.com/css2?family=Black+Ops+One&display=swap');

    .bg-video {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        z-index: 0;
    }

    .content {
        z-index: 2;
    }

    .form-custom {
        background-color: transparent !important;
        border: none !important;
    }

    .black-ops-one-regular {
        font-family: "Black Ops One", system-ui;
        font-weight: 400;
        font-style: normal;
    }

    .overlay {
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1;
    }
</style>

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
    <link rel="shortcut icon" href="https://cdn-icons-png.flaticon.com/512/295/295128.png">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <title>Login Page</title>
</head>

<body class="custom-class d-flex align-items-center justify-content-center" style="height: 100vh;">
    <div class="position-absolute top-0 start-0 w-100 h-100 overlay"></div>
    <video autoplay muted loop playsinline class=" bg-video">
        <source src="../background-video.mp4" type="video/mp4">
    </video>
    <div class="container content d-flex flex-column justify-content-center align-items-center">
        <?php if ($message): ?>
            <div class="toast align-items-center text-white 
            <?php echo $toastClass; ?> border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <?php echo $message; ?>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        <?php endif; ?>
        <!-- this form have REQUEST_METHOD = POST -->
        <form action="" method="post" class="form-control form-custom mt-5 p-4"
            style="height: 600px; width:400px; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);">
            <div class="row">
                <i class="fa fa-user-circle-o fa-3x mt-1 mb-2"
                    style="text-align: center; color: green;"></i>
                <h5 class="text-center p-4 text-white"
                    style="font-weight: 700;">Login Into Your Account</h5>
            </div>
            <div class="col-mb-3 py-2">
                <label for="email" class="py-2 fa-1x">
                    <i class="fa fa-envelope"></i>
                    Email
                </label>
                <input type="text" name="email" id="email" class="form-control py-2" required>
            </div>
            <div class="col mb-3 mt-3">
                <label for="password" class="py-2 fa-1x">
                    <i class="fa fa-lock"></i>
                    Password
                </label>
                <input type="password" name="password" id="password"
                    class="form-control" required>
            </div>
            <div class="flex row px-5 mb-5 mt-5">
                <button type="submit" class="btn btn-success bg-success" style="font-weight: 600;">Login</button>
            </div>
            <div class="col mb-2 mt-4">
                <p class="text-center"
                    style="font-weight: 600; color: navy;"><a href="register.php"
                        style="text-decoration: none;">Create Account</a> OR <a href="forgot-password.php"
                        style="text-decoration: none;">Forgot Password</a></p>
            </div>
        </form>
    </div>
    <div class="w-75 content form-custom h-100 mx-5" style="box-shadow:rgba(60, 64, 67, 0.5) 0px 4px 8px 0px,rgba(60, 64, 67, 0.3) 0px 8px 20px 4px;">
        <div class=" py-4 h-100 d-flex flex-column justify-content-between align-items-center ">
            <h1 class="text-success black-ops-one-regular fs-2">Welcom to HYPERNOVA Platform</h1>
            <p class="text-white black-ops-one-regular" style="width: 90%;"> <b>A hypernova</b> është një shpërthim yjor jashtëzakonisht i fuqishëm dhe i shndritshëm,
                që çliron 10 deri në 100 herë më shumë energji sesa një supernova standarde.
                Zakonisht rezulton nga shembja katastrofike e bërthamës së yjeve masivë >30 masa diellore,
                ato shpesh lënë pas vrima të zeza rrotulluese dhe lëshojnë shpërthime intensive rrezesh gama.
            </p>
        </div>
    </div>
    <script>
        var toastElList = [].slice.call(document.querySelectorAll('.toast'))
        var toastList = toastElList.map(function(toastEl) {
            return new bootstrap.Toast(toastEl, {
                delay: 3000
            });
        });
        toastList.forEach(toast => toast.show());
    </script>

</body>

</html>