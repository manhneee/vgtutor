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
<body class="body-login"> 
    <div class="white-fill br-10">
    <div class="d-flex justify-content-center align-items-center flex-column">
        <form class="login"
              method="post"
              action="req/login.php">
            <div class="text-center">
                <a class="navbar-brand" href="index.php">
                    <img src="img/logo.png" alt="Logo" class="d-inline-block align-text align-items-center justify-content-center" style="width: 350px;">
                </a>
            </div>
            <?php
            if (isset($_GET['error'])) { ?>
                <div class="alert alert-danger" role="alert">
                    <?=$_GET['error']?>
                </div>
            <?php } ?>
            <div class="mb-3">
                <label class="form-label">User ID</label>
                <input type="text" class="form-control" name="userid">
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" class="form-control" name="password">
            </div>
            <br />
            <div class="d-flex justify-content-center">
                <button type="submit" class="btn btn-primary" style="background-color: #f47119; border-color: #f47119;">Log In</button>
            </div>
        </form>
        <br/><br/>
        <!--<div class="text-center"> 
             <?php
                $pass = 123;
                $pass = password_hash($pass, PASSWORD_DEFAULT);
                echo $pass; 
                ?>
        </div> -->
    </div>
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
