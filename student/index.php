<?php 
session_start();
if (isset($_SESSION['studentid']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Student') {
        $showDeniedMsg = false;
        $deniedAt = null;
        if (isset($_SESSION['studentid'])) {
            include_once $_SERVER['DOCUMENT_ROOT'] . "/vgtutor/DB_connection.php";
            $stmt = $conn->prepare("SELECT status, denied_at FROM tutor_registration WHERE studentid = ? ORDER BY denied_at DESC LIMIT 1");
            $stmt->execute([$_SESSION['studentid']]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row && $row['status'] === 'denied' && $row['denied_at']) {
                $deniedAt = strtotime($row['denied_at']);
                // Show alert only if this is the first page load after denial (within 10 seconds)
                if (time() - $deniedAt < 10) {
                    $showDeniedMsg = true;
                }
            }
        }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student-Home</title>
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
                <h1 style="color: white">Welcome, <?= $_SESSION['name']?> - <?=$_SESSION['studentid'] ?></h1>
                <p style="color: white">You are logged in as an <?= $_SESSION['role'] ?>.</p>
            </div>
        </div>
    <div class="container mt-3">
        <?php if ($showDeniedMsg): ?>
            <div class="alert alert-danger text-center" id="denied-alert">
                Your application to become a Tutor has been <strong>denied</strong>. Please register again after 3 days.
            </div>
            <script>
                setTimeout(function() {
                    var alertBox = document.getElementById('denied-alert');
                    if (alertBox) alertBox.style.display = 'none';
                }, 30000); // 30 seconds
            </script>
        <?php endif; ?>
    </div>
    <div class="container mt-5">
        <div class="container text-center">
            <div class="row row-cols-5">
                <a href="tutor.php" class="col btn bg-orange m-2 py-3">
                    <i class="fa fa-user-md fs-1" aria-hidden="true"></i><br>Tutors
                </a>
                <a href="" class="col btn bg-orange m-2 py-3">
                    <i class="fa fa-users fs-1" aria-hidden="true"></i><br>Students
                </a>
                <a href="" class="col btn bg-orange m-2 py-3">
                    <i class="fa fa-pencil-square fs-1" aria-hidden="true"></i><br>Registration Office
                </a>
                <a href="" class="col btn bg-orange m-2 py-3">
                    <i class="fa fa-cubes fs-1" aria-hidden="true"></i><br>Class
                </a>
                <a href="session_process/session.php" class="col btn bg-orange m-2 py-3">
                    <i class="fa fa-columns fs-1" aria-hidden="true"></i><br>Session
                </a>
                <a href="" class="col btn bg-orange m-2 py-3">
                    <i class="fa fa-calendar fs-1" aria-hidden="true"></i><br>Schedule
                </a>
                <a href="course_process/courseSelection.php " class="col btn bg-orange m-2 py-3">
                    <i class="fa fa-book fs-1" aria-hidden="true"></i><br>Register Courses
                </a>
                <a href="chat_process/chat.php" class="col btn bg-orange m-2 py-3">
                    <i class="fa fa-comments fs-1" aria-hidden="true"></i><br>Messages
                </a>
                <a href="" class="col btn bg-primary m-2 py-3 col-5 text-white">
                    <i class="fa fa-cogs fs-1" aria-hidden="true"></i><br>Settings
                </a>
                <a href="../logout.php" class="col btn btn-warning m-2 py-3 col-5 text-white">
                    <i class="fa fa-sign-out fs-1" aria-hidden="true"></i><br>Logout
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
