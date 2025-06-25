<?php

session_start();
if (isset($_SESSION['tutorid']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Tutor') {

        include "../../DB_connection.php";
        include "../data/session.php";
        include_once dirname(__DIR__, 2) . '/student/data/notifications.php';
        // PROCESS ACCEPT / DENY ACTION
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_id'])) {
            $id = intval($_POST['confirm_id']);
            $action = $_POST['action'];
            $status = ($action === 'accept') ? 'accepted' : 'denied';
            $studentid = $_POST['studentid'];
            $courseid = $_POST['courseid'];
            $date_and_time = $_POST['date_and_time'];

            // Update payment confirmation status
            $stmt = $conn->prepare("UPDATE payment_confirmation SET status = ? WHERE id = ?");
            $stmt->execute([$status, $id]);

            // If accepted, mark session as paid
            if ($status === 'accepted') {
                $stmt2 = $conn->prepare("UPDATE session SET paid = 1 WHERE studentid = ? AND tutorid = ? AND courseid = ? AND date_and_time = ?");
                $stmt2->execute([$studentid, $_SESSION['tutorid'], $courseid, $date_and_time]);
                addNotification($conn, $studentid, $_SESSION['tutorid'], "Payment Accepted", "Your payment for the session has been <strong>accepted</strong> by the tutor. Your session is now confirmed.", "Payment");
            }

            // If denied, log reason to chat
            if ($status === 'denied' && !empty($_POST['deny_reason'])) {
                $deny_reason = trim($_POST['deny_reason']);
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
                addNotification($conn, $studentid, $_SESSION['tutorid'], "Payment Denied", "Your payment for the session was <strong>denied</strong> by the tutor. Reason: " . htmlspecialchars($deny_reason), "Payment");
            }

            header("Location: session.php?success=Payment $status.");
            exit;
        }

        $sessions = getTutorSessions($conn, $_SESSION['tutorid']);
?>
        <!DOCTYPE html>
        <html lang="en">

        <head>

            <meta charset="UTF-8">
            <link rel="stylesheet" href="../../css/session_tutor.css" />
            <link rel="stylesheet" href="../../css/style1.css" />
            <link rel="stylesheet" href="../../css/framework.css" />
            <link rel="stylesheet" href="../../css/master.css" />
            <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;500&display=swap" rel="stylesheet" />

        </head>

        <body>

            <div class="page d-flex">
                <?php include_once '../inc/navbar.php'; ?> <!-- Sidebar -->

                <div class="content w-full">
                    <?php include_once '../inc/upbar.php'; ?> <!-- Top Bar -->

                    <h1 class="p-relative c-orange">Pending Sessions With Students</h1>


                    <!-- Deny Chat Confirmation Modal -->
                    <!-- Deny Chat Confirmation Modal -->
                    <!-- <div id="denyChatModal" class="modal-overlay d-none p-fixed top-0 left-0 w-full h-full bg-black-50 d-flex align-center justify-center z-9999">
        <div class="modal-content bg-white rad-10 p-20 w-400 p-relative">
            <h3 class="mb-10">Confirm Denial</h3>
            <p>If you deny to chat with student, the session will be denied also.<br>Are you sure you want to continue?</p>
            <div class="d-flex justify-end mt-20 gap-10">
                <button class="btn-shape bg-grey c-white" onclick="closeDenyChatModal()">Cancel</button>
                <button class="btn-shape bg-red c-white" id="confirmDenyBtn">Continue</button>
            </div>
        </div>
    </div> -->


                    <!-- Hidden form for actual submission -->
                    <form id="denyChatForm" method="post" style="display:none;">
                        <input type="hidden" name="studentid" id="denyChatStudentId">
                        <input type="hidden" name="courseid" id="denyChatCourseId">
                        <input type="hidden" name="action" value="deny">
                    </form>

                    <div class="container mt-5">
                        <div class="container m-20">
                            <div class="friends-page d-grid gap-20">
                                <?php if (count($sessions) > 0): ?>
                                    <?php foreach ($sessions as $session): ?>
                                        <div class="friend bg-white rad-6 p-20 p-relative">
                                            <div class="contact">
                                                <i class="fa-solid fa-phone"></i>
                                                <i class="fa-regular fa-envelope"></i>
                                            </div>
                                            <div class="txt-c">
                                                <img class="rad-half mt-10 mb-10 w-100 h-100" src="../../img/avatar.png" alt="Student Avatar" />
                                                <h4 class="m-0">
                                                    <?= htmlspecialchars($session['student_name']) ?>
                                                    <span class="fs-13 c-grey">(
                                                        <?= htmlspecialchars($session['student_major']) ?>
                                                        )</span>
                                                </h4>
                                                <p class="c-grey fs-13 mt-5 mb-0"><?= htmlspecialchars($session['course_name']) ?></p>
                                            </div>
                                            <div class="icons fs-14 p-relative">
                                                <div class="mb-10">
                                                    <i class="fa-solid fa-calendar-days fa-fw"></i>
                                                    <span><?= htmlspecialchars($session['date_and_time']) ?></span>
                                                </div>
                                                <div class="mb-10">
                                                    <i class="fa-solid fa-school fa-fw"></i>
                                                    <span><?= htmlspecialchars($session['place']) ?></span>
                                                </div>
                                                <div class="mb-10">
                                                    <i class="fa-solid fa-clock fa-fw"></i>
                                                    <span><?= htmlspecialchars($session['duration']) ?> hr</span>
                                                </div>
                                                <div class="mb-10">
                                                    <i class="fa-solid fa-dollar-sign fa-fw"></i>
                                                    <span>
                                                        <?php
                                                        $total = floatval($session['duration']) * floatval($session['price_per_hour']);
                                                        echo number_format($total, 2);
                                                        ?>
                                                    </span>
                                                </div>
                                                <div>
                                                    <i class="fa-solid fa-check-double fa-fw"></i>
                                                    <span>
                                                        <?php
                                                        if ($session['consensus'] == 'pending') {
                                                            echo '<span class="c-grey">Pending</span>';
                                                        } elseif ($session['consensus'] == 'denied') {
                                                            echo '<span class="c-red">Session Denied</span>';
                                                        } elseif ($session['consensus'] == 'accepted') {
                                                            echo '<span class="c-green">Session Accepted</span>';
                                                        }
                                                        ?>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="info between-flex fs-13 mt-10">
                                                <span class="c-grey">Student ID: <?= htmlspecialchars($session['studentid']) ?></span>
                                                <div>
                                                    <a class="bg-orange c-white btn-shape" href="../chat/chat.php?studentid=<?= urlencode($session['studentid']) ?>">Chat</a>
                                                    <?php
                                                    $stmtPay = $conn->prepare("SELECT * FROM payment_confirmation WHERE studentid = ? AND tutorid = ? AND courseid = ? AND date_and_time = ? ORDER BY id DESC LIMIT 1");
                                                    $stmtPay->execute([
                                                        $session['studentid'],
                                                        $_SESSION['tutorid'],
                                                        $session['courseid'],
                                                        $session['date_and_time']
                                                    ]);
                                                    $pay = $stmtPay->fetch(PDO::FETCH_ASSOC);

                                                    $sessionStatus = $session['consensus'];
                                                    $imgPath = ($pay && !empty($pay['img_path']))
                                                        ? "../../student/payment_process/" . htmlspecialchars($pay['img_path'])
                                                        : '';
                                                    $paymentFound = ($pay && $pay['status'] === 'pending');
                                                    $payId = $pay['id'] ?? '';
                                                    ?>
                                                    <?php if ($session['consensus'] === 'accepted'): ?>
                                                        <a
                                                            type="button"
                                                            class="bg-orange c-white btn-shape"
                                                            onclick="handleViewPayment('<?= addslashes($session['consensus']) ?>', <?= $paymentFound ? 'true' : 'false' ?>, '<?= $payId ?>', '<?= $imgPath ?>', '<?= $session['studentid'] ?>', '<?= $session['courseid'] ?>', '<?= $session['date_and_time'] ?>')">View Payment</a>
                                                    <?php endif; ?>
                                                </div>

                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="alert alert-info text-center">No sessions found.</div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <script>
                            function showDenyChatModal(studentid, courseid) {
                                document.getElementById('denyChatStudentId').value = studentid;
                                document.getElementById('denyChatCourseId').value = courseid;

                                document.getElementById('denyChatModal').classList.remove("d-none");

                                document.getElementById('confirmDenyBtn').onclick = function() {
                                    document.getElementById('denyChatForm').submit();
                                };
                            }

                            function closeDenyChatModal() {
                                document.getElementById('denyChatModal').classList.add("d-none");
                            }

                            function showPaymentPopup(id, imgPath, studentId, courseId, dateTime) {
                                document.getElementById("payConfirmId").value = id;
                                document.getElementById("payStudentId").value = studentId;
                                document.getElementById("payCourseId").value = courseId;
                                document.getElementById("payDateTime").value = dateTime;
                                document.getElementById("paymentImage").src = imgPath;
                                document.getElementById("deny_reason").value = '';
                                document.getElementById("paymentPopup").classList.remove("d-none");

                                // Bind Accept and Deny buttons
                                const acceptBtn = document.querySelector('button[name="action"][value="accept"]');
                                const denyBtn = document.querySelector('button[name="action"][value="deny"]');
                                const reasonField = document.getElementById("deny_reason");

                                if (acceptBtn && denyBtn) {
                                    acceptBtn.onclick = function() {
                                        reasonField.removeAttribute("required");
                                    };
                                    denyBtn.onclick = function() {
                                        reasonField.setAttribute("required", "true");
                                    };
                                }
                            }

                            function closePaymentPopup() {
                                document.getElementById("paymentPopup").classList.add("d-none");
                            }

                            function handleViewPayment(sessionStatus, paymentFound, id, imgPath, studentId, courseId, dateTime) {

                                if (!paymentFound) {
                                    document.getElementById('noPaymentModal').style.display = 'flex';
                                    return;
                                }

                                // proceed to show the popup
                                document.getElementById("payConfirmId").value = id;
                                document.getElementById("payStudentId").value = studentId;
                                document.getElementById("payCourseId").value = courseId;
                                document.getElementById("payDateTime").value = dateTime;
                                document.getElementById("paymentImage").src = imgPath;
                                document.getElementById("deny_reason").value = '';
                                document.getElementById("paymentPopup").classList.remove("d-none");
                            }

                            function closeNoPaymentModal() {
                                document.getElementById('noPaymentModal').style.display = 'none';
                            }

                            function closeNoSessionModal() {
                                document.getElementById('noSessionModal').style.display = 'none';
                            }
                        </script>

                        <!-- Payment Review Modal -->
                        <div id="paymentPopup" class="modal-overlay d-none" onclick="closePaymentPopup(event)">
                            <div class="modal-content modern-modal" onclick="event.stopPropagation()">
                                <button class="modal-close" onclick="closePaymentPopup()" aria-label="Close">&times;</button>

                                <h2 class="modal-title">Review Payment</h2>

                                <div class="payment-image-wrapper">
                                    <img id="paymentImage" alt="Payment Screenshot" />
                                </div>

                                <form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" class="modal-form">
                                    <input type="hidden" name="confirm_id" id="payConfirmId" />
                                    <input type="hidden" name="studentid" id="payStudentId" />
                                    <input type="hidden" name="courseid" id="payCourseId" />
                                    <input type="hidden" name="date_and_time" id="payDateTime" />

                                    <label for="deny_reason" class="modal-label">Reason (if denying):</label>
                                    <textarea id="deny_reason" name="deny_reason" class="modal-textarea" placeholder="Type your reason here..."></textarea>

                                    <div class="modal-actions">
                                        <button type="submit" name="action" value="accept" class="btn-shape bg-green c-white">Accept</button>
                                        <button type="submit" name="action" value="deny" class="btn-shape bg-red c-white">Deny</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div id="noPaymentModal" class="modal-overlay" onclick="closeNoPaymentModal(event)">
                            <div class="modal-box" onclick="event.stopPropagation()">
                                <button class="modal-close" onclick="closeNoPaymentModal()" aria-label="Close">&times;</button>
                                <p class="modal-message">No payment found yet.</p>
                            </div>
                        </div>



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