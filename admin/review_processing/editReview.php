<?php
session_start();
if (!isset($_SESSION['adminid']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php?error=Unauthorized access");
    exit;
}

if (!isset($_GET['studentid']) || !isset($_GET['tutorid']) || !isset($_GET['courseid'])) {
    header("Location: review.php?error=Missing review keys");
    exit;
}

include "../../DB_connection.php";
include "../data/review.php";

$studentid = $_GET['studentid'];
$tutorid = $_GET['tutorid'];
$courseid = $_GET['courseid'];

$review = getReview($conn, $studentid, $tutorid, $courseid);

if (!$review) {
    header("Location: review.php?error=Review not found");
    exit;
}

$success = $error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = $_POST['rating'] ?? '';
    $reviewText = $_POST['review'] ?? '';

    if (updateReview($conn, $studentid, $tutorid, $courseid, $rating, $reviewText)) {
        $success = "Review updated successfully!";
        $review = getReview($conn, $studentid, $tutorid, $courseid);
    } else {
        $error = "Failed to update review.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Review</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="icon" href="../../img/logo.png">
</head>
<body class="body-home">
    <?php include "../inc/navbar.php"; ?>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="card shadow-lg" style="border: 2px solid #000;">
                    <div class="card-header text-white text-center" style="background-color: #f47119;">
                        <h3 class="mb-0">Edit Review</h3>
                    </div>
                    <?php if ($success): ?>
                        <div class="alert alert-success" role="alert">
                            <?= $success ?>
                        </div>
                    <?php elseif ($error): ?>
                        <div class="alert alert-danger" role="alert">
                            <?= $error ?>
                        </div>
                    <?php endif; ?>
                    <div class="card-body">
                        <form method="post">
                            <div class="mb-3">
                                <label class="form-label">Student</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($review['student_name']) ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tutor</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($review['tutor_name']) ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Course</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($review['course_name']) ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Rating</label>
                                <input type="number" name="rating" class="form-control" min="1" max="5" value="<?= htmlspecialchars($_POST['rating'] ?? $review['rating'] ?? '') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Review</label>
                                <textarea name="review" class="form-control" rows="6" maxlength="1000" required><?= htmlspecialchars($_POST['review'] ?? $review['review'] ?? '') ?></textarea>
                            </div>
                            <div class="d-flex justify-content-center">
                                <button type="submit" class="btn btn-primary" style="background-color: #f47119; border-color: #f47119;">Update Review</button>
                                <a href="review.php" class="btn btn-secondary ms-2">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>