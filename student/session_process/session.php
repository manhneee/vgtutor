<?php

session_start();
if (isset($_SESSION['studentid']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Student') {

        include "../../DB_connection.php";
        include "../data/session.php";
        include "../data/courseSelection.php";

        // Fetch all sessions for this student
        $sessions = getStudentSessions($conn, $_SESSION['studentid']);
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
                            <th>Chat with Tutors</th>
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
                                    <?php
                                        echo $session['paid']
                                            ? '<span class="status-badge status-permitted">Paid</span>'
                                            : '<span class="status-badge status-pending">Unpaid</span>';
                                    ?>
                                </td>
                                <td>
                                    <?php
                                        if (isset($session['consensus'])) {
                                            $consensus = strtolower($session['consensus']);
                                            if ($consensus === 'accepted') {
                                                echo '<span class="status-badge status-permitted">Accepted</span>';
                                            } elseif ($consensus === 'denied') {
                                                echo '<span class="status-badge status-denied">Rejected</span>';
                                            } else {
                                                echo '<span class="status-badge status-pending">Pending</span>';
                                            }
                                        }
                                    ?>
                                </td>
                                <td>
                                    <a class="btn-shape bg-orange c-white" href="../chat_process/chat.php?tutorid=<?= htmlspecialchars($session['tutorid']) ?>">Chat</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="10" class="text-center">No sessions found.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
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
?>