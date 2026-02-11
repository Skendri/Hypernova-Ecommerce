<?php
include './database.php';

$message = "";
$toastClass = "";

$token = $_GET["token"];

$token_hash = hash("sha256", $token);

$sql = "SELECT * FROM userdata
        WHERE reset_token_hash = ?";

$stmt = $linkConnect->prepare($sql);

$stmt->bind_param("s", $token_hash);

$stmt->execute();

$result = $stmt->get_result();

$user = $result->fetch_assoc();

if ($user === null) {
    die("token not found");
}

if (strtotime($user["reset_token_expires_at"]) <= time()) {
    die("token has expired");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    if ($password === $confirmPassword) {
        // Prepare and execute
        $stmt = $linkConnect->prepare("UPDATE userdata SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $hashedPassword, $email);

        if ($stmt->execute()) {
            $message = "Password updated successfully";
            $toastClass = "bg-success";
        } else {
            $message = "Error updating password";
            $toastClass = "bg-danger";
        }

        $stmt->close();
    } else {
        $message = "Passwords do not match";
        $toastClass = "bg-warning";
    }

    $linkConnect->close();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, 
                   initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
    <link rel="shortcut icon" href="https://cdn-icons-png.flaticon.com/512/295/295128.png">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <title>Reset Password</title>
</head>

<body>
    <div class="container p-5 d-flex flex-column align-items-center">
        <?php if ($message): ?>
            <div class="toast align-items-center text-white border-0" role="alert"
                aria-live="assertive" aria-atomic="true"
                style="background-color: <?php echo $toastClass === 'bg-success' ?
                                                '#28a745' : ($toastClass === 'bg-danger' ? '#dc3545' : ($toastClass === 'bg-warning' ? '#ffc107' : '')); ?>">
                <div class="d-flex">
                    <div class="toast-body">
                        <?php echo $message; ?>
                    </div>
                    <button type="button" class="btn-close 
                    btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        <?php endif; ?>
        <form action="process-reset-password.php" method="post" class="form-control mt-5 p-4" style="height:auto; width:380px; box-shadow: rgba(60, 64, 67, 0.3) 
            0px 1px 2px 0px, rgba(60, 64, 67, 0.15) 0px 2px 6px 2px;">

            <!-- muk e di ca eshte -->
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

            <div class="row">
                <i class="fa fa-user-circle-o fa-3x mt-1 mb-2"
                    style="text-align: center; color: green;"></i>
                <h5 class="text-center p-4" style="font-weight: 700;">
                    Change Your Password</h5>
            </div>

            <div class="col mb-3 mt-3">
                <label for="password"><i class="fa fa-lock"></i>
                    Password</label>
                <input type="password" name="password"
                    id="password" class="form-control" required>
            </div>
            <div class="col mb-3 mt-3">
                <label for="confirm_password"><i
                        class="fa fa-lock"></i> Confirm Password</label>
                <input type="password" name="confirm_password"
                    id="confirm_password"
                    class="form-control" required>
            </div>

            <div class="col mb-3 mt-3">
                <button type="submit" class="btn bg-dark" style="font-weight: 600; color:white;">
                    Reset Password
                </button>
            </div>
        </form>
    </div>
</body>

</html>