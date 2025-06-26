<?php
session_start();
date_default_timezone_set('Asia/Bangkok');
if (isset($_SESSION['studentid']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Student') {

        include "../../DB_connection.php";
        include "../data/session.php";
        include "../data/courseSelection.php";
        include_once dirname(__DIR__, 2) . '/student/data/notifications.php';

        $sessions = getStudentSessions($conn, $_SESSION['studentid']);

        $stmt = $conn->prepare("SELECT pc.*, sa.name AS tutor_name FROM payment_confirmation pc JOIN student_account sa ON pc.tutorid = sa.accountid WHERE pc.studentid = ? AND pc.status = 'denied' ORDER BY pc.date_and_time DESC");
        $stmt->execute([$_SESSION['studentid']]);
        $deniedPayments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay_submit'])) {
            // collect variables
            $studentid = $_POST['pay_studentid'];
            $tutorid = $_POST['pay_tutorid'];
            $courseid = $_POST['pay_courseid'];
            $date_and_time = $_POST['pay_date_and_time'];

            // file validation
            if (isset($_FILES['payment_img']) && $_FILES['payment_img']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['payment_img']['tmp_name'];
                $fileName = $_FILES['payment_img']['name'];
                $fileSize = $_FILES['payment_img']['size'];
                $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $allowedExt = ['jpg', 'jpeg', 'png', 'gif'];

                if (in_array($fileExt, $allowedExt) && $fileSize <= 5 * 1024 * 1024) {
                    $uploadDir = __DIR__ . '/../payment_process/uploads/';
                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

                    $newFileName = 'pay_' . $studentid . '_' . time() . '.' . $fileExt;
                    $destPath = $uploadDir . $newFileName;

                    if (move_uploaded_file($fileTmpPath, $destPath)) {
                        $stmt = $conn->prepare("INSERT INTO payment_confirmation (studentid, tutorid, courseid, date_and_time, img_path, status) VALUES (?, ?, ?, ?, ?, 'pending')");
                        $stmt->execute([$studentid, $tutorid, $courseid, $date_and_time, 'uploads/' . $newFileName]);
                        $studentName = $_SESSION['name'];
                        $course_name = getCourseName($conn, $courseid);

                        $notifyMsg = "$studentName has submitted a payment for the session of  on $date_and_time.";
                        addNotification(
                            $conn,
                            $tutorid,          // user_id_receive (tutor sẽ nhận thông báo)
                            $studentid,        // user_id_send (student là người gửi)
                            "New Payment Submitted", // title
                            $notifyMsg,        // message
                            "Payment"          // type
                        );
                        header("Location: session.php?success=Payment uploaded");
                        exit;
                    } else {
                        echo "Failed to move uploaded file.";
                    }
                } else {
                    echo "Invalid file type or size too large.";
                }
            } else {
                echo "No file uploaded.";
            }
        }



        $tutorIds = array_unique(array_column($sessions, 'tutorid'));
        $tutorBankInfo = [];
        if (!empty($tutorIds)) {
            $in = implode(',', array_fill(0, count($tutorIds), '?'));
            $stmt = $conn->prepare("SELECT ta.accountid, sa.name, ta.bank_name, ta.bank_acc_no FROM tutor_account ta JOIN student_account sa ON ta.accountid = sa.accountid WHERE ta.accountid IN ($in)");
            $stmt->execute(array_values($tutorIds));
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $tutorBankInfo[$row['accountid']] = $row;
            }
        }
?>

        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <title>Your Sessions</title>
            <link rel="stylesheet" href="../../css/course_offered.css" />
            <link rel="stylesheet" href="../../css/style1.css" />
            <link rel="stylesheet" href="../../css/framework.css" />
            <link rel="stylesheet" href="../../css/master.css" />
            <link rel="icon" href="../../img/logo.png">
            <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
        </head>


        <body>
            <div class="page d-flex">
                <?php include_once '../inc/navbar.php'; ?> <!-- LEFT SIDEBAR -->

                <div class="content w-full">
                    <?php include_once '../inc/upbar.php'; ?> <!-- upbar -->

                    <div class="section">
                        <div class="card">
                            <h3><i class="fa fa-book"></i> Your Registered Tutors</h3>
                            <table class="modern-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Course Name</th>
                                        <th>Tutor Name</th>
                                        <th>Date & Time</th>
                                        <th>Place</th>
                                        <th>Duration (hours)</th>
                                        <th>Total Price</th>
                                        <th>Paid</th>
                                        <th>Status</th>
                                        <th>Chat</th>
                                        <th>Payment Action</th>
                                        <th>Review</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($sessions as $i => $session): ?>

                                        <?php
                                        // 🔀 Fetch payment status first so it's usable below
                                        $stmtPay = $conn->prepare("
        SELECT status 
        FROM payment_confirmation 
        WHERE studentid = ? AND tutorid = ? AND courseid = ? AND date_and_time = ?
        ORDER BY id DESC LIMIT 1
    ");
                                        $stmtPay->execute([
                                            $_SESSION['studentid'],
                                            $session['tutorid'],
                                            $session['courseid'],
                                            $session['date_and_time']
                                        ]);
                                        $paymentStatus = $stmtPay->fetch(PDO::FETCH_ASSOC);
                                        ?>

                                        <tr>
                                            <td><?= $i + 1 ?></td>
                                            <td><?= htmlspecialchars($session['course_name']) ?></td>
                                            <td><?= htmlspecialchars($session['tutor_name']) ?></td>
                                            <td><?= htmlspecialchars($session['date_and_time']) ?></td>
                                            <td><?= htmlspecialchars($session['place']) ?></td>
                                            <td><?= htmlspecialchars($session['duration']) ?></td>
                                            <td><?= number_format(floatval($session['duration']) * floatval($session['price_per_hour']), 2) ?></td>

                                            <td>
                                                <?php
                                                if ($paymentStatus && $paymentStatus['status'] === 'denied') {
                                                    echo '<span class="status-badge status-denied">Denied</span>';
                                                } elseif ($paymentStatus && $paymentStatus['status'] === 'pending') {
                                                    echo '<span class="status-badge status-pending">Pending</span>';
                                                } elseif ($session['paid']) {
                                                    echo '<span class="status-badge status-permitted">accepted</span>';
                                                } else {
                                                    echo '<span class="status-badge status-pending">Unpaid</span>';
                                                }

                                                ?>
                                            </td>



                                            </td>

                                            <td>
                                                <?php
                                                $consensus = strtolower($session['consensus'] ?? '');
                                                echo match ($consensus) {
                                                    'accepted' => '<span class="status-badge status-permitted">Accepted</span>',
                                                    'denied' => '<span class="status-badge status-denied">Rejected</span>',
                                                    default => '<span class="status-badge status-pending">Pending</span>'
                                                };
                                                ?>
                                            </td>
                                            <td>
                                                <a class="btn-shape bg-orange c-white" href="../chat_process/chat.php?tutorid=<?= htmlspecialchars($session['tutorid']) ?>">Chat</a>
                                            </td>
                                            <td>
                                                <?php
                                                // Payment action logic
                                                if ($paymentStatus && $paymentStatus['status'] === 'denied') {
                                                    // Payment denied: allow student to pay again
                                                    echo '<a class="btn-shape bg-orange c-white"
                                                            data-studentid="' . $_SESSION['studentid'] . '"
                                                            data-tutorid="' . $session['tutorid'] . '"
                                                            data-courseid="' . $session['courseid'] . '"
                                                            data-date_and_time="' . $session['date_and_time'] . '"
                                                            onclick="openPayModal(this)">Pay Now</a>';
                                                } elseif ($session['paid']) {
                                                    // Payment accepted
                                                    echo '<span class="status-badge status-permitted">Paid</span>';
                                                } elseif ($paymentStatus && $paymentStatus['status'] === 'pending') {
                                                    // Payment is pending tutor review
                                                    echo '<span class="status-badge status-pending">Pending</span>';
                                                } elseif ($session['consensus'] === "accepted") {
                                                    // Session accepted, allow payment
                                                    echo '<a class="btn-shape bg-orange c-white"
                                                            data-studentid="' . $_SESSION['studentid'] . '"
                                                            data-tutorid="' . $session['tutorid'] . '"
                                                            data-courseid="' . $session['courseid'] . '"
                                                            data-date_and_time="' . $session['date_and_time'] . '"
                                                            onclick="openPayModal(this)">Pay Now</a>';
                                                } else {
                                                    echo '-';
                                                }
                                                ?>
                                            </td>

                                            <td>
                                                <?php
                                                $sessionStartTime = strtotime($session['date_and_time']);
                                                $sessionEndTime = $sessionStartTime + ($session['duration'] * 3600);
                                                $now = time();
                                                $hasReview = checkReviewExists($conn, $_SESSION['studentid'], $session['tutorid'], $session['courseid']);
                                                if ($session['consensus'] === "accepted" && $sessionEndTime < $now && !$hasReview) {
                                                    echo '<a class="btn-shape bg-orange c-white" data-tutorid="' . $session['tutorid'] . '" data-courseid="' . $session['courseid'] . '" onclick="openReviewModal(this)">Leave Review</a>';
                                                } elseif ($hasReview) {
                                                    echo '<span class="status-badge status-info">Reviewed</span>';
                                                } else {
                                                    echo '-';
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                var tutorBankInfo = <?= json_encode($tutorBankInfo) ?>;

                function openPayModal(btn) {
                    const modal = document.getElementById("payModal");
                    modal.style.display = "block";

                    document.getElementById("payStudentId").value = btn.dataset.studentid;
                    document.getElementById("payTutorId").value = btn.dataset.tutorid;
                    document.getElementById("payCourseId").value = btn.dataset.courseid;
                    document.getElementById("payDateTime").value = btn.dataset.date_and_time;

                    const info = tutorBankInfo[btn.dataset.tutorid] || {};
                    document.getElementById("payOwner").value = info.name || '';
                    document.getElementById("payBank").value = info.bank_name || '';
                    document.getElementById("payAccNo").value = info.bank_acc_no || '';
                }

                function openReviewModal(btn) {
                    const modal = document.getElementById("reviewModal");
                    modal.style.display = "block";

                    document.getElementById("reviewTutorId").value = btn.dataset.tutorid;
                    document.getElementById("reviewCourseId").value = btn.dataset.courseid;
                }

                function closeModal(id) {
                    document.getElementById(id).style.display = "none";
                }
            </script>

            <!-- Payment Modal -->
            <div class="popup" id="payModal" style="display: none;">Add commentMore actions
                <div style="max-width:500px;margin:60px auto;padding:28px 26px;position:relative;">
                    <form method="post" enctype="multipart/form-data" class="popup-content">
                        <h3>Payment Confirmation</h3>
                        <input type="hidden" name="pay_studentid" id="payStudentId">
                        <input type="hidden" name="pay_tutorid" id="payTutorId">
                        <input type="hidden" name="pay_courseid" id="payCourseId">
                        <input type="hidden" name="pay_date_and_time" id="payDateTime">

                        <label>Bank Owner</label>
                        <input type="text" id="payOwner" class="input" readonly>

                        <label>Bank Name</label>
                        <input type="text" id="payBank" class="input" readonly>

                        <label>Bank Account</label>
                        <input type="text" id="payAccNo" class="input" readonly>

                        <label>Upload Screenshot</label>
                        <input type="file" name="payment_img" class="input" required>

                        <div class="buttons">
                            <button type="submit" name="pay_submit" class="btn-shape bg-orange">Submit</button>
                            <button type="button" class="btn-shape cancel-btn" onclick="closeModal('payModal')">Cancel</button>

                        </div>
                    </form>
                </div>
            </div>
            <!-- Review Modal -->
            <div class="popup" id="reviewModal" style="display: none;">Add commentMore actions
                <div style="max-width:500px;margin:60px auto;padding:28px 26px;position:relative;">
                    <form action="../review_process/submit_review.php" method="post" class="popup-content">
                        <h3>Leave a Review</h3>
                        <input type="hidden" name="studentid" value="<?= $_SESSION['studentid'] ?>">
                        <input type="hidden" name="tutorid" id="reviewTutorId">
                        <input type="hidden" name="courseid" id="reviewCourseId">

                        <label>Your Review</label>
                        <textarea name="reviewText" class="input" rows="4" required></textarea>

                        <label>Rating (1-5)</label>
                        <input type="number" name="rating" min="1" max="5" class="input" required>

                        <div class="buttons">
                            <button type="submit" class="btn-shape bg-orange c-white">Submit</button>
                            <button type="button" onclick="closeModal('reviewModal')" class="btn-shape">Cancel</button>
                        </div>
                    </form>
                </div>
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

function checkReviewExists($conn, $studentid, $tutorid, $courseid)
{
    $stmt = $conn->prepare("SELECT 1 FROM review WHERE studentid = ? AND tutorid = ? AND courseid = ?");
    $stmt->execute([$studentid, $tutorid, $courseid]);
    return $stmt->fetchColumn() !== false;
}


?>