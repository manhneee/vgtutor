<?php
session_start();
if (!isset($_SESSION['studentid']) || $_SESSION['role'] !== 'Student') {
    header("Location: ../login.php?error=Unauthorized access");
    exit;
}

include "../../DB_connection.php";
include "../data/courseSelection.php";

// Get courseid from URL
$courseid = isset($_GET['courseid']) ? $_GET['courseid'] : null;
if (!$courseid) {
    echo "No course selected.";
    exit;
}

// Fetch course name
$course_name = getCourseName($conn, $courseid); 

// Fetch tutors teaching this course
$tutors = tutorFetching($conn, $courseid);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student - Courses</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="icon" href="../../img/logo.png">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body class="body-home">
    <?php include "navbar.php"; ?>
    <div class="container mt-5">
        <h2 class="mb-4">Tutors for Course ID: <?= htmlspecialchars($course_name) ?></h2>
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Tutor Name</th>
                        <th>Major</th>
                        <th>GPA</th>
                        <th>Description</th>
                        <th>Email</th>
                        <th>Overall Rating</th>
                        <th>Reviews</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (count($tutors) > 0): ?>
                    <?php foreach ($tutors as $i => $tutor): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= htmlspecialchars($tutor['tutor_name']) ?></td>
                            <td><?= htmlspecialchars($tutor['major']) ?></td>
                            <td><?= htmlspecialchars($tutor['gpa']) ?></td>
                            <td><?= htmlspecialchars($tutor['description']) ?></td>
                            <td><?= htmlspecialchars($tutor['email']) ?></td>
                            <td><?= htmlspecialchars($tutor['rating']) ?></td>
                            <td>
                                <a href="tutorReviews.php?tutorid=<?= urlencode($tutor['tutorid']) ?>" class="btn btn-primary">
                                    View Reviews
                                </a>
                            </td>
                            
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">No tutors found for this course.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
</body>
</html>