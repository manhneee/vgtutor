<?php
session_start();
if (isset($_SESSION['tutorid']) && $_SESSION['role'] === 'Tutor') {
    include "../../DB_connection.php";
    include "../data/course_offering.php";

    $tutorid = $_SESSION['tutorid'];
    $my_offerings = getTutorPendingOfferings($conn, $tutorid);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Your Offered course </title>
  <link rel="stylesheet" href="../../css/course_offered.css" />
  <link rel="stylesheet" href="../../css/style1.css" />
  <link rel="stylesheet" href="../../css/framework.css" />
  <link rel="stylesheet" href="../../css/master.css" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="icon" href="../../img/logo.png">
</head>
<body>
  <div class="page d-flex">
    <?php include_once '../inc/navbar.php'; ?> <!-- Sidebar -->

    <div class="content w-full">
      <?php include_once '../inc/upbar.php'; ?> <!-- Top Bar -->

      <div class="section">
        <div class="card">
          <h3><i class="fa fa-chalkboard-teacher"></i> Your Offered Courses</h3>
          <table class="modern-table">
            <thead>
              <tr>
                <th>#</th>
                <th>Course Name</th>
                <th>Major</th>
                <th>Grade</th>
                <th>Price</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php if (count($my_offerings) > 0): ?>
                <?php foreach ($my_offerings as $i => $row): ?>
                  <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($row['course_name']) ?></td>
                    <td><?= htmlspecialchars($row['major']) ?></td>
                    <td><?= htmlspecialchars($row['grade']) ?></td>
                    <td>$<?= htmlspecialchars($row['price']) ?></td>
                    <td>
                      <?php
                        $status = strtolower($row['status']);
                        if ($status === 'permitted') {
                          echo '<span class="status-badge status-permitted">Permitted</span>';
                        } elseif ($status === 'denied' || $status === 'deny') {
                          echo '<span class="status-badge status-denied">Denied</span>';
                        } else {
                          echo '<span class="status-badge status-pending">Pending</span>';
                        }
                      ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="6" class="text-center">You have not offered any courses yet.</td></tr>
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
    $em = "Unauthorized access.";
    header("Location: ../login.php?error=$em");
    exit;
}
?>
