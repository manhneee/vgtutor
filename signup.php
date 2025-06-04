<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Sign Up - VGtUtor</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="css/signup.css">

</head>
<body>
  <div class="signup-page">
    <div class="signup-container">
      <!-- Left side -->
      <div class="signup-left d-flex flex-column justify-content-center align-items-center text-center p-4">
        <img src="img/logo.png" alt="VGtUtor Logo" class="logo mb-3">
        <p class="description-text">Create your account and start your learning journey with us.</p>
      </div>

      <!-- Right side -->
      <div class="signup-right">
        <h2 class="fw-bold">Create Account</h2>
        <p class="text-muted">Sign up to access all features</p>

        <?php if (isset($_GET['error'])): ?>
          <div class="alert alert-danger" role="alert">
            <?= htmlspecialchars($_GET['error']) ?>
          </div>
        <?php endif; ?>

        <form action="req/signup.php" method="post">
          <input type="text" name="fullname" class="form-control" placeholder="Full Name" required>
          <input type="text" name="userid" class="form-control" placeholder="Username" required>
          <input type="email" name="email" class="form-control" placeholder="Email" required>
          <input type="password" name="password" class="form-control" placeholder="Password" required>
          <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" required>
          <button type="submit" class="btn btn-signup">Sign Up</button>
        </form>

        <div class="form-footer">
          <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
