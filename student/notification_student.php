<?php 
session_start();
if (!isset($_SESSION['studentid']) || $_SESSION['role'] !== 'Student') {
    header("Location: ../login.php?error=Unauthorized access");
    exit;
}

include_once "../DB_connection.php";
include_once "data/session.php";

$studentid = $_SESSION['studentid'];
$allNotifications = getAllStudentSessionNotifications($conn, $studentid);

// Optionally mark them as seen
foreach ($allNotifications as $noti) {
    if (!$noti['notified']) {
        markStudentSessionNotified($conn, $noti['studentid'], $noti['tutorid'], $noti['courseid'], $noti['date_and_time']);
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
    <?php include_once 'inc/navbar.php'; ?> <!-- LEFT BAR -->

    <div class="content w-full">
      <?php include_once 'inc/upbar.php'; ?> <!-- UPBAR -->

      <div class="notifications p-20">
        <h2 class="mb-4 c-orange">All Notifications</h2>

<?php if (!empty($allNotifications)): ?>
  <ul class="notification-list">
    <?php foreach ($allNotifications as $n): ?>
      <li class="notification-item bg-white p-15 rad-6 mb-10 shadow-sm">
        <div>
          Your session for 
          <strong><?= htmlspecialchars($n['course_name']) ?></strong> 
          with tutor 
          <em><?= htmlspecialchars($n['tutor_name']) ?></em> 
          was <strong class="c-green">accepted</strong> on 
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
  </div>
</body>
</html>
