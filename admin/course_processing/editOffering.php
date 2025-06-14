<?php
session_start();
if (!isset($_SESSION['adminid']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../../login.php?error=Unauthorized access");
    exit;
}

if (!isset($_GET['tutorid']) || !isset($_GET['courseid'])) {
    header("Location: offerings.php?error=Missing tutor or course ID");
    exit;
}

include "../../DB_connection.php";
include "../data/offerings.php";

$tutorid = $_GET['tutorid'];
$courseid = $_GET['courseid'];

$offering = getOffering($conn, $tutorid, $courseid);
if (!$offering) {
    header("Location: offerings.php?error=Offering not found");
    exit;
}

// Handle form submission
$success = $error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tutor_grade = $_POST['tutor_grade'] ?? '';
    $rating = $_POST['rating'] ?? '';
    $price = $_POST['price'] ?? '';

    if (updateOffering($conn, $tutorid, $courseid, $tutor_grade, $rating, $price)) {
        $success = "Offering updated successfully!";
        $offering = getOffering($conn, $tutorid, $courseid);
    } else {
        $error = "Failed to update Offering.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Offering</title>
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
                        <h3 class="mb-0">Edit Offering</h3>
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
                                <label class="form-label">Tutor Name</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($offering['tutor_name']) ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Course Name</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($offering['course_name']) ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tutor Grade</label>
                                <input type="text" name="tutor_grade" class="form-control" value="<?= htmlspecialchars($_POST['tutor_grade'] ?? $offering['tutor_grade'] ?? '') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Rating</label>
                                <input type="text" name="rating" class="form-control" value="<?= htmlspecialchars($_POST['rating'] ?? $offering['rating'] ?? '') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Price</label>
                                <input type="text" name="price" class="form-control" value="<?= htmlspecialchars($_POST['price'] ?? $offering['price'] ?? '') ?>" required>
                            </div>
                            <div class="d-flex justify-content-center">
                                <button type="submit" class="btn btn-primary" style="background-color: #f47119; border-color: #f47119;">Update Offering</button>
                                <a href="offerings.php" class="btn btn-secondary ms-2">Cancel</a>
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