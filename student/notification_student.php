<?php
session_start();
if (!isset($_SESSION['studentid']) || $_SESSION['role'] !== 'Student') {
  header("Location: ../login.php?error=Unauthorized access");
  exit;
}

include_once "../DB_connection.php";
include_once "data/notifications.php";

$studentid = $_SESSION['studentid'];
$allNotifications = getAllNotifications($conn, $studentid, 100);

// Optionally mark as read (is_read = 1)
foreach ($allNotifications as $noti) {
  if (isset($noti['is_read']) && !$noti['is_read']) {
    $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE id = ?");
    $stmt->execute([$noti['id']]);
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
                  <span class="c-grey fs-13"><?= htmlspecialchars($n['created_at']) ?></span>
                  <div class="fw-bold mb-6"><?= htmlspecialchars($n['title']) ?></div>
                  <div><?= htmlspecialchars($n['message']) ?></div>
                  <?php if (!empty($n['sender_name'])): ?>
                    <div class="fs-13 c-grey mt-5">From: <?= htmlspecialchars($n['sender_name']) ?></div>
                  <?php endif; ?>
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