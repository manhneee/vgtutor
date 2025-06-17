<?php
session_start();
if (isset($_SESSION['tutorid']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Tutor') {

        // Include database connection and course data functions
        include "../../DB_connection.php";
        include "../data/course.php";
        include "../data/course_offering.php";

        // Fetch courses
        $courses = getAllCourse($conn);
        $tutorid = $_SESSION['tutorid'];
        $my_offerings = getTutorPendingOfferings($conn, $tutorid);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tutor - Courses</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="icon" href="../../img/logo.png">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body class="body-home">
    <?php include "../inc/navbar.php"; ?>

    <div class="container mt-5">
        <h2 class="mb-4">Courses</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Course ID</th>
                        <th>Course Name</th>
                        <th>Major</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (count($courses) > 0): ?>
                    <?php foreach ($courses as $i => $course): ?>
                        <tr style="cursor:pointer;" onclick="window.location='course_info.php?courseid=<?= urlencode($course['course_courseid']) ?>'">
                            <td><?= $i + 1 ?></td>
                            <td><?= htmlspecialchars($course['course_courseid'] ?? '') ?></td>
                            <td><?= htmlspecialchars($course['course_name'] ?? '') ?></td>
                            <td><?= htmlspecialchars($course['major'] ?? '') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center">No courses found.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="container mt-5">
        <h2 class="mb-4">Your Offered Course</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Course Name</th>
                        <th>Major</th>
                        <th>Grade</th>
                        <th>Price</th>
                        <th>Self Introduction</th>
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
                            <td><?= htmlspecialchars($row['price']) ?></td>
                            <td><?= nl2br(htmlspecialchars($row['self_description'])) ?></td>
                            <td>
                                <?php
                                if ($row['status'] === 'permitted') {
                                    echo '<span class="badge bg-success">Permitted</span>';
                                } elseif ($row['status'] === 'deny' || $row['status'] === 'denied') {
                                    echo '<span class="badge bg-danger">Denied</span>';
                                } else {
                                    echo '<span class="badge bg-warning text-dark">Pending</span>';
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">You have not offered any courses yet.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function(){
            $("#navLinks li:nth-child(1) a").addClass('active');
        });
    </script>
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