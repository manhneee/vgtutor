<?php
session_start();
if (isset($_SESSION['adminid']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Admin') {

        include "../../DB_connection.php";
        include "../data/review.php";

        // Fetch all reviews
        $reviews = getAllReviews($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Reviews</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="icon" href="../../img/logo.png">
</head>
<body class="body-home">
    <?php include "../inc/navbar.php"; ?>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="mb-0">All Reviews</h2>
        </div>
        <?php if ($reviews && count($reviews) > 0): ?>
            <div class="table-responsive">
                <table id="table" class="table table-bordered">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Student</th>
                            <th scope="col">Tutor</th>
                            <th scope="col">Course</th>
                            <th scope="col">Rating</th>
                            <th scope="col">Review</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reviews as $i => $row): ?>
                        <tr>
                            <th scope="row"><?= $i + 1 ?></th>
                            <td><?= htmlspecialchars($row['student_name']) ?></td>
                            <td><?= htmlspecialchars($row['tutor_name']) ?></td>
                            <td><?= htmlspecialchars($row['course_name']) ?></td>
                            <td><?= htmlspecialchars($row['rating']) ?></td>
                            <td><?= nl2br(htmlspecialchars($row['review'])) ?></td>
                            <td>
                                <a href="editReview.php?studentid=<?= urlencode($row['studentid']) ?>&tutorid=<?= urlencode($row['tutorid']) ?>&courseid=<?= urlencode($row['courseid']) ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="deleteReview.php?studentid=<?= urlencode($row['studentid']) ?>&tutorid=<?= urlencode($row['tutorid']) ?>&courseid=<?= urlencode($row['courseid']) ?>" class="btn btn-danger btn-sm"
                                   onclick="return confirm('Are you sure you want to delete this review?');">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info w-50 m-5 mx-auto text-center" role="alert">
                No reviews found!
            </div>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function(){
            // Highlight nav if needed
            // $("#navLinks li:contains('Reviews') a").addClass('active');
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