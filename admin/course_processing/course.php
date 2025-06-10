<?php
session_start();
if (isset($_SESSION['adminid']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Admin') {
    include "../../DB_connection.php";

    // Fetch all courses
    $stmt = $conn->prepare("SELECT * FROM course");
    $stmt->execute();
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courses</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="icon" href="../../img/logo.png">
</head>
<body class="body-home">
    <?php include "../inc/navbar.php"; ?>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="mb-0">Courses</h2>
            <a href="addCourse.php" class="btn bg-orange text-white">
                <i class="fa fa-plus"></i> Add New Course
            </a>
        </div>
        <?php if ($courses && count($courses) > 0): ?>
            <div class="table-responsive">
                <table id="table" class="table table-bordered">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Course ID</th>
                            <th scope="col">Course Name</th>
                            <th scope="col">Major</th>
                            <th scope="col">Semester</th>
                            <th scope="col" style="min-width:200px;">Conditions</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($courses as $i => $row): ?>
                        <tr>
                            <th scope="row"><?= $i + 1 ?></th>
                            <td><?= htmlspecialchars($row['courseid']) ?></td>
                            <td><?= htmlspecialchars($row['course_name']) ?></td>
                            <td><?= htmlspecialchars($row['major']) ?></td>
                            <td><?= htmlspecialchars($row['semester']) ?></td>
                            <td>
                                <div style="max-height:100px; overflow-y:auto;"><?= nl2br(htmlspecialchars($row['cond'])) ?></div>
                            </td>
                            <td>
                                <a href="editCourse.php?courseid=<?= urlencode($row['courseid']) ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="deleteCourse.php?courseid=<?= urlencode($row['courseid']) ?>" class="btn btn-danger btn-sm"
                                   onclick="return confirm('Are you sure you want to delete this course?');">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info w-50 m-5 mx-auto text-center" role="alert">
                No courses found!
            </div>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function(){
            // Highlight nav if needed, e.g.:
            // $("#navLinks li:contains('Courses') a").addClass('active');
        });
    </script>
</body>
</html>
<?php
} else {
    header("Location: ../login.php?error=Unauthorized access");
    exit;
}
?>