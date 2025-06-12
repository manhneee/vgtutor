<?php
session_start();
if (isset($_SESSION['adminid']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Admin') {
        include "../../DB_connection.php";
        include "../data/student.php";

        $students = getAllStudents($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin-Students</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="icon" href="../../img/logo.png">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body class="body-home">
    <?php include "../inc/navbar.php"; ?>
    <div class="container mt-5">
        <h2 class="mb-4">Students</h2>
        <?php if ($students && count($students) > 0): ?>
        <div class="table-responsive">
            <table id="table" class="table table-bordered">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Student ID</th>
                        <th scope="col">Email</th>
                        <th scope="col">Name</th>
                        <th scope="col">Major</th>
                        <th scope="col">Intake</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $i => $student): ?>
                    <tr>
                        <th scope="row"><?= $i + 1 ?></th>
                        <td><?= htmlspecialchars($student['accountid']) ?></td>
                        <td><?= htmlspecialchars($student['email']) ?></td>
                        <td><?= htmlspecialchars($student['name']) ?></td>
                        <td><?= htmlspecialchars($student['major']) ?></td>
                        <td><?= htmlspecialchars($student['intake']) ?></td>
                        <td>
                            <a href="editStudent.php?studentid=<?= urlencode($student['accountid']) ?>" class="btn btn-warning btn-sm">Edit</a>
                            <button type="button"
                                    class="btn btn-danger btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#deleteStudentModal"
                                    data-studentid="<?= htmlspecialchars($student['accountid']) ?>">
                                Delete
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <div class="alert alert-info .w-450 m-5" role="alert">
                Empty!
            </div>
        <?php endif; ?>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteStudentModal" tabindex="-1" aria-labelledby="deleteStudentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title w-100 text-center" id="deleteStudentModalLabel">Delete Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger mb-0 text-center">
                      Are you sure you want to delete this student? This will also delete their entry in the tutor table, if it exists, and all of this student's reviews. This action cannot be undone.
                    </div>
                </div>
                <div class="modal-footer">
                    <form id="deleteStudentForm" method="get" action="deleteStudent.php" class="mb-0 w-100 d-flex">
                        <button type="submit" class="btn btn-danger">Delete</button>
                        <input type="hidden" name="studentid" id="modalStudentId" value="">
                        <button type="button" class="btn btn-warning ms-auto" data-bs-dismiss="modal">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function(){
            // Highlight nav if needed
            // $("#navLinks li:contains('Students') a").addClass('active');

            // Set student ID in delete form
            $('#deleteStudentModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget) // Button that triggered the modal
                var studentId = button.data('studentid') // Extract info from data-* attributes
                var modal = $(this)
                modal.find('.modal-footer #modalStudentId').val(studentId);
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
            var deleteModal = document.getElementById('deleteStudentModal');
            if (deleteModal) {
                deleteModal.addEventListener('show.bs.modal', function (event) {
                    var button = event.relatedTarget;
                    var studentId = button.getAttribute('data-studentid');
                    document.getElementById('modalStudentId').value = studentId;
                });
            }
        });
    </script>
</body>
</html>
<?php
    } else {
        $em = "You are not authorized to access this page.";
        header("Location: ../../login.php?error=$em");
        exit;
    }
} else {
    $em = "You are not logged in.";
    header("Location: ../../login.php?error=$em");
    exit;
}
?>