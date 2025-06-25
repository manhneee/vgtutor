<?php
session_start();
include "DB_connection.php";
// Xử lý đăng ký
$success = $error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signup_btn'])) {
    include "req/signup.php";
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
// Xử lý đăng nhập (chuyển sang req/login.php, nên chỉ hiển thị lỗi nếu có)
$login_error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : "";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>VGtUtor</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <link rel="stylesheet" href="css/login1.css" />

</head>

<body>
    <div class="container" id="container">
        <!-- SIGN UP FORM -->
        <div class="form-container sign-up">
            <form method="post" action="">
                <h1>Create Account</h1>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?= $success ?></div>
                <?php elseif ($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <input type="text" name="studentid" placeholder="Student ID" required />
                <input type="password" name="password" placeholder="Password" required oninput="checkPasswordMatch()" />
                <input type="password" name="confirm_password" placeholder="Confirm Password" required oninput="checkPasswordMatch()" />
                <input type="text" name="fullname" placeholder="Full Name" required />
                <input type="email" name="email" placeholder="Student Email" required />
                <input type="text" name="major" placeholder="Major" required />
                <input type="text" name="intake" placeholder="Intake" required />
                <button type="submit" name="signup_btn">Sign Up</button>

            </form>
        </div>
        <!-- SIGN IN FORM -->
        <div class="form-container sign-in">
            <form method="post" action="req/login.php">
                <h1>Student Login</h1>
                <?php if ($login_error): ?>
                    <div class="alert alert-danger"><?= $login_error ?></div>
                <?php endif; ?>
                <input type="text" name="userid" placeholder="Student ID / Username" required />
                <input type="password" name="password" placeholder="Password" required />

                <a href="req/resetpassword/forgot_password.php" class="d-block small mb-1">🔒 Forgot your password?</a>
                <button type="submit" name="signin_btn">Sign In</button>

            </form>
        </div>
        <!-- TOGGLE PANEL -->
        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-left">
                    <h1>Welcome Back!</h1>
                    <p>Sign in with your account to access your dashboard</p>
                    <button class="hidden" id="login" type="button">Sign In</button>
                </div>
                <div class="toggle-panel toggle-right">
                    <h1>New to VGtUtor?</h1>
                    <p>
                        Register with your student information to use our platform
                    </p>
                    <button class="hidden" id="register" type="button">Sign Up</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Password match check -->
    <script>
        function checkPasswordMatch() {
            const pwd = document.querySelector('input[name="password"]');
            const conf = document.querySelector('input[name="confirm_password"]');
            if (!pwd || !conf) return;
            if (pwd.value !== conf.value) {
                conf.setCustomValidity("Passwords do not match");
            } else {
                conf.setCustomValidity("");
            }
        }
        // Toggle logic: chỉ dùng nút ở panel phải/trái
        const container = document.getElementById('container');
        const registerBtn = document.getElementById('register');
        const loginBtn = document.getElementById('login');

        registerBtn.addEventListener('click', () => {
            container.classList.add("active");
        });
        loginBtn.addEventListener('click', () => {
            container.classList.remove("active");
        });
    </script>
</body>

</html>