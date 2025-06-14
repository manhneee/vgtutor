<?php

session_start();
if (isset($_SESSION['tutorid']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Tutor') {

        include "../../DB_connection.php";
        include "../data/session.php";

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['studentid'], $_POST['courseid'], $_POST['action'])) {
            handleSessionAction($conn, $_POST['studentid'], $_POST['courseid'], $_POST['action']);
        }

        $sessions = getTutorSessions($conn, $_SESSION['tutorid']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Tutor Sessions</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="icon" href="../../img/logo.png">
</head>
<body class="body-home">
    <?php include "navbar.php"; ?>

    <!-- Deny Chat Confirmation Modal -->
    <div class="modal fade" id="denyChatModal" tabindex="-1" aria-labelledby="denyChatModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="denyChatModalLabel">Confirm Denial</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                If you deny to chat with student, the session will be denied also.<br>
                Are you sure you want to continue?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDenyBtn">Continue</button>
            </div>
        </div>
    </div>
    </div>

    <!-- Hidden form for actual submission -->
    <form id="denyChatForm" method="post" style="display:none;">
        <input type="hidden" name="studentid" id="denyChatStudentId">
        <input type="hidden" name="courseid" id="denyChatCourseId">
        <input type="hidden" name="action" value="deny">
    </form>

    <div class="container mt-5">
        <h2 class="mb-4">Sessions With Your Students</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Course Name</th>
                        <th>Student Name</th>
                        <th>Major</th>
                        <th>Date & Time</th>
                        <th>Duration (hours)</th>
                        <th>Total Price</th>
                        <th>Chat with Student/Your Decision</th>
                    </tr>
                </thead>
                <tbody>`
                <?php if (count($sessions) > 0): ?>
                    <?php foreach ($sessions as $i => $session): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= htmlspecialchars($session['course_name']) ?></td>
                            <td><?= htmlspecialchars($session['student_name']) ?></td>
                            <td><?= htmlspecialchars($session['student_major']) ?></td>
                            <td><?= htmlspecialchars($session['date_and_time']) ?></td>
                            <td><?= htmlspecialchars($session['duration']) ?></td>
                            <td>
                                <?php
                                    $total = floatval($session['duration']) * floatval($session['price_per_hour']);
                                    echo number_format($total, 2);
                                ?>
                            </td>
                            <td>
                            <?php
                            if (
                                $session['student_chat_request'] &&
                                $session['consensus'] !== 'denied' &&
                                isset($session['tutor_chat_requested']) && $session['tutor_chat_requested'] == 1
                            ) {
                                echo '<span class="badge bg-secondary">Pending</span>';
                            } elseif ($session['student_chat_request']) {
                                if ($session['consensus'] == 'denied') {
                                    echo '<span class="badge bg-danger">Session Denied</span>';
                                } else {
                            ?>
                                    <form method="post" class="d-inline" style="display:inline;">
                                        <input type="hidden" name="studentid" value="<?= htmlspecialchars($session['studentid']) ?>">
                                        <input type="hidden" name="courseid" value="<?= htmlspecialchars($session['courseid']) ?>">
                                        <input type="hidden" name="action" value="accept_chat">
                                        <button type="submit" class="btn btn-success btn-sm">Accept</button>
                                    </form>
                                    <button type="button"
                                            class="btn btn-danger btn-sm"
                                            onclick="showDenyChatModal('<?= htmlspecialchars($session['studentid']) ?>', '<?= htmlspecialchars($session['courseid']) ?>')">
                                        Deny
                                    </button>
                            <?php
                                }
                            } else {
                                if ($session['consensus'] == 'accepted') {
                                    echo '<span class="badge bg-success">Accepted</span>';
                                } elseif ($session['consensus'] == 'denied') {
                                    echo '<span class="badge bg-danger">Denied</span>';
                                } else {
                            ?>
                                    <form method="post" class="d-inline">
                                        <input type="hidden" name="studentid" value="<?= htmlspecialchars($session['studentid']) ?>">
                                        <input type="hidden" name="courseid" value="<?= htmlspecialchars($session['courseid']) ?>">
                                        <input type="hidden" name="action" value="accept">
                                        <button type="submit" class="btn btn-success btn-sm">Accept</button>
                                    </form>
                                    <form method="post" class="d-inline">
                                        <input type="hidden" name="studentid" value="<?= htmlspecialchars($session['studentid']) ?>">
                                        <input type="hidden" name="courseid" value="<?= htmlspecialchars($session['courseid']) ?>">
                                        <input type="hidden" name="action" value="deny">
                                        <button type="submit" class="btn btn-danger btn-sm">Deny</button>
                                    </form>
                                    <form method="post" class="d-inline" style="display:inline;">
                                        <input type="hidden" name="studentid" value="<?= htmlspecialchars($session['studentid']) ?>">
                                        <input type="hidden" name="courseid" value="<?= htmlspecialchars($session['courseid']) ?>">
                                        <input type="hidden" name="action" value="request_chat">
                                        <button type="submit" class="btn btn-warning btn-sm">Request Chat</button>
                                    </form>
                            <?php
                                }
                            }
                            ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">No sessions found.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function showDenyChatModal(studentid, courseid) {
        document.getElementById('denyChatStudentId').value = studentid;
        document.getElementById('denyChatCourseId').value = courseid;
        var myModal = new bootstrap.Modal(document.getElementById('denyChatModal'));
        myModal.show();
        document.getElementById('confirmDenyBtn').onclick = function() {
            document.getElementById('denyChatForm').submit();
        };
    }
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
