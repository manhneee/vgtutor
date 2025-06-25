<?php
include __DIR__ . '/../DB_connection.php';

$email = $_GET['email'] ?? '';
$token = $_GET['token'] ?? '';

if ($email && $token) {
    $stmt = $conn->prepare("SELECT * FROM account WHERE email = ? AND verify_token = ? AND is_verified = 0");
    $stmt->execute([$email, $token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $stmt = $conn->prepare("UPDATE account SET is_verified = 1, verify_token = NULL WHERE email = ?");
        $stmt->execute([$email]);
        echo '<div class="verify-success-box">
    <div class="verify-success-icon">&#10003;</div>
    <h2>Account Verified!</h2>
    <p>Your account has been verified successfully.<br>You can now <a href="../login.php" class="goto-login">log in</a> and start using VGtUtor.</p>
    <a href="../login.php" class="verify-success-btn">Go to Login</a>
</div>
<div class="verify-success-bg"></div>';
    } else {
        echo "Invalid verification link or account already verified.";
    }
} else {
    echo "Missing verification information.";
}
