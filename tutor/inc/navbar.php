<?php
if (isset($_POST['submit_error'])) {
    $subject = trim($_POST['error_subject']);
    $message = trim($_POST['error_message']);
    $user = isset($_SESSION['studentid']) ? $_SESSION['studentid'] : (isset($_SESSION['tutorid']) ? $_SESSION['tutorid'] : 'Guest');
    $source = 'tutor';
    // Save to database (match your table columns)
    $stmt = $conn->prepare("INSERT INTO error_reports (datetime, user, subject, message, source) VALUES (NOW(), ?, ?, ?, ?)");
    $stmt->execute([$user, $subject, $message, $source]);
    // Optional: Show a success message (JS alert)
    echo "<script>alert('Your message has been sent to the admin.');</script>";
}
?>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="/vgtutor/tutor/index.php">
            <img src="/vgtutor/img/logo.png" alt="Logo" width="200" height="" class="d-inline-block align-text-top">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0" id="navLinks">
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="/vgtutor/tutor/index.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/vgtutor/tutor/session_process/session.php">Sessions</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Schedule</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/vgtutor/tutor/course_process/course.php">Course</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/vgtutor/tutor/chat_process/chat.php">Message</a>
                </li>
            </ul>
            
            <ul class="navbar-nav me-right mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="btn btn-outline-primary me-2" href="/vgtutor/tutor/switch_to_student.php">Switch to Student Mode</a>
                </li>
               
                <li class="nav-item dropdown">
                    <a class="btn me-2 dropdown-toggle position-relative" href="#" id="notifDropdown" role="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-bell"></i>
                        <?php if (empty($_SESSION['tutor_notif_seen'])): ?>
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