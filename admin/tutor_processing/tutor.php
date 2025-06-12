<?php
session_start();
if (isset($_SESSION['adminid']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Admin') {
        include "../../DB_connection.php";
        include "../data/tutor.php";

        $tutors = getAllTutors($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin-Tutors</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="icon" href="../../img/logo.png">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body class="body-home"> 
    <?php include "../inc/navbar.php"; ?>
    <div class="container mt-5">
        <h2 class="mb-4">Tutors</h2>
        <?php if ($tutors && count($tutors) > 0): ?>
        <div class="table-responsive">
            <table id="table" class="table table-bordered">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Tutor ID</th>
                        <th scope="col">Email</th>
                        <th scope="col">Name</th>
                        <th scope="col">GPA</th>
                        <th scope="col">Description</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tutors as $i => $tutor): ?>
                    <tr>
                        <th scope="row"><?= $i + 1 ?></th>
                        <td><?= htmlspecialchars($tutor['accountid']) ?></td>
                        <td><?= htmlspecialchars($tutor['email']) ?></td>
                        <td><?= htmlspecialchars($tutor['name']) ?></td>
                        <td><?= htmlspecialchars($tutor['gpa']) ?></td>
                        <td><?= htmlspecialchars($tutor['description']) ?></td>
                        <td>
                            <a href="editTutor.php?tutorid=<?= urlencode($tutor['accountid']) ?>" class="btn btn-warning btn-sm">Edit</a>
                            <button type="button"
                                    class="btn btn-danger btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#deleteTutorModal"
                                    data-tutorid="<?= htmlspecialchars($tutor['accountid']) ?>">
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
                Empty!
            </div>
        <?php endif; ?>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteTutorModal" tabindex="-1" aria-labelledby="deleteTutorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title w-100 text-center" id="deleteTutorModalLabel">Delete Tutor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger mb-0 text-center">
                      <strong>Warning:</strong> Are you sure you want to delete this tutor? This will delete all of their offerings and reviews. This action cannot be undone.
                    </div>
                </div>
                <div class="modal-footer">
                    <form id="deleteTutorForm" method="get" action="deleteTutor.php" class="mb-0 w-100 d-flex">
                        <button type="submit" class="btn btn-danger">Delete</button>
                        <input type="hidden" name="tutorid" id="modalTutorId" value="">
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
            // $("#navLinks li:contains('Tutors') a").addClass('active');

            // Set tutor ID in delete form
            $('#deleteTutorModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget) // Button that triggered the modal
                var tutorid = button.data('tutorid') // Extract info from data-* attributes
                var modal = $(this)
                modal.find('#modalTutorId').val(tutorid);
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
            var deleteModal = document.getElementById('deleteTutorModal');
            if (deleteModal) {
                deleteModal.addEventListener('show.bs.modal', function (event) {
                    var button = event.relatedTarget;
                    var tutorId = button.getAttribute('data-tutorid');
                    document.getElementById('modalTutorId').value = tutorId;
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