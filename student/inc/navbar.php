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
        <a class="navbar-brand" href="/vgtutor/student/index.php">
            <img src="/vgtutor/img/logo.png" alt="Logo" width="200" height="" class="d-inline-block align-text-top">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0" id="navLinks">
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="../index.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/vgtutor/student/session_process/session.php">Sessions</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Schedule</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/vgtutor/student/course_process/course.php">Course</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/vgtutor/student/chat_process/chat.php">Message</a>
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
                <?php if (!$showBecomeTutor): ?>
                <li class="nav-item">
                    <a class="btn btn-outline-primary me-2" href="/vgtutor/student/switch_to_tutor.php">Switch to Tutor Mode</a>
                </li>
                <?php endif; ?>
                <!-- Notification Dropdown -->
                <li class="nav-item dropdown">
                    <form method="post" id="notifForm" style="display:none;">
                        <input type="hidden" name="notif_seen" value="1">
                    </form>
                    <a class="btn me-2 dropdown-toggle position-relative" href="#" id="notifDropdown" role="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-bell"></i>
                        <?php if (empty($_SESSION['student_notif_seen'])): ?>
                            <?php if (count($notifications) > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    <?= count($notifications) ?>
                                </span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notifDropdown" style="min-width: 350px;">
                        <?php foreach ($notifications as $notif): ?>
                            <li>
                                <div class="alert alert-<?= $notif['type'] ?> mb-1 py-2 px-3" style="font-size: 0.95em;">
                                    <?= $notif['msg'] ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var notifDropdown = document.getElementById('notifDropdown');
                    notifDropdown && notifDropdown.addEventListener('show.bs.dropdown', function() {
                        <?php if (empty($_SESSION['student_notif_seen'])): ?>
                        document.getElementById('notifForm').submit();
                        <?php endif; ?>
                    });
                });
                </script>

                <li class="nav-item">
                    <a class="nav-link" href="/vgtutor/logout.php">Sign Out</a>
                </li>
            </ul>
        </div>
    </div>
    <!-- Report Error / Contact Admin Floating Button -->
    <button type="button" id="contactAdminBtn" class="btn btn-danger rounded-circle"
            style="position: fixed; bottom: 30px; right: 30px; z-index: 1050; width:60px; height:60px; box-shadow: 0 2px 8px rgba(0,0,0,0.2);"
            data-bs-toggle="modal" data-bs-target="#contactAdminModal" title="Report Error / Contact Admin">
        <i class="fa fa-exclamation-triangle"></i>
    </button>

    <!-- Contact Admin Modal -->
    <div class="modal fade" id="contactAdminModal" tabindex="-1" aria-labelledby="contactAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="post" action="" class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="contactAdminModalLabel">Report Error / Contact Admin</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <div class="mb-3">
            <label for="error_subject" class="form-label">Subject</label>
            <input type="text" class="form-control" id="error_subject" name="error_subject" required>
            </div>
            <div class="mb-3">
            <label for="error_message" class="form-label">Message</label>
            <textarea class="form-control" id="error_message" name="error_message" rows="5" required></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" name="submit_error" class="btn btn-primary">Send</button>
        </div>
        </form>
    </div>
    </div>
</nav>