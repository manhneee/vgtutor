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
  <style>
    * {
      box-sizing: border-box;
    }

    html, body {
      margin: 0;
      padding: 0;
      height: 100%;
      font-family: 'Inter', sans-serif;
    }

    .signup-page {
      display: flex;
      height: 100vh;
      width: 100%;
      background: url('img/NewCampus.jpg') no-repeat center center/cover;
      position: relative;
    }

    .signup-container {
      z-index: 1;
      margin: auto;
      display: flex;
      background: white;
      border-radius: 20px;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
      overflow: hidden;
      max-width: 900px;
      width: 100%;
      opacity: 0;
      transform: translateY(30px);
      animation: fadeInUp 0.8s ease-out forwards;
    }

    @keyframes fadeInUp {
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .signup-left {
      background-color: rgba(237, 213, 183, 0.9);
      border-top-left-radius: 20px;
      border-bottom-left-radius: 20px;
      min-height: 100%;
      transition: all 0.4s ease;
      box-shadow: inset -5px 0 10px rgba(0, 0, 0, 0.05);
    }

    .logo {
      max-width: 400px;
      transition: transform 0.3s ease;
    }

    .logo:hover {
      transform: scale(1.15);
    }

    .description-text {
      font-size: 1rem;
      color: #555;
      max-width: 250px;
    }

    .signup-right {
      flex: 1;
      padding: 3rem;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .form-control {
      border-radius: 30px;
      padding: 0.75rem 1.25rem;
      font-size: 1rem;
      margin-bottom: 1rem;
      background-color: #f5f5f5;
      border: 1px solid #ccc;
    }

    .btn-signup {
      background-color: #f57c00;
      color: white;
      font-weight: 600;
      border: none;
      border-radius: 30px;
      padding: 0.75rem;
      width: 100%;
      transition: all 0.3s ease;
    }

    .btn-signup:hover {
      background-color: #e65100;
      transform: scale(1.03);
    }

    .form-footer {
      text-align: center;
      margin-top: 1rem;
      font-size: 0.9rem;
    }

    .form-footer a {
      color: rgb(228, 114, 32);
      text-decoration: none;
    }

    .form-footer a:hover {
      text-decoration: underline;
    }

    @media (max-width: 768px) {
      .signup-container {
        flex-direction: column;
      }

      .signup-left {
        display: none;
      }
    }
  </style>
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
