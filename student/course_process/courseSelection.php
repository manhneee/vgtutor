<?php
session_start();
if (isset($_SESSION['studentid']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Student') {
        include "../../DB_connection.php";
        include "../data/courseSelection.php";

    $courses = searchCourse($conn, '', '', '');;

        // Handle search
        $search_name = isset($_GET['search_name']) ? trim($_GET['search_name']) : '';
        $search_major = isset($_GET['search_major']) ? trim($_GET['search_major']) : '';
        $search_semester = isset($_GET['search_semester']) ? trim($_GET['search_semester']) : '';
        $courses = searchCourse($conn, $search_name, $search_major, $search_semester);
        
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Courses</title>
    <link rel="stylesheet" href="../../css/style1.css" />
    <link rel="stylesheet" href="../../css/framework.css" />
    <link rel="stylesheet" href="../../css/master.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;500&display=swap" rel="stylesheet" />
</head>
<body class="body-home">
    <div class="page d-flex">
        <?php include_once '../inc/navbar.php'; ?> <!-- LEFT SIDEBAR -->

        <div class="content w-full">
      <?php include_once '../inc/upbar.php'; ?> 
                        <!-- Search bar -->

             <h1 class="c-orange ">Courses</h1>
<form method="GET" class="d-flex gap-20 m-20">
  <input type="text" name="search_name" placeholder="Course name" class="p-10 rad-6 fs-14" value="<?= htmlspecialchars($_GET['search_name'] ?? '') ?>">
  <input type="text" name="search_major" placeholder="Major" class="p-10 rad-6 fs-14" value="<?= htmlspecialchars($_GET['search_major'] ?? '') ?>">
  <input type="number" name="search_semester" placeholder="Semester" class="p-10 rad-6 fs-14" value="<?= htmlspecialchars($_GET['search_semester'] ?? '') ?>">
  <button type="submit" class="bg-orange c-white btn-shape fs-14">Search</button>
</form>
                        <!-- end Search bar -->

<?php if (!empty($courses)): ?>
  <?php
    $course_images = [
      'course-01.jpg',
      'course-02.jpg',
      'course-03.jpg',
      'course-04.jpg',
      'course-05.jpg'
    ];
  ?>
  <div class="courses-page d-grid m-20 gap-20">
    <?php foreach ($courses as $course): ?>
      <?php $randomImage = $course_images[array_rand($course_images)]; ?>
      <div class="course bg-white rad-6 p-relative" style="cursor: pointer;" onclick="window.location.href='tutorSelection.php?courseid=<?= urlencode($course['courseid']) ?>'">
        <!-- Randomized Course Image -->
        <img class="cover" src="../../img/<?= $randomImage ?>" alt="Course Cover" />

        <!-- Course Info -->
        <div class="p-20">
          <h4 class="m-0 c-orange"><?= htmlspecialchars($course['course_name']) ?></h4>
          <p class="description c-grey mt-15 fs-14">
            <?= htmlspecialchars($course['cond'] ?? 'No description available.') ?>
          </p>
        </div>

        <div class="info p-15 p-relative between-flex">
          <span class="title bg-orange c-white btn-shape">Course Info</span>
          <span class="c-grey fs-13">Major: <?= htmlspecialchars($course['major']) ?></span>
          <span class="c-grey fs-13">Semester: <?= htmlspecialchars($course['semester']) ?></span>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php else: ?>
  <p class="c-grey">No courses available.</p>
<?php endif; ?>




</body>

</html>
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