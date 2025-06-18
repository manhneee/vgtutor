<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once $_SERVER['DOCUMENT_ROOT'] . "/vgtutor/DB_connection.php";

if (isset($_POST['submit_error'])) {
    $subject = trim($_POST['error_subject']);
    $message = trim($_POST['error_message']);
    $user = isset($_SESSION['studentid']) ? $_SESSION['studentid'] : (isset($_SESSION['tutorid']) ? $_SESSION['tutorid'] : 'Guest');
    $source = 'student';
    // Save to database (match your table columns)
    $stmt = $conn->prepare("INSERT INTO error_reports (datetime, user, subject, message, source) VALUES (NOW(), ?, ?, ?, ?)");
    $stmt->execute([$user, $subject, $message, $source]);

    // Optional: Show a success message (JS alert)
    echo "<script>alert('Your message has been sent to the admin.');</script>";
}

$showBecomeTutor = true;
$notifications = [];

if (isset($_SESSION['studentid'])) {
    include_once $_SERVER['DOCUMENT_ROOT'] . "/vgtutor/DB_connection.php";
    if (isset($conn)) {
        // Check if student is already a tutor
        $stmt = $conn->prepare("SELECT accountid FROM tutor_account WHERE accountid = ?");
        $stmt->execute([$_SESSION['studentid']]);
        if ($stmt->fetch()) {
            $showBecomeTutor = false;
        }

        // Tutor registration denied notification
        $stmt = $conn->prepare("SELECT status, denied_at FROM tutor_registration WHERE studentid = ? ORDER BY denied_at DESC LIMIT 1");
        $stmt->execute([$_SESSION['studentid']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row && $row['status'] === 'denied' && $row['denied_at']) {
            $deniedAt = strtotime($row['denied_at']);
            if (time() - $deniedAt < 86400) { // Show for 1 day
                $notifications[] = [
                    'type' => 'danger',
                    'msg' => 'Your application to become a Tutor has been <strong>denied</strong>. Please register again after 3 days.'
                ];
            }
        }

        // Payment denied notification
        if (isset($_POST['notif_seen'])) {
            $_SESSION['student_notif_seen'] = true;
            // Prevent form resubmission
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit;
        }
        $stmt = $conn->prepare("
            SELECT pc.*, sa.name AS tutor_name 
            FROM payment_confirmation pc
            JOIN student_account sa ON pc.tutorid = sa.accountid
            WHERE pc.studentid = ? 
              AND pc.status = 'denied'
              AND NOT EXISTS (
                SELECT 1 FROM payment_confirmation pc2
                WHERE pc2.studentid = pc.studentid
                  AND pc2.tutorid = pc.tutorid
                  AND pc2.courseid = pc.courseid
                  AND pc2.date_and_time = pc.date_and_time
                  AND pc2.status = 'accepted'
                  AND pc2.id > pc.id
              )
            ORDER BY pc.date_and_time DESC
        ");
        $stmt->execute([$_SESSION['studentid']]);
        $paymentDenied = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($paymentDenied as $pay) {
            $notifications[] = [
                'type' => 'danger',
                'msg' => 'Your payment to tutor <strong>' . htmlspecialchars($pay['tutor_name']) . '</strong> for session on <strong>' . htmlspecialchars($pay['date_and_time']) . '</strong> has been <strong>denied</strong>. Please <a href="/vgtutor/student/chat_process/chat.php?tutorid=' . htmlspecialchars($pay['tutorid']) . '" class="alert-link">chat with tutor</a>.'
            ];
        }
    } else {
        $showBecomeTutor = false;
    }
}
?>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">
            <img src="../img/logo.png" alt="Logo" width="200" height="" class="d-inline-block align-text-top">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0" id="navLinks">
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="index.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="tutor.php">Tutors</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Students</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Registration-Office</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Class</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Section</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Schedule</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Course</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Message</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Settings</a>
                </li>
            </ul>
            <ul class="navbar-nav me-right mb-2 mb-lg-0">
                <?php if ($showBecomeTutor): ?>
                <li class="nav-item">
                    <a class="btn me-2" 
                        style="border: 2px solid #f47119; color: #f47119; background: #fff;" 
                        href="/vgtutor/student/signupTutor/signupTutor.php">
                        Become a Tutor
                    </a>
                </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="btn btn-outline-primary me-2" href="switch_to_tutor.php">Switch to Tutor Mode</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../logout.php">Sign Out</a>
                </li>
            </ul>
        </div>
    </div>
</nav>