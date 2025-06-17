<?php 
session_start();
if (isset($_SESSION['adminid']) && 
    isset($_SESSION['role'])) {
    
    if ($_SESSION['role'] == 'Admin') {
       
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin-Home</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" href="../img/logo.png">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

</head>
<body class="body-home"> 
    <?php include "inc/navbar.php"; ?>

    <div class="container mt-5">
        <div class="text-center">
            <div class="col btn bg-orange m-2 py-3">
                <h1 style="color: white">Welcome, <?= $_SESSION['name']?> - <?=$_SESSION['adminid'] ?></h1>
                <p style="color: white">You are logged in as an <?= $_SESSION['role'] ?>.</p>
            </div>
        </div>
        
    <div class="container mt-5">
        <div class="container text-center">
            <div class="row row-cols-5">
                <a href="tutor_processing/tutor.php" class="col btn bg-orange m-2 py-3">
                    <i class="fa fa-user-md fs-1" aria-hidden="true"></i><br>Tutors
                </a>
                <a href="student_processing/student.php" class="col btn bg-orange m-2 py-3">
                    <i class="fa fa-users fs-1" aria-hidden="true"></i><br>Students
                </a>
                <a href="tutor_processing/pending_registration.php" class="col btn bg-orange m-2 py-3">
                    <i class="fa fa-book fs-1" aria-hidden="true"></i><br>Tutor Registration
                </a>
                <a href="course_processing/course.php" class="col btn bg-orange m-2 py-3">
                    <i class="fa fa-cubes fs-1" aria-hidden="true"></i><br>Courses
                </a>
                <a href="course_processing/offerings.php" class="col btn bg-orange m-2 py-3">
                    <i class="fa fa-columns fs-1" aria-hidden="true"></i><br>Offerings
                </a>
                <a href="review_processing/review.php" class="col btn bg-orange m-2 py-3">
                    <i class="fa fa-star fs-1" aria-hidden="true"></i><br>Reviews
                </a>
                <a href="course_processing/pending_offerings.php" class="col btn bg-orange m-2 py-3">
                    <i class="fa fa-pencil-square fs-1" aria-hidden="true"></i><br>Pending Offerings
                </a>
                <a href="report_processing/getReport.php" class="col btn btn-danger m-2 py-3">
                    <i class="fa fa-comments fs-1" aria-hidden="true"></i><br>Reports
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
    $em = "You are not authorized to access this page.";
    header("Location: ../login.php?error=$em");
    // header("Location: ../login.php");
    exit;
 } 
} else {
    $em = "You are not logged in.";
    header("Location: ../login.php?error=$em");
    // header("Location: ../login.php");
    exit;
 }
?>
