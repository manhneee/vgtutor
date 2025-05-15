<?php 
session_start();
if (isset($_SESSION['admin_id']) && 
    isset($_SESSION['role'])) {
    
    if ($_SESSION['role'] == 'Admin') {
       
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" href="../img/logo.png">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

</head>
<body class="body-home"> 
    <?php include "inc/navbar.php"; ?>

    <div class="container mt-5">
        <div class="container text-center">
            <div class="row row-cols-5">
                <a href="" class="col btn bg-orange m-2 py-3">
                    Tutors
                </a>
                <a href="" class="col btn bg-orange m-2 py-3">
                    Students
                </a>
                <a href="" class="col btn bg-orange m-2 py-3">
                    Registration Office
                </a>
                <a href="" class="col btn bg-orange m-2 py-3">
                    Class
                </a>
                <a href="" class="col btn bg-orange m-2 py-3">
                    Section
                </a>
                <a href="" class="col btn bg-orange m-2 py-3">
                    Schedule
                </a>
                <a href="" class="col btn bg-orange m-2 py-3">
                    Course
                </a>
                <a href="" class="col btn bg-orange m-2 py-3">
                    Message
                </a>
                <a href="" class="col btn bg-orange m-2 py-3">
                    Settings
                </a>

            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js">
        
    </script>
    <script>
        $(document).ready(function(){
            $("#navLinks li:nth-child(1) a").addClass('active');
        });
    </script>
</body>
</html>

<?php
 } else {
    header("Location: ../login.php");
    exit;
 } 
} else {
    header("Location: ../login.php");
    exit;
 }
?>
