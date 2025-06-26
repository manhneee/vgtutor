<?php
session_start();
if (isset($_SESSION['adminid']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Admin') {
    include "../../DB_connection.php";
    include "../data/course.php";

    $courses = getAllCourses($conn);
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
                                <button type="button"
                                        class="btn btn-danger btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteModal"
                                        data-courseid="<?= htmlspecialchars($row['courseid']) ?>">
                                    Delete
                                </button>
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

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title w-100 text-center" id="deleteModalLabel">Delete Course</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger mb-0 text-center">
                      <strong>Warning:</strong> Are you sure you want to delete this course? This will delete all of this course's offerings and their reviews. This action cannot be undone.
                    </div>
                </div>
                <div class="modal-footer">
                    <form id="deleteForm" method="get" action="deleteCourse.php" class="mb-0 w-100 d-flex">
                        <button type="submit" class="btn btn-danger">Delete</button>
                        <input type="hidden" name="courseid" id="modalCourseId" value="">
                        <button type="button" class="btn btn-warning ms-auto" data-bs-dismiss="modal">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var deleteModal = document.getElementById('deleteModal');
            deleteModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var courseId = button.getAttribute('data-courseid');
                var input = document.getElementById('modalCourseId');
                input.value = courseId;
            });
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