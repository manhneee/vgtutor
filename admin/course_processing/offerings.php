<?php
session_start();
if (isset($_SESSION['adminid']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Admin') {
    include "../../DB_connection.php";
    include "../data/offerings.php";

    $offerings = getAllOfferings($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offerings</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="icon" href="../../img/logo.png">
</head>
<body class="body-home">
    <?php include "../inc/navbar.php"; ?>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="mb-0">Offerings</h2>
        </div>
        <?php if ($offerings && count($offerings) > 0): ?>
            <div class="table-responsive">
                <table id="table" class="table table-bordered">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Tutor ID</th>
                            <th scope="col">Tutor Name</th>
                            <th scope="col">Course ID</th>
                            <th scope="col">Course Name</th>
                            <th scope="col">Tutor Grade</th>
                            <th scope="col">Rating</th>
                            <th scope="col">Price</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($offerings as $i => $row): ?>
                        <tr>
                            <th scope="row"><?= $i + 1 ?></th>
                            <td><?= htmlspecialchars($row['tutorid'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['tutor_name'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['courseid'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['course_name'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['tutor_grade'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['rating'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['price'] ?? '') ?></td>
                            <td>
                                <a href="editOffering.php?tutorid=<?= urlencode($row['tutorid'] ?? '') ?>&courseid=<?= urlencode($row['courseid'] ?? '') ?>" class="btn btn-warning btn-sm">Edit</a>
                                <button type="button"
                                        class="btn btn-danger btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteOfferingModal"
                                        data-tutorid="<?= htmlspecialchars($row['tutorid'] ?? '') ?>"
                                        data-courseid="<?= htmlspecialchars($row['courseid'] ?? '') ?>">
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
    <div class="modal fade" id="deleteOfferingModal" tabindex="-1" aria-labelledby="deleteOfferingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title w-100 text-center" id="deleteOfferingModalLabel">Delete Offering</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger mb-0 text-center">
                      <strong>Warning:</strong> Are you sure you want to delete this offering? This will also delete all reviews for this offering. This action cannot be undone.
                    </div>
                </div>
                <div class="modal-footer">
                    <form id="deleteOfferingForm" method="get" action="deleteOffering.php" class="mb-0 w-100 d-flex">
                        <button type="submit" class="btn btn-danger">Delete</button>
                        <input type="hidden" name="tutorid" id="modalTutorId" value="">
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
            var deleteModal = document.getElementById('deleteOfferingModal');
            deleteModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var tutorId = button.getAttribute('data-tutorid');
                var courseId = button.getAttribute('data-courseid');
                document.getElementById('modalTutorId').value = tutorId;
                document.getElementById('modalCourseId').value = courseId;
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