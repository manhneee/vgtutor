<?php
// student/resetpassword/reset_password.php

require __DIR__ . '/../../DB_connection.php';

$token = $_GET['token'] ?? '';
if (!$token) {
  exit('Invalid or missing token.');
}

// Verify token validity
$stmt = $conn->prepare(
  "SELECT email, expires_at
     FROM password_resets
     WHERE token = ?"
);
$stmt->execute([$token]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data || strtotime($data['expires_at']) < time()) {
  exit('This reset link is invalid or has expired.');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Set a New Password | VGtUtor</title>

  <!-- Fonts & Framework -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Custom Style -->
  <link rel="stylesheet" href="../../css/reset_password/reset_password.css">
</head>

<body class="p-5">
  <div class="container">
    <h3 class="mb-4">Set Your New Password</h3>
    <form id="resetForm" action="update_password.php" method="post" novalidate>
      <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

      <div class="mb-3">
        <label for="pw1" class="form-label">New Password</label>
        <input
          type="password"
          name="password"
          id="pw1"
          class="form-control"
          placeholder="Enter new password"
          required
          minlength="6">
      </div>

      <div class="mb-3">
        <label for="pw2" class="form-label">Confirm Password</label>
        <input
          type="password"
          name="confirm_password"
          id="pw2"
          class="form-control"
          placeholder="Re-enter new password"
          required>
      </div>

      <!-- Inline error message -->
      <div id="errorMsg" class="text-danger mb-3" style="min-height:1.5em;"></div>

      <button type="submit" id="submitBtn" class="btn btn-success w-100" disabled>
        Update Password
      </button>
    </form>
    <p class="mt-3 text-center">
      <a href="../../login.php">&larr; Back to Login</a>
    </p>
  </div>

  <script>
    const pw1 = document.getElementById('pw1');
    const pw2 = document.getElementById('pw2');
    const btn = document.getElementById('submitBtn');
    const err = document.getElementById('errorMsg');

    function validatePasswords() {
      if (!pw1.value || !pw2.value) {
        err.textContent = '';
        btn.disabled = true;
        return;
      }
      if (pw1.value === pw2.value) {
        err.textContent = '';
        btn.disabled = false;
      } else {
        err.textContent = 'Passwords do not match.';
        btn.disabled = true;
      }
    }

    pw1.addEventListener('input', validatePasswords);
    pw2.addEventListener('input', validatePasswords);

    document.getElementById('resetForm').addEventListener('submit', e => {
      if (btn.disabled) e.preventDefault();
    });
  </script>
</body>

</html>