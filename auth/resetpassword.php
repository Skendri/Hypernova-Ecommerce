<?php
include __DIR__ . '/../config/database.php';

$token = (string) ($_GET['token'] ?? '');
if (!preg_match('/^[a-f0-9]{32}$/i', $token)) {
    http_response_code(400);
    exit('Invalid or expired password reset link.');
}

$token_hash = hash("sha256", $token);

$sql = "SELECT * FROM userdata
        WHERE reset_token_hash = ?";

$stmt = $linkConnect->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    exit('Password reset is temporarily unavailable.');
}

$stmt->bind_param("s", $token_hash);

$stmt->execute();

$result = $stmt->get_result();

$user = $result->fetch_assoc();

if ($user === null) {
    http_response_code(400);
    exit("Invalid or expired password reset link.");
}

if (empty($user['reset_token_expires_at']) || strtotime($user['reset_token_expires_at']) <= time()) {
    http_response_code(400);
    exit("Invalid or expired password reset link.");
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
    <title>Reset Password</title>
</head>

<body>
    <div class="container p-5 d-flex flex-column align-items-center">
        <form action="process-reset-password.php" method="post" class="form-control mt-5 p-4" style="height:auto; width:380px; box-shadow: rgba(60, 64, 67, 0.3) 
            0px 1px 2px 0px, rgba(60, 64, 67, 0.15) 0px 2px 6px 2px;">

            <input type="hidden" name="token" value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>">

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
                    id="password" class="form-control" minlength="12" autocomplete="new-password" required>
                <small class="form-text">Use at least 12 characters, including a letter and a number.</small>
            </div>
            <div class="col mb-3 mt-3">
                <label for="confirm_password"><i
                        class="fa fa-lock"></i> Confirm Password</label>
                <input type="password" name="confirm_password"
                    id="confirm_password"
                    class="form-control" minlength="12" autocomplete="new-password" required>
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
