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
                            <td><?= htmlspecialchars($row['tutorid']) ?></td>
                            <td><?= htmlspecialchars($row['tutor_name']) ?></td>
                            <td><?= htmlspecialchars($row['courseid']) ?></td>
                            <td><?= htmlspecialchars($row['course_name']) ?></td>
                            <td><?= htmlspecialchars($row['tutor_grade']) ?></td>
                            <td><?= htmlspecialchars($row['rating']) ?></td>
                            <td><?= htmlspecialchars($row['price']) ?></td>
                            <td>
                                <a href="editOffering.php?tutorid=<?= urlencode($row['tutorid']) ?>&courseid=<?= urlencode($row['courseid']) ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="deleteOffering.php?tutorid=<?= urlencode($row['tutorid']) ?>&courseid=<?= urlencode($row['courseid']) ?>" class="btn btn-danger btn-sm"
                                   onclick="return confirm('Are you sure you want to delete this offering?');">Delete</a>
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