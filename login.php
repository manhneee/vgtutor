<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login | VGtUtor</title>

  <!-- Fonts & Framework -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Custom Style -->
  <link rel="stylesheet" href="css/login.css">
</head>

<body>
  <div class="login-page">
    <div class="login-container glass shadow-lg rounded-4 overflow-hidden">
      
      <!-- Left Panel -->
      <div class="login-left d-flex flex-column justify-content-center align-items-center text-center p-4">
        <img src="img/logo.png" alt="VGtUtor Logo" class="logo mb-3">
        <p class="description-text">Smarter learning, guided by trusted VGU tutors. Start your journey now.</p>
      </div>

      <!-- Right Panel -->
      <div class="login-right p-4">
        <div class="login-content w-100">
          <h2 class="fw-bold mb-1 text-orange">Welcome Back </h2>
          <p class="text-muted mb-4">Sign in to continue to your dashboard</p>

          <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
          <?php endif; ?>

          <form action="req/login.php" method="post">
            <div class="form-floating mb-3">
              <input type="text" name="userid" class="form-control" id="userid" placeholder="Username" required>
              <label for="userid">Username</label>
            </div>

            <div class="form-floating mb-3">
              <input type="password" name="password" class="form-control" id="password" placeholder="Password" required>
              <label for="password">Password</label>
            </div>

            <button type="submit" class="btn btn-login w-100 mb-3">Login</button>
          </form>

          <div class="form-footer text-center mt-3">
            <a href="#" class="d-block small mb-1">Forgot your password?</a>
            <span class="small">New to VGtUtor? <a href="signup.php">Create an account</a></span>
          </div>
        </div>
      </div>

    </div>
  </div>
</body>
</html>
