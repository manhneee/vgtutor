<?php
session_start();
if (isset($_SESSION['studentid']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Student') {
        include "../../DB_connection.php";
        include "../data/courseSelection.php";

        // Handle search
        $search_name = isset($_GET['search_name']) ? trim($_GET['search_name']) : '';
        $search_major = isset($_GET['search_major']) ? trim($_GET['search_major']) : '';
        $search_semester = isset($_GET['search_semester']) ? trim($_GET['search_semester']) : '';
        $courses = searchCourse($conn, $search_name, $search_major, $search_semester);
        
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
    <?php include "../inc/navbar.php"; ?>

    <div class="container mt-5">
        <h2 class="mb-4">Available Courses</h2>
        <!-- Search Bar -->
        <form class="row g-3 mb-4" method="get" action="">
            <div class="col-md-4">
                <input type="text" name="search_name" class="form-control" placeholder="Search by course name" value="<?= htmlspecialchars($search_name) ?>">
            </div>
            <div class="col-md-3">
                <input type="text" name="search_major" class="form-control" placeholder="Search by major" value="<?= htmlspecialchars($search_major) ?>">
            </div>
            <div class="col-md-3">
                <input type="number" name="search_semester" class="form-control" placeholder="Search by semester" value="<?= htmlspecialchars($search_semester) ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Search</button>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Course ID</th>
                        <th>Course Name</th>
                        <th>Major</th>
                        <th>Semester</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (count($courses) > 0): ?>
                    <?php foreach ($courses as $i => $course): ?>
                        <tr style="cursor:pointer;" onclick="window.location='tutorSelection.php?courseid=<?= urlencode($course['courseid']) ?>'">
                            <td><?= $i + 1 ?></td>
                            <td><?= htmlspecialchars($course['courseid'] ?? '') ?></td>
                            <td><?= htmlspecialchars($course['course_name'] ?? '') ?></td>
                            <td><?= htmlspecialchars($course['major'] ?? '') ?></td>
                            <td><?= htmlspecialchars($course['semester'] ?? '') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">No courses found.</td>
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