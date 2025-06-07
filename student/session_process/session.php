<?php

session_start();
if (isset($_SESSION['studentid']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Student') {

        include "../../DB_connection.php";
        include "../data/session.php";
        include "../data/courseSelection.php";

        // Fetch all sessions for this student
        $sessions = getStudentSessions($conn, $_SESSION['studentid']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && 
            isset($_POST['studentid'], $_POST['tutorid'], $_POST['courseid'], $_POST['date_and_time'], $_POST['chat_response'])) {
            
            handleChatResponse(
                $conn,
                $_POST['studentid'],
                $_POST['tutorid'],
                $_POST['courseid'],
                $_POST['date_and_time'],
                $_POST['chat_response']
            );
        }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Sessions</title>
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
                If you deny to chat with tutor, the session will be denied also.<br>
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
        <input type="hidden" name="tutorid" id="denyChatTutorId">
        <input type="hidden" name="courseid" id="denyChatCourseId">
        <input type="hidden" name="date_and_time" id="denyChatDateAndTime">
        <input type="hidden" name="chat_response" value="deny">
    </form>

    <div class="container mt-5">
        <h2 class="mb-4">Your Registered Courses</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Course name</th>
                        <th>Tutor Name</th>
                        <th>Date & Time</th>
                        <th>Duration (hours)</th>
                        <th>Place</th>
                        <th>Total Price</th>
                        <th>Paid</th>
                        <th>Status</th>
                        <th>Chat with Tutor</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (count($sessions) > 0): ?>
                    <?php foreach ($sessions as $i => $session): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= htmlspecialchars($session['course_name']) ?></td>
                            <td><?= htmlspecialchars($session['tutor_name']) ?></td>
                            <td><?= htmlspecialchars($session['date_and_time']) ?></td>
                            <td><?= htmlspecialchars($session['place']) ?></td>
                            <td><?= htmlspecialchars($session['duration']) ?></td>
                            <td>
                                <?php
                                    $total = floatval($session['duration']) * floatval($session['price_per_hour']);
                                    echo number_format($total, 2);
                                ?>
                            </td>
                            <td>
                                <?= $session['paid'] ? '<span class="badge bg-success">Paid</span>' : '<span class="badge bg-warning text-dark">Unpaid</span>' ?>
                            </td>
                            <td>
                                <?php
                                    // You may need to adjust this if you have a status column
                                    if (isset($session['consensus'])) {
                                        if ($session['consensus'] == "accepted") {
                                            echo '<span class="badge bg-success">Accepted</span>';
                                        } elseif ($session['consensus'] == "denied") {
                                            echo '<span class="badge bg-danger">Rejected</span>';
                                        } else {
                                            echo '<span class="badge bg-secondary">Pending</span>';
                                        }
                                    } else {
                                        echo '<span class="badge bg-secondary">Pending</span>';
                                    }
                                ?>
                            </td>
                            <td>
                            <?php
                            if ($session['tutor_chat_requested'] && !$session['student_chat_requested']) {
                            ?>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="studentid" value="<?= htmlspecialchars($session['studentid']) ?>">
                                    <input type="hidden" name="tutorid" value="<?= htmlspecialchars($session['tutorid']) ?>">
                                    <input type="hidden" name="courseid" value="<?= htmlspecialchars($session['courseid']) ?>">
                                    <input type="hidden" name="date_and_time" value="<?= htmlspecialchars($session['date_and_time']) ?>">
                                    <input type="hidden" name="chat_response" value="accept">
                                    <button type="submit" class="btn btn-success btn-sm">Accept</button>
                                </form>
                                <button type="button"
                                            class="btn btn-danger btn-sm"
                                            onclick="showDenyChatModal('<?= htmlspecialchars($session['studentid']) ?>', '<?= htmlspecialchars($session['tutorid']) ?>', '<?= htmlspecialchars($session['courseid']) ?>', '<?= htmlspecialchars($session['date_and_time']) ?>')">
                                        Deny
                                    </button>
                            <?php
                            } else {
                                echo '<span class="text-muted">—</span>';
                            }
                            ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center">No sessions found.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function showDenyChatModal(studentid, tutorid, courseid, date_and_time) {
        document.getElementById('denyChatStudentId').value = studentid;
        document.getElementById('denyChatTutorId').value = tutorid;
        document.getElementById('denyChatCourseId').value = courseid;
        document.getElementById('denyChatDateAndTime').value = date_and_time;
        // Show the modal
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