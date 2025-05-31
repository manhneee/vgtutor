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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Signup</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="img/logo.png">
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
<body class="body-login">
    <div class="white-fill br-10">
        <div class="d-flex justify-content-center align-items-center flex-column">
            <form class="login" method="post" action="">
                <div class="text-center">
                    <a class="navbar-brand" href="index.php">
                        <img src="img/logo.png" alt="Logo" class="d-inline-block align-text align-items-center justify-content-center" style="width: 350px;">
                    </a>
                </div>
                <?php if ($success): ?>
                    <div class="alert alert-success" role="alert">
                        <?= $success ?>
                    </div>
                <?php elseif ($error): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= $error ?>
                    </div>
                <?php endif; ?>
                <h5 class="mb-3 mt-3">Account Information</h5>
                <div class="mb-3">
                    <label class="form-label">Student ID</label>
                    <input type="text" class="form-control" name="studentid" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" required oninput="checkPasswordMatch()">
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" name="confirm_password" required oninput="checkPasswordMatch()">
                    <div class="invalid-feedback">
                        Passwords do not match.
                    </div>
                </div>
                <hr>
                <h5 class="mb-3">Personal Information</h5>
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" class="form-control" name="fullname" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Student Email</label>
                    <input type="email" class="form-control" name="email" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Major</label>
                    <input type="text" class="form-control" name="major" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Intake</label>
                    <input type="text" class="form-control" name="intake" required>
                </div>
                <div class="d-flex justify-content-center">
                    <button type="submit" class="btn btn-primary" style="background-color: #f47119; border-color: #f47119;">Sign Up</button>
                </div>
            </form>
            <br/>
            <div class="text-center">
                Already have an account? <a href="login.php">Login here</a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>