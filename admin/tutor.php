<?php 
session_start();
if (isset($_SESSION['adminid']) && 
    isset($_SESSION['role'])) {
    
    if ($_SESSION['role'] == 'Admin') {
        include "../DB_connection.php";
        include "data/tutor.php";
        include "data/course.php";
        include "data/course_offering.php";

        $tutors = getAllTutors($conn);
        $courses = getAllCourses($conn);
        $course_offering = getAllCourseOfferings($conn);
        
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin-Tutors</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" href="../img/logo.png">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

</head>
<body class="body-home"> 
    <?php 
        include "inc/navbar.php"; 
        if ($tutors !=0) {

    ?>
        
    <div class="container mt-5">
        <a href="" class="btn bg-orange">Add New Tutor</a>
        <div class="table-responsive">
            <table id="table" class="table table-bordered mt-3 n-table ">
            <thead>
                <tr>
                <th scope="col">#</th>
                <th scope="col">TutorID</th>
                <th scope="col">Name</th>
                <th scope="col">Email</th>
                <th scope="col">GPA</th>
                <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tutors as $tutor) { ?>
                    
                
                <tr>
                    <th scope="row">1</th>
                    <td><?= $tutor['accountid'] ?></td>
                    <td><?= $tutor['name'] ?></td>
                    <td><?= $tutor['email'] ?></td>
                    <td><?= $tutor['gpa'] ?></td>
                    
                    <td>
                        <a href="" class="btn btn-warning">Edit</a>
                        <a href="" class="btn btn-danger">Delete</a>
                    </td>
                </tr>

                <?php } ?>

            </tbody>
            </table>
        </div>
        <?php 
                    
        } else { ?>
            <div class="alert alert-info .w-450 m-5" role="alert">
                Empty!
            </div>
        <?php } ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js">
        
    </script>
    <script>
        $(document).ready(function(){
            $("#navLinks li:nth-child(2) a").addClass('active');
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
