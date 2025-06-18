<?php
session_start();
if (isset($_SESSION['tutorid']) && $_SESSION['role'] === 'Tutor') {
    include "../../DB_connection.php";
    include "../data/course.php";

    $courses = getAllCourse($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Tutor - Courses</title>
  
  <link rel="stylesheet" href="../../css/course.css" />
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

      <h1 class="p-relative c-orange">Courses</h1>

<!-- Course Cards -->
<div class="projects-page">
  <?php if (count($courses) > 0): ?>
    <?php foreach ($courses as $course): ?>
      <div class="course-card" onclick="window.location='course_info.php?courseid=<?= urlencode($course['course_courseid']) ?>'">
        <div class="card-header">
          <span class="badge">Course ID: <?= htmlspecialchars($course['course_courseid']) ?></span>
        </div>
        <div class="card-body">
          <h3 class="course-title"><?= htmlspecialchars($course['course_name']) ?></h3>
          <p class="course-major">Major: <strong><?= htmlspecialchars($course['major']) ?></strong></p>
        </div>
        <div class="card-tags">
          <span class="tag">Computer Science</span>
          <span class="tag">Third Semester</span>
        </div>
        <div class="card-footer">
          <div class="progress-bar">
            <span class="progress-fill"></span>
          </div>
          <span class="status">Available</span>
        </div>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <div class="course-card">
      <p class="no-courses">No courses found.</p>
    </div>
  <?php endif; ?>
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
