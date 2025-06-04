<?php
// Start session
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>VGtUtor Login</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css">
  <link rel="icon" href="img/logo.png">
  <link rel="stylesheet" href="css/style.css">
</head>
<body >
  <div class="login-page">
    <div class="login-container">
      <!-- Left side -->
<div class="login-left d-flex flex-column justify-content-center align-items-center text-center p-4">
  <a href="index.php" class="mb-4">
    <img src="img/logo.png" alt="VGtUtor Logo" class="logo">
  </a>
  <p class="description-text">Your gateway to smarter learning with trusted tutors from VGU.</p>
</div>


      <!-- Right side -->
      <div class="login-right">
        <h2 class="fw-bold">Welcome Back</h2>
        <p class="text-muted">Login to access your tutor dashboard</p>

        <?php if (isset($_GET['error'])): ?>
          <div class="alert alert-danger" role="alert">
            <?= htmlspecialchars($_GET['error']) ?>
          </div>
        <?php endif; ?>

        <form action="req/login.php" method="post">
          <input type="text" name="userid" class="form-control" placeholder="Username" required>
          <input type="password" name="password" class="form-control" placeholder="Password" required>
          <button type="submit" class="btn btn-login">Login</button>
        </form>

        <div class="form-footer">
          <p><a href="#">Forgot your password?</a></p>
              <p>Not a member? <a href="signup.php">Register here</a></p>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
