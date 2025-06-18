<?php
session_start();
include "req/signup.php";
include "DB_connection.php";

$success = $error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = registerStudent(
        $conn,
        $_POST['studentid'],
        $_POST['password'],
        $_POST['fullname'],
        $_POST['email'],
        $_POST['major'],
        $_POST['intake']
    );
    if ($result === true) {
        $success = "Sign up successfully!";
    } else {
        $error = $result;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sign Up | VGtUtor</title>

  <!-- Fonts & Framework -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Custom Style -->
  <link rel="stylesheet" href="css/signup.css">

  <script>
    function checkPasswordMatch() {
      const password = document.querySelector('input[name="password"]');
      const confirm = document.querySelector('input[name="confirm_password"]');
      if (password.value !== confirm.value) {
        confirm.classList.add('is-invalid');
      } else {
        confirm.classList.remove('is-invalid');
      }
    }
  </script>
</head>
<body>
  <div class="signup-page">
    <div class="signup-container glass shadow-lg rounded-4 overflow-hidden">
      
      <!-- Left Panel -->
      <div class="login-left d-flex flex-column justify-content-center align-items-center text-center p-4">
        <!-- <img src="img/logo.png" alt="VGtUtor Logo" class="logo mb-3"> -->
      </div>

      <!-- Right Panel -->
      <div class="login-right p-4">
        <div class="login-content w-100">
          <h2 class="fw-bold mb-1 text-orange">Create Account</h2>
          <p class="text-muted mb-4">Sign up to get started with VGtUtor</p>

          <?php if ($success): ?>
            <div class="alert alert-success" role="alert">
              <?= $success ?>
            </div>
          <?php elseif ($error): ?>
            <div class="alert alert-danger" role="alert">
              <?= $error ?>
            </div>
          <?php endif; ?>

          <form method="post" action="" novalidate>
            <div class="form-floating mb-3">
              <input type="text" name="studentid" class="form-control" id="studentid" placeholder="Student ID" required>
              <label for="studentid">Student ID</label>
            </div>

            <div class="form-floating mb-3">
              <input type="password" name="password" class="form-control" id="password" placeholder="Password" required oninput="checkPasswordMatch()">
              <label for="password">Password</label>
            </div>

            <div class="form-floating mb-3">
              <input type="password" name="confirm_password" class="form-control" id="confirm_password" placeholder="Confirm Password" required oninput="checkPasswordMatch()">
              <label for="confirm_password">Confirm Password</label>
              <div class="invalid-feedback">Passwords do not match.</div>
            </div>

            <hr>

            <div class="form-floating mb-3">
              <input type="text" name="fullname" class="form-control" id="fullname" placeholder="Full Name" required>
              <label for="fullname">Full Name</label>
            </div>

            <div class="form-floating mb-3">
              <input type="email" name="email" class="form-control" id="email" placeholder="Student Email" required>
              <label for="email">Student Email</label>
            </div>

            <div class="form-floating mb-3">
              <input type="text" name="major" class="form-control" id="major" placeholder="Major" required>
              <label for="major">Major</label>
            </div>

            <div class="form-floating mb-3">
              <input type="text" name="intake" class="form-control" id="intake" placeholder="Intake" required>
              <label for="intake">Intake</label>
            </div>

            <button type="submit" class="btn btn-signup w-100 mb-3">Sign Up</button>
          </form>

          <div class="form-footer text-center mt-3">
            <span class="small">Already have an account? <a href="login.php">Login here</a></span>
          </div>

        </div>
      </div>

    </div>
  </div>
</body>
</html>
