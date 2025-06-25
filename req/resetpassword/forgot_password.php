<?php
// Password reset request form
?>
<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Forgot Password | VGtUtor</title>

    <!-- Fonts & Framework -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom Style -->
    <link rel="stylesheet" href="../../css/reset_password/forgot_password.css">


</head>

<body class="p-5">
    <div class="container" style="max-width:400px">
        <h3 class="mb-4">Forgot Your Password?</h3>
        <form action="send_reset_email.php" method="post">
            <div class="mb-3">
                <label for="email" class="form-label">Registered Email Address</label>
                <input
                    type="email"
                    name="email"
                    id="email"
                    class="form-control"
                    placeholder="Enter your email"
                    required
                    autofocus>
            </div>
            <button type="submit" class="btn btn-primary w-100">
                Send Password Reset Link
            </button>
        </form>
        <p class="mt-3 text-center">
            <a href="../../login.php">&larr; Back to Login</a>
        </p>
    </div>
</body>

</html>