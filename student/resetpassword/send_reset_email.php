<?php
// student/resetpassword/send_reset_email.php

require __DIR__ . '/../../DB_connection.php';
// Load Composer autoloader from resetpassword/vendor
require __DIR__ . '/verifyEmail/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Initialize status/message
$status  = 'error';
$message = '';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    $message = 'Method Not Allowed';
} else {
    // Validate email address
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    if (!$email) {
        $message = 'Invalid email address.';
    } else {
        // Check that the email exists in student_account
        $stmt = $conn->prepare("SELECT accountid FROM student_account WHERE email = ?");
        $stmt->execute([$email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            // Custom message when no matching account
            $message = 'No account found for that email. Please check your address and try again.';
        } else {
            // Proceed with token & email
            try {
                // Generate token & expiry
                $token   = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', time() + 3600);
                $conn->prepare(
                    "INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)"
                )->execute([$email, $token, $expires]);

                // Send the reset email
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'no.reply.vgtutor@gmail.com';
                $mail->Password   = 'psxp ijkl mlsr lmrw';
                $mail->SMTPSecure = 'tls';
                $mail->Port       = 587;

                $mail->setFrom('no.reply.vgtutor@gmail.com', 'VGtUtor');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = 'VGtUtor Password Reset Request';
                // Build dynamic base URL
                $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
                $host = $_SERVER['HTTP_HOST'];
                $baseUrl = $protocol . $host;

                $resetLink = $baseUrl . "/student/resetpassword/reset_password.php?token={$token}";
                $mail->Body    = "
                    <p>Dear VGtUtor user,</p>
                    <p>We received a request to reset your password. Please click the link below to proceed:</p>
                    <p><a href=\"{$resetLink}\">Reset Your Password</a></p>
                    <p>This link expires in one hour. If you did not request this, you can ignore this email.</p>
                    <p>Thanks,<br>The VGtUtor Team</p>
                ";
                $mail->send();

                $status  = 'success';
                $message = 'A password reset link has been sent to your email. Please check your inbox.';
            } catch (Exception $e) {
                // show a user-friendly error
                $message = 'Failed to send email. Please try again later.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Password Reset | VGtUtor</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css"
        rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../css/reset_password/send_reset_email.css">
</head>

<body>
    <div class="container">
        <div class="message <?= $status ?>">
            <?= htmlspecialchars($message) ?>
        </div>
        <?php if ($status === 'success'): ?>
            <a href="../../login.php" class="btn-back">Back to Login</a>
        <?php else: ?>
            <!-- On error, offer to go back to the form -->
            <a href="forgot_password.php" class="btn-back">Try Again</a>
        <?php endif; ?>
    </div>
</body>

</html>