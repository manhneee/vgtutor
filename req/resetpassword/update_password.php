<?php
// student/resetpassword/update_password.php

require __DIR__ . '/../../DB_connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Invalid request method.');
}

$token    = $_POST['token'] ?? '';
$password = $_POST['password'] ?? '';
$confirm  = $_POST['confirm_password'] ?? '';

if ($password !== $confirm) {
    exit('Passwords do not match.');
}

// 1) Verify the token and expiration
$stmt = $conn->prepare("
    SELECT email, expires_at
    FROM password_resets
    WHERE token = ?
");
$stmt->execute([$token]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data || strtotime($data['expires_at']) < time()) {
    exit('This reset link is invalid or has expired.');
}
$email = $data['email'];

// 2) Lookup the user’s accountid by email
$stmt = $conn->prepare("SELECT accountid FROM student_account WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    exit('No account found for that email.');
}
$userid = $user['accountid'];

// 3) Hash the new password and update the account table
$hash = password_hash($password, PASSWORD_DEFAULT);
$conn->prepare("
    UPDATE account
    SET password = ?
    WHERE userid = ?
")->execute([$hash, $userid]);

// 4) Remove the used token so it cannot be reused
$conn->prepare("
    DELETE FROM password_resets
    WHERE token = ?
")->execute([$token]);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Password Updated | VGtUtor</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css"
        rel="stylesheet">
    <!-- Custom Style -->
    <link rel="stylesheet" href="../../css/reset_password/update_password.css">
</head>

<body class="p-5">
    <div class="container">
        <div class="message success">
            ✔ Your password has been successfully updated!
        </div>
        <a href="../../login.php" class="btn-back">Log in</a>
    </div>
</body>

</html>