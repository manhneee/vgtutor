<?php
session_start();
if (!isset($_SESSION['studentid']) || $_SESSION['role'] !== 'Student') {
    header("Location: ../login.php?error=Unauthorized access");
    exit;
}

include "../../DB_connection.php";
include "../data/courseSelection.php";

// Get course ID from URL
$courseid = isset($_GET['courseid']) ? $_GET['courseid'] : null;
if (!$courseid) {
    echo "No course selected.";
    exit;
}

// Fetch course name and tutors
$course_name = getCourseName($conn, $courseid);
$tutors = tutorFetching($conn, $courseid);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Tutors for <?= htmlspecialchars($course_name) ?></title>
    <link rel="stylesheet" href="../../css/style1.css">
    <link rel="stylesheet" href="../../css/framework.css">
    <link rel="stylesheet" href="../../css/master.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;500&display=swap" rel="stylesheet">
</head>

<body class="body-home">
    <div class="page d-flex">
        <?php include_once '../inc/navbar.php'; ?> <!-- Sidebar -->

        <div class="content w-full">
            <?php include_once '../inc/upbar.php'; ?> <!-- upbar -->

            <h1 class="c-orange mt-20 mb-20 ml-20">Available Tutors</h1>

            <div class="courses-page d-grid m-20 gap-20">
                <?php if (!empty($tutors)): ?>
                    <?php foreach ($tutors as $tutor): ?>
                        <div class="course bg-white rad-6 p-relative shadow p-20">
                            <!-- Avatar -->
                            <div class="center-flex mb-15">
                            </div>

                            <!-- Tutor Info -->
                            <h4 class="c-orange fs-15 m-0"><?= htmlspecialchars($tutor['tutor_name']) ?></h4>
                            <p class="c-grey fs-14 mt-10">📘 Major: <?= htmlspecialchars($tutor['major']) ?></p>
                            <p class="c-grey fs-14">🎓 GPA: <?= htmlspecialchars($tutor['gpa']) ?></p>
                            <p class="fs-14">🗒️ <?= htmlspecialchars($tutor['description']) ?></p>
                            <p class="fs-14">✉️ <a href="mailto:<?= htmlspecialchars($tutor['email']) ?>" class="c-blue"><?= htmlspecialchars($tutor['email']) ?></a></p>
                            <p class="fs-14 mt-10">⭐ Rating: <span class="c-orange fw-bold"><?= htmlspecialchars($tutor['rating']) ?>/5</span></p>
                            <p class="fs-14 mt-10">⭐ Price: <span class="c-orange fw-bold"><?= htmlspecialchars($tutor['price']) ?>/hour</span></p>
                            <!-- View Reviews Button -->
                            <div class="d-flex gap-2 mt-10">
                                <a href="tutorReviews.php?tutorid=<?= urlencode($tutor['tutorid']) ?>" class="btn-shape bg-orange c-white" style="margin-right: 10px;">View Reviews</a>
                                <a href="../session_process/session_setup.php?tutorid=<?= urlencode($tutor['tutorid']) ?>&courseid=<?= urlencode($courseid) ?>" class="btn-shape bg-orange c-white w-fit">Register Session</a>
                            </div>

                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="c-grey fs-14">Sorry, There is no current tutors available for this course.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>