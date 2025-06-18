<?php
session_start();
if (!isset($_SESSION['tutorid']) || $_SESSION['role'] !== 'Tutor') {
    header("Location: ../login.php?error=Unauthorized access");
    exit;
}

include_once "../DB_connection.php";
include_once "data/session.php";

$tutorid = $_SESSION['tutorid'];
$allNotifications = getAllTutorSessionNotifications($conn, $tutorid);

// Optionally mark them as seen if not already
foreach ($allNotifications as $noti) {
    if (!$noti['notified']) {
        markSessionNotified($conn, $noti['studentid'], $noti['tutorid'], $noti['courseid'], $noti['date_and_time']);
    }
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>All Notifications</title>
  <link rel="stylesheet" href="../css/style1.css">
  <link rel="stylesheet" href="../css/framework.css">
  <link rel="stylesheet" href="../css/master.css">
  <link rel="stylesheet" href="../css/notification.css">
</head>
<body>
 <div class="page d-flex">
        <?php include_once '../tutor/inc/navbar.php'; ?> <!-- LEFT BAR -->

        <div class="content w-full">
        <?php include_once '../tutor/inc/upbar.php'; ?> <!-- upbar -->

    <div class="notifications p-20">
      <h2 class="mb-4 c-orange">All Notifications</h2>

      <?php if (!empty($allNotifications)): ?>
        <ul class="notification-list">
          <?php foreach ($allNotifications as $n): ?>
            <li class="notification-item bg-white p-15 rad-6 mb-10 shadow-sm">
              <div>
                <strong><?= htmlspecialchars($n['student_name']) ?></strong> registered for 
                <em><?= htmlspecialchars($n['course_name']) ?></em> on 
                <span class="c-grey"><?= htmlspecialchars($n['date_and_time']) ?></span>.
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p class="c-grey">You have no notifications yet.</p>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
