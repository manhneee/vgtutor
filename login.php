<?php
session_start();
include "DB_connection.php";
require_once 'req/signup.php';
$success = $error = "";

// Đăng ký (Signup)

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signup_btn'])) {
    file_put_contents("debug.txt", date("c") . " - Signup POST: " . json_encode($_POST) . "\n", FILE_APPEND);
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
        $success = "Sign up successfully! Please check your email to verify your account.";
    } else {
        $error = $result;
    }
}



// Đăng nhập (Signin) - Chỉ hiện lỗi nếu có
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
    <style>
        .popupsignup {
            position: fixed;
            top: 40px;
            left: 50%;
            transform: translateX(-50%);
            min-width: 350px;
            max-width: 90vw;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 32px rgba(0, 0, 0, 0.18);
            border: 2px solid #53c9b4;
            z-index: 9999;
            padding: 32px 30px 24px 30px;
            display: flex;
            flex-direction: column;
            align-items: center;
            animation: popupfade 0.35s;
        }

        @keyframes popupfade {
            0% {
                opacity: 0;
                transform: translate(-50%, -24px);
            }

            100% {
                opacity: 1;
                transform: translate(-50%, 0);
            }
        }

        .popupsignup .close-btn {
            position: absolute;
            top: 8px;
            right: 16px;
            font-size: 28px;
            font-weight: bold;
            color: #888;
            cursor: pointer;
            transition: color 0.2s;
        }

        .popupsignup .close-btn:hover {
            color: #3bc8a3;
        }

        .popupsignup .popup-content {
            text-align: center;
        }

        .popupsignup h2 {
            margin-top: 0;
            color: #34b6a8;
            font-size: 1.4em;
            margin-bottom: 12px;
        }

        .popupsignup p {
            color: #333;
            margin: 0;
            font-size: 1.08em;
        }


        input[type="email"]:invalid {
            border: 2px solid #e34b4b !important;
            background: #fff7f7 !important;
        }

        input[type="email"]:focus:invalid {
            outline: 2px solid #e34b4b !important;
        }
    </style>


</head>

<body>
    <?php if ($success): ?>
        <div class="popupsignup" id="popupsignup">
            <span class="close-btn" onclick="document.getElementById('popupsignup').style.display='none';">&times;</span>
            <div class="popup-content">
                <h2>Registration Received!</h2>
                <p>
                    Thank you for registering.<br>
                    Please <strong>check the email which you used to sign up</strong> and click the verification link to activate your account.
                </p>
            </div>
        </div>
    <?php endif; ?>


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
                <label for="email" style="font-size: 0.6em; color: #444; margin-bottom: 3px;">
                    Student email must be in the format: <strong>...@student.vgu.edu.vn</strong>
                </label>
                <input type="email" id="email" name="email" placeholder="Student Email" required
                    pattern="^[a-zA-Z0-9._%+-]+@student\.vgu\.edu\.vn$"
                    title="Email must be in the format ...@student.vgu.edu.vn" />
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