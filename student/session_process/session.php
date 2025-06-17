<?php
session_start();
date_default_timezone_set('Asia/Bangkok');
if (isset($_SESSION['studentid']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Student') {

        include "../../DB_connection.php";
        include "../data/session.php";
        include "../data/courseSelection.php";

        // Fetch all sessions for this student
        $sessions = getStudentSessions($conn, $_SESSION['studentid']);
        // Fetch denied payments for this student
        $deniedPayments = [];
        $stmt = $conn->prepare("SELECT pc.*, sa.name AS tutor_name 
            FROM payment_confirmation pc
            JOIN student_account sa ON pc.tutorid = sa.accountid
            WHERE pc.studentid = ? AND pc.status = 'denied'
            ORDER BY pc.date_and_time DESC");
        $stmt->execute([$_SESSION['studentid']]);
        $deniedPayments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay_submit'])) {
            $studentid = $_POST['pay_studentid'];
            $tutorid = $_POST['pay_tutorid'];
            $courseid = $_POST['pay_courseid'];
            $date_and_time = $_POST['pay_date_and_time'];
            $error = $success = "";

            if (isset($_FILES['payment_img']) && $_FILES['payment_img']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['payment_img']['tmp_name'];
                $fileName = $_FILES['payment_img']['name'];
                $fileSize = $_FILES['payment_img']['size'];
                $fileType = $_FILES['payment_img']['type'];
                $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $allowedExt = ['jpg', 'jpeg', 'png', 'gif'];
                if (!in_array($fileExt, $allowedExt)) {
                    $error = "Only image files (jpg, jpeg, png, gif) are allowed.";
                } elseif ($fileSize > 5 * 1024 * 1024) {
                    $error = "File size must be less than 5MB.";
                } else {
                    $uploadDir = __DIR__ . '/../payment_process/uploads/';
                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                    $newFileName = 'pay_' . $studentid . '_' . time() . '.' . $fileExt;
                    $destPath = $uploadDir . $newFileName;
                    if (move_uploaded_file($fileTmpPath, $destPath)) {
                        // Save to payment_confirmation table
                        $stmt = $conn->prepare("INSERT INTO payment_confirmation (studentid, tutorid, courseid, date_and_time, img_path, status) VALUES (?, ?, ?, ?, ?, 'pending')");
                        $stmt->execute([$studentid, $tutorid, $courseid, $date_and_time, 'uploads/' . $newFileName]);
                        // Redirect to avoid duplicate on reload
                        header("Location: session.php?success=Payment confirmation uploaded! Please wait for tutor verification.");
                        exit;
                    } else {
                        $error = "Failed to upload image.";
                    }
                }
            } else {
                $error = "Please upload a payment confirmation image.";
            }
        }
        $tutorIds = array_unique(array_column($sessions, 'tutorid'));
        $tutorBankInfo = [];
        // Only fetch if there is at least one tutorId
        if (!empty($tutorIds)) {
            $in = implode(',', array_fill(0, count($tutorIds), '?'));
            $stmt = $conn->prepare("SELECT ta.accountid, sa.name, ta.bank_name, ta.bank_acc_no 
                                    FROM tutor_account ta 
                                    JOIN student_account sa ON ta.accountid = sa.accountid 
                                    WHERE ta.accountid IN ($in)");
            $stmt->execute(array_values($tutorIds));
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $tutorBankInfo[$row['accountid']] = $row;
            }
        }
?>

<script>
var tutorBankInfo = <?= json_encode($tutorBankInfo) ?>;
</script>


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
<?php include "../inc/navbar.php"; ?>

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
                    <th>Review</th>
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
                        <td><?= htmlspecialchars($session['duration']) ?></td>
                        <td><?= htmlspecialchars($session['place']) ?></td>
                        <td>
                            <?php
                                $total = floatval($session['duration']) * floatval($session['price_per_hour']);
                                echo number_format($total, 2);
                            ?>
                        </td>
                        <td>
                            <?php
                                // Fetch payment confirmation for this session
                                $stmtPay = $conn->prepare("SELECT status FROM payment_confirmation WHERE studentid = ? AND tutorid = ? AND courseid = ? AND date_and_time = ? ORDER BY id DESC LIMIT 1");
                                $stmtPay->execute([
                                    $session['studentid'],
                                    $session['tutorid'],
                                    $session['courseid'],
                                    $session['date_and_time']
                                ]);
                                $pay = $stmtPay->fetch(PDO::FETCH_ASSOC);

                                if ($session['paid']) {
                                    echo '<span class="badge bg-success">Paid</span>';
                                } elseif ($pay && $pay['status'] == 'pending') {
                                    echo '<span class="badge bg-warning text-dark">Pending</span>';
                                } elseif ($session['consensus'] == "accepted") {
                                    ?>
                                    <button 
                                        class="btn btn-warning btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#payModal"
                                        data-studentid="<?= htmlspecialchars($_SESSION['studentid']) ?>"
                                        data-tutorid="<?= htmlspecialchars($session['tutorid']) ?>"
                                        data-courseid="<?= htmlspecialchars($session['courseid']) ?>"
                                        data-date_and_time="<?= htmlspecialchars($session['date_and_time']) ?>"
                                    >Pay Now</button>
                                    <?php
                                } else {
                                    echo '<span class="badge bg-secondary">Unpaid</span>';
                                }
                            ?>
                        </td>
                        <td>
                            <?php
                                if (isset($session['consensus'])) {
                                    if ($session['consensus'] == "accepted") {
                                        echo '<span class="badge bg-success">Accepted</span>';
                                    } elseif ($session['consensus'] == "denied") {
                                        echo '<span class="badge bg-danger">Rejected</span>';
                                    } else {
                                        echo '<span class="badge bg-secondary">Pending</span>';
                                    }
                                }
                            ?>
                        </td>
                        <td>
                            <a class="btn btn-primary" href="../chat_process/chat.php?tutorid=<?= htmlspecialchars($session['tutorid']) ?>">Chat</a>
                        </td>
                        <td>
                            
                            <?php
                            $sessionStartTime = strtotime($session['date_and_time']);
                            $sessionEndTime = $sessionStartTime + ($session['duration'] * 3600);
                            $now = time();
                            $hasReview = checkReviewExists($conn, $_SESSION['studentid'], $session['tutorid'], $session['courseid']);
                            
                            if ($session['consensus'] == "accepted" && $sessionEndTime < $now && !$hasReview) {
                                ?>
                                <button class="btn btn-success btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#reviewModal"
                                    data-tutorid="<?= htmlspecialchars($session['tutorid']) ?>"
                                    data-courseid="<?= htmlspecialchars($session['courseid']) ?>"
                                    >Leave Review</button>
                                <?php
                            } elseif ($hasReview) {
                                echo '<span class="badge bg-info">Reviewed</span>';
                            } else {
                                echo '-';
                            }
                            ?>
                            
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="11" class="text-center">No sessions found.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Review Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="../review_process/submit_review.php" method="post" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reviewModalLabel">Leave a Review</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="studentid" value="<?= $_SESSION['studentid'] ?>">
                <input type="hidden" name="tutorid" id="reviewTutorId">
                <input type="hidden" name="courseid" id="reviewCourseId">
                <div class="mb-3">
                    <label for="reviewText" class="form-label">Your Review</label>
                    <textarea class="form-control" name="reviewText" id="reviewText" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="rating" class="form-label">Rating (1-5)</label>
                    <input type="number" class="form-control" name="rating" id="rating" min="1" max="5" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Submit Review</button>
            </div>
        </form>
    </div>
</div>
<!-- Payment Modal -->
<div class="modal fade" id="payModal" tabindex="-1" aria-labelledby="payModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="post" enctype="multipart/form-data" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="payModalLabel">Payment Confirmation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="pay_studentid" id="payStudentId">
                <input type="hidden" name="pay_tutorid" id="payTutorId">
                <input type="hidden" name="pay_courseid" id="payCourseId">
                <input type="hidden" name="pay_date_and_time" id="payDateTime">
                <div class="mb-3">
                    <label class="form-label">Bank Account Owner</label>
                    <input type="text" class="form-control" id="payOwner" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Bank Name</label>
                    <input type="text" class="form-control" id="payBank" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Bank Account Number</label>
                    <input type="text" class="form-control" id="payAccNo" readonly>
                </div>
                <div class="mb-3">
                    <label for="payment_img" class="form-label">Upload Payment Confirmation Image</label>
                    <input type="file" class="form-control" id="payment_img" name="payment_img" accept="image/*" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" name="pay_submit" class="btn btn-primary">Submit Payment</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
<script>
    var reviewModal = document.getElementById('reviewModal');
    reviewModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var tutorid = button.getAttribute('data-tutorid');
        var courseid = button.getAttribute('data-courseid');
        document.getElementById('reviewTutorId').value = tutorid;
        document.getElementById('reviewCourseId').value = courseid;
    });
</script>

<script>
var payModal = document.getElementById('payModal');
payModal.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget;
    var studentid = button.getAttribute('data-studentid');
    var tutorid = button.getAttribute('data-tutorid');
    var courseid = button.getAttribute('data-courseid');
    var date_and_time = button.getAttribute('data-date_and_time');
    document.getElementById('payStudentId').value = studentid;
    document.getElementById('payTutorId').value = tutorid;
    document.getElementById('payCourseId').value = courseid;
    document.getElementById('payDateTime').value = date_and_time;

    // Fill bank info
    if (tutorBankInfo[tutorid]) {
        document.getElementById('payOwner').value = tutorBankInfo[tutorid].name;
        document.getElementById('payBank').value = tutorBankInfo[tutorid].bank_name;
        document.getElementById('payAccNo').value = tutorBankInfo[tutorid].bank_acc_no;
    } else {
        document.getElementById('payOwner').value = '';
        document.getElementById('payBank').value = '';
        document.getElementById('payAccNo').value = '';
    }
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

function checkReviewExists($conn, $studentid, $tutorid, $courseid) {
    $stmt = $conn->prepare("SELECT 1 FROM review WHERE studentid = ? AND tutorid = ? AND courseid = ?");
    $stmt->execute([$studentid, $tutorid, $courseid]);
    return $stmt->fetchColumn() !== false;
}
?>
