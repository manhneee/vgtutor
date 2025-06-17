<?php
session_start();
if (isset($_SESSION['tutorid']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Tutor') {
        include "../DB_connection.php";
        include "data/session.php";
        // Notification logic
        $notifications = [];
        // New session notification
        $row = getNewSessionNotification($conn, $_SESSION['tutorid']);
        if ($row) {
            markSessionNotified($conn, $row['studentid'], $row['tutorid'], $row['courseid'], $row['date_and_time']);
            $notifications[] = [
                'type' => 'warning',
                'msg' => htmlspecialchars($row['student_name']) . " has registered your " . htmlspecialchars($row['course_name']) . " course, please check your session for more information."
            ];
        }
        // Payment confirmation notification (pending payments)
        $stmt = $conn->prepare("
            SELECT pc.*, sa.name AS student_name, c.course_name
            FROM payment_confirmation pc
            JOIN student_account sa ON pc.studentid = sa.accountid
            JOIN course c ON pc.courseid = c.courseid
            WHERE pc.tutorid = ? AND pc.status = 'pending'
            ORDER BY pc.date_and_time DESC
        ");
        $stmt->execute([$_SESSION['tutorid']]);
        $pendingPayments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($pendingPayments as $pay) {
            $notifications[] = [
                'type' => 'info',
                'msg' => "Payment confirmation from <strong>" . htmlspecialchars($pay['student_name']) . "</strong> for <strong>" . htmlspecialchars($pay['course_name']) . "</strong> is pending. <a href='session_process/session.php' class='alert-link'>Check now</a>."
            ];
        }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tutor-Home</title>
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
                <h1 style="color: white">Welcome, <?= $_SESSION['name']?> - <?=$_SESSION['tutorid'] ?></h1>
                <p style="color: white">You are logged in as an <?= $_SESSION['role'] ?>.</p>
            </div>
        </div>
    </div>

    <div class="container mt-5">
        <div class="container text-center">
            <div class="row row-cols-5">
                <a href="session_process/session.php" class="col btn bg-orange m-2 py-3">
                    <i class="fa fa-columns fs-1" aria-hidden="true"></i><br>Sessions
                </a>
                <a href="" class="col btn bg-orange m-2 py-3">
                    <i class="fa fa-calendar fs-1" aria-hidden="true"></i><br>Schedule
                </a>
                <a href="course_process/course.php" class="col btn bg-orange m-2 py-3">
                    <i class="fa fa-book fs-1" aria-hidden="true"></i><br>Courses Offering
                </a>
                <a href="chat_process/chat.php" class="col btn bg-orange m-2 py-3">
                    <i class="fa fa-comments fs-1" aria-hidden="true"></i><br>Messages
                </a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
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
        exit;
    }
} else {
    $em = "You are not logged in.";
    header("Location: ../login.php?error=$em");
    exit;
}
?>