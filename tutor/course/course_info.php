<?php
session_start();
if (isset($_SESSION['tutorid']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Tutor') {
        include "../../DB_connection.php";

        // Get courseid from URL
        $courseid = isset($_GET['courseid']) ? $_GET['courseid'] : null;
        $cond = null;

        if ($courseid) {
            // Fetch only the 'cond' column for this course
            $stmt = $conn->prepare("SELECT cond FROM course WHERE courseid = ?");
            $stmt->execute([$courseid]);
            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $cond = $row['cond'];
            }
        }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Course Condition</title>
  <link rel="stylesheet" href="../../css/style1.css" />
  <link rel="stylesheet" href="../../css/framework.css" />
  <link rel="stylesheet" href="../../css/master.css" />
  <link rel="icon" href="../../img/logo.png" />
</head>

 <div class="page d-flex">
    <?php include_once '../inc/navbar.php'; ?> <!-- Sidebar -->

    <div class="content w-full">
      <?php include_once '../inc/upbar.php'; ?> <!-- Top Bar -->

<div class="content w-full p-20">
  <!-- Header -->
    <h1 class="c-orange m-0">📋 Course Condition</h1>


  <!-- Condition Display -->
<?php if ($cond !== null): ?>
  <div class="bg-white p-20 rad-10 shadow d-flex flex-column justify-content-between" style="min-height: 300px;">
    <div> 
      <h3 class="c-orange mb-20">🔎 Requirements Overview</h3>

      <?php
        $lines = explode("\n", $cond);
        foreach ($lines as $line):
          if (trim($line) === '') continue;
          $parts = explode(":", $line, 2);
          $title = $parts[0] ?? '';
          $desc = $parts[1] ?? '';
      ?>
        <div class="mb-15">
          <div class="fw-bold fs-15 c-orange mb-5">
            ✅ <?= htmlspecialchars($title) ?>
          </div>
          <div class="fs-14 c-black ms-4"><?= htmlspecialchars(trim($desc)) ?></div>
        </div>
      <?php endforeach; ?>
    </div>

    

  </div>
<?php else: ?>
  <div class="bg-orange rad-10 p-15 mt-15 c-white">
    <i class="fa fa-warning me-2"></i> Course condition not found.
  </div>
<?php endif; ?>
<!-- Offer Button at bottom-right -->
  <div style="display: flex; justify-content: flex-end; width: 100%; margin-top: 1.5rem;">
    <a href="offering.php?courseid=<?= urlencode($courseid) ?>" class="btn-shape bg-orange c-white w-fit" style="margin-left: auto;">
      <i class="fa fa-plus me-1"></i> Offer Course
    </a>
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
