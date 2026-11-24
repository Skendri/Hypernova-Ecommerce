<?php
require_once __DIR__ . '/../includes/session.php';

require_once __DIR__ . '/../config/database.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$message = "";
$toastClass = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim((string) ($_POST['username'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $csrfToken = (string) ($_POST['csrf_token'] ?? '');

    if (!hash_equals($_SESSION['csrf_token'], $csrfToken)) {
        $message = 'Your session expired. Please try again.';
        $toastClass = '#dc3545';
    } elseif ($username === '' || strlen($username) > 100) {
        $message = 'Enter a username of 1 to 100 characters.';
        $toastClass = '#dc3545';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 254) {
        $message = 'Enter a valid email address.';
        $toastClass = '#dc3545';
    } elseif (strlen($password) < 12 || !preg_match('/[a-z]/i', $password) || !preg_match('/[0-9]/', $password)) {
        $message = 'Password must be at least 12 characters and include a letter and a number.';
        $toastClass = '#dc3545';
    }

    if ($message === '') {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Check email existence without retrieving the stored address.
        $checkEmailStmt = $linkConnect->prepare("SELECT 1 FROM userdata WHERE email = ? LIMIT 1");
        if (!$checkEmailStmt) {
            $message = 'Registration is temporarily unavailable. Please try again.';
            $toastClass = '#dc3545';
        } else {
            $checkEmailStmt->bind_param("s", $email);
            $checkEmailStmt->execute();
            $checkEmailStmt->store_result();

            if ($checkEmailStmt->num_rows > 0) {
                $message = "Email ID already exists";
                $toastClass = "#007bff";
            } else {
                $verificationToken = bin2hex(random_bytes(32));
                $verificationTokenHash = hash('sha256', $verificationToken);
                $verificationExpiresAt = date('Y-m-d H:i:s', time() + 86400);
                $stmt = $linkConnect->prepare("INSERT INTO userdata (username, email, password, email_verified_at, email_verification_token_hash, email_verification_expires_at) VALUES (?, ?, ?, NULL, ?, ?)");
                if ($stmt) {
                    $stmt->bind_param("sssss", $username, $email, $hashedPassword, $verificationTokenHash, $verificationExpiresAt);

                    if ($stmt->execute()) {
                        try {
                            require_once __DIR__ . '/../mailer.php';
                            $mail = getMailer();
                            $mail->setFrom($_ENV['SMTP_FROM_EMAIL'] ?? $_ENV['SMTP_USERNAME']);
                            $mail->addAddress($email, $username);
                            $mail->Subject = 'Verify your Hypernova account';
                            $verificationUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https://' : 'http://')
                                . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\')
                                . '/verify-email.php?token=' . urlencode($verificationToken);
                            $mail->Body = 'Please verify your email address by clicking <a href="'
                                . htmlspecialchars($verificationUrl, ENT_QUOTES, 'UTF-8') . '">this link</a>. The link expires in 24 hours.';
                            $mail->AltBody = "Verify your Hypernova account: $verificationUrl (link expires in 24 hours).";
                            $mail->send();
                            $message = 'Account created. Check your email for a verification link before logging in.';
                            $toastClass = "#28a745";
                        } catch (Throwable $e) {
                            error_log('Verification email could not be sent: ' . $e->getMessage());
                            $message = 'Your account was created, but the verification email could not be sent. Please contact support to request a new link.';
                            $toastClass = '#dc3545';
                        }
                        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                    } else {
                        $message = 'Registration failed. Please try again.';
                        $toastClass = '#dc3545';
                    }
                    $stmt->close();
                } else {
                    $message = 'Registration is temporarily unavailable. Please try again.';
                    $toastClass = '#dc3545';
                }
            }
            $checkEmailStmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
    <link rel="shortcut icon" href="https://cdn-icons-png.flaticon.com/512/295/295128.png">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <title>Registration</title>
</head>

<body class="bg-light">
    <div class="container p-5 d-flex flex-column align-items-center">
        <?php if ($message): ?>
            <div class="toast align-items-center text-white border-0"
                role="alert" aria-live="assertive" aria-atomic="true"
                style="background-color: <?php echo $toastClass; ?>;">
                <div class="d-flex">
                    <div class="toast-body">
                        <?php echo $message; ?>
                    </div>
                    <button type="button" class="btn-close
                    btn-close-white me-2 m-auto"
                        data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        <?php endif; ?>
        <form method="post" class="form-control mt-5 p-4"
            style="height:auto; width:380px;
            box-shadow: rgba(60, 64, 67, 0.3) 0px 1px 2px 0px,
            rgba(60, 64, 67, 0.15) 0px 2px 6px 2px;">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
            <div class="row text-center">
                <i class="fa fa-user-circle-o fa-3x mt-1 mb-2" style="color: green;"></i>
                <h5 class="p-4" style="font-weight: 700;">Create Your Account</h5>
            </div>
            <div class="mb-2">
                <label for="username"><i class="fa fa-user"></i> User Name</label>
                <input type="text" name="username" id="username" class="form-control" required>
            </div>
            <div class="mb-2 mt-2">
                <label for="email"><i
                        class="fa fa-envelope"></i> Email</label>
                <input type="email" name="email" id="email" maxlength="254"
                    class="form-control" required>
            </div>
            <div class="mb-2 mt-2">
                <label for="password"><i
                        class="fa fa-lock"></i> Password</label>
                <input type="password" name="password" id="password"
                    class="form-control" minlength="12" autocomplete="new-password" required>
                <small class="form-text">Use at least 12 characters, including a letter and a number.</small>
            </div>
            <div class="mb-2 mt-3">
                <button type="submit"
                    class="btn btn-success
                bg-success" style="font-weight: 600;">Create
                    Account</button>
            </div>
            <div class="mb-2 mt-4">
                <p class="text-center" style="font-weight: 600; 
                color: navy;">I have an Account <a href="login.php"
                        style="text-decoration: none;">Login</a></p>
            </div>
        </form>
    </div>
    <script>
        let toastElList = [].slice.call(document.querySelectorAll('.toast'))
        let toastList = toastElList.map(function(toastEl) {
            return new bootstrap.Toast(toastEl, {
                delay: 3000
            });
        });
        toastList.forEach(toast => toast.show());
    </script>
</body>

</html>

