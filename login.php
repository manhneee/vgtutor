<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="img/logo.png">
</head>
<body class="body-login"> 

    <div class="d-flex justify-content-center align-items-center flex-column">
        <form class="login"
              method="post"
              action="req/login.php">
            <div class="text-center">
                <img src="img/logo.png" alt="Logo" class="d-inline-block align-text align-items-center justify-content-center" style="width: 350px;">
            </div>
            <?php
            if (isset($_GET['error'])) { ?>
                <div class="alert alert-danger" role="alert">
                    <?=$_GET['error']?>
                </div>
            <?php } ?>
            <div class="mb-3">
                <label class="form-label">Email address</label>
                <input type="text" class="form-control" name="email">
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" class="form-control" name="password">
            </div>
            <div class="mb-3">
                <label class="form-label">Log In as:</label>
                <select class="form-control" name="role">
                    <option value="1">Admin</option>
                    <option value="2">Tutor</option>
                    <option value="3">Student</option>
                    
                    
                </select>
            </div> 
            <button type="submit" class="btn btn-primary" style="background-color: #f47119; border-color: #f47119;">Log In</button>
            <a href="index.php" class="text-decoration-none">Home</a>
        </form>
        <br/><br/>
        <!-- <div class="text-center"> 
            <?php
                $pass = 123;
                $pass = password_hash($pass, PASSWORD_DEFAULT);
                echo $pass; 
                ?>
        </div> -->
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js">
        
    </script>
</body>
</html>

