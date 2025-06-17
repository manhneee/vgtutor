<?php

session_start();
if (isset($_SESSION['tutorid']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Tutor') {

        include "../../DB_connection.php";
        include "../data/session.php";

        $sessions = getTutorSessions($conn, $_SESSION['tutorid']);
        
        // Handle denial of payment with explanation
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_id'])) {
            $id = intval($_POST['confirm_id']);
            $action = $_POST['action'];
            $status = ($action === 'accept') ? 'accepted' : 'denied';
            $studentid = $_POST['studentid'];
            $courseid = $_POST['courseid'];
            $date_and_time = $_POST['date_and_time'];

            $stmt = $conn->prepare("UPDATE payment_confirmation SET status = ? WHERE id = ?");
            $stmt->execute([$status, $id]);
            // Optionally, update session.paid if accepted
            if ($status === 'accepted') {
                $stmt2 = $conn->prepare("UPDATE session SET paid = 1 WHERE studentid = ? AND tutorid = ? AND courseid = ? AND date_and_time = ?");
                $stmt2->execute([$studentid, $_SESSION['tutorid'], $courseid, $date_and_time]);
            }
            // If denied, send explanation to chat
            if ($status === 'denied' && !empty($_POST['deny_reason'])) {
                $deny_reason = trim($_POST['deny_reason']);
                // Save to chatlog.json (same as your chat system)
                $file = $_SERVER['DOCUMENT_ROOT'] . "/vgtutor/chatlog.json";
                if (!file_exists($file)) file_put_contents($file, '[]');
                $messages = json_decode(file_get_contents($file), true);
                $messages[] = [
                    'userid' => $_SESSION['tutorid'],
                    'username' => $_SESSION['name'],
                    'studentid' => $studentid,
                    'tutorid' => $_SESSION['tutorid'],
                    'text' => '[Payment Denied] ' . $deny_reason,
                    'time' => date('H:i')
                ];
                file_put_contents($file, json_encode($messages));
            }
            header("Location: session.php?success=Payment $status.");
            exit;
        }
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
    <?php include "../inc/navbar.php"; ?>

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
                        <th>Payment Status</th> 
                        <th>Status</th>
                        <th>Chat with Student</th>
                    </tr>
                </thead>
                <tbody>
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
                                // Fetch payment confirmation for this session
                                $stmtPay = $conn->prepare("SELECT * FROM payment_confirmation WHERE studentid = ? AND tutorid = ? AND courseid = ? AND date_and_time = ? ORDER BY id DESC LIMIT 1");
                                $stmtPay->execute([
                                    $session['studentid'],
                                    $_SESSION['tutorid'],
                                    $session['courseid'],
                                    $session['date_and_time']
                                ]);
                                $pay = $stmtPay->fetch(PDO::FETCH_ASSOC);

                                if ($session['paid']) {
                                    echo '<span class="badge bg-success">Paid</span>';
                                } elseif ($pay) {
                                    // Show image and accept/deny buttons if status is pending
                                    echo '<a href="../../student/payment_process/' . htmlspecialchars($pay['img_path']) . '" target="_blank"><img src="../../student/payment_process/' . htmlspecialchars($pay['img_path']) . '" alt="Payment" style="max-width:80px;"></a><br>';
                                    if ($pay['status'] == 'pending') {
                                        ?>
                                        <form method="post" style="display:inline;">
                                            <input type="hidden" name="confirm_id" value="<?= $pay['id'] ?>">
                                            <input type="hidden" name="studentid" value="<?= $session['studentid'] ?>">
                                            <input type="hidden" name="courseid" value="<?= $session['courseid'] ?>">
                                            <input type="hidden" name="date_and_time" value="<?= $session['date_and_time'] ?>">
                                            <button type="submit" name="action" value="accept" class="btn btn-success btn-sm">Accept</button>
                                            <button type="button"
                                                class="btn btn-danger btn-sm"
                                                onclick="showDenyPaymentModal(
                                                    '<?= $pay['id'] ?>',
                                                    '<?= htmlspecialchars($session['studentid']) ?>',
                                                    '<?= htmlspecialchars($session['courseid']) ?>',
                                                    '<?= htmlspecialchars($session['date_and_time']) ?>'
                                                )"
                                            >Deny</button>
                                        </form>
                                        <?php
                                    } elseif ($pay['status'] == 'accepted') {
                                        echo '<span class="badge bg-success">Confirmed</span>';
                                    } elseif ($pay['status'] == 'denied') {
                                        echo '<span class="badge bg-danger">Denied</span>';
                                    }
                                } else {
                                    echo '<span class="badge bg-warning text-dark">Unpaid</span>';
                                }
                                ?>
                            </td>
                            <td>
                            <?php
                            if ($session['consensus'] == 'pending') {
                                echo '<span class="badge bg-secondary">Pending</span>';
                            } elseif ($session['consensus'] == 'denied') {
                                echo '<span class="badge bg-danger">Session Denied</span>';
                            } elseif($session['consensus'] == 'accepted') {
                                echo '<span class="badge bg-success">Session Accepted</span>';
                            }
                            ?>
                            </td>
                            <td>
                            <a href="../chat/chat.php?studentid=<?= urlencode($session['studentid']) ?>" class="btn btn-primary">Chat</a>
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
    <div class="modal fade" id="denyPaymentModal" tabindex="-1" aria-labelledby="denyPaymentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="denyPaymentForm" method="post">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="denyPaymentModalLabel">Deny Payment & Explain</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                <input type="hidden" name="confirm_id" id="denyPayId">
                <input type="hidden" name="studentid" id="denyPayStudentId">
                <input type="hidden" name="courseid" id="denyPayCourseId">
                <input type="hidden" name="date_and_time" id="denyPayDateTime">
                <input type="hidden" name="action" value="deny">
                <div class="mb-3">
                    <label for="denyReason" class="form-label">Explain to the student why you denied the payment:</label>
                    <textarea class="form-control" name="deny_reason" id="denyReason" rows="4" required></textarea>
                </div>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger">Deny & Send</button>
                </div>
            </div>
            </form>
        </div>
    </div>
    <script>
        function showDenyPaymentModal(id, studentid, courseid, date_and_time) {
            document.getElementById('denyPayId').value = id;
            document.getElementById('denyPayStudentId').value = studentid;
            document.getElementById('denyPayCourseId').value = courseid;
            document.getElementById('denyPayDateTime').value = date_and_time;
            document.getElementById('denyReason').value = '';
            var myModal = new bootstrap.Modal(document.getElementById('denyPaymentModal'));
            myModal.show();
        }
    </script>
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
