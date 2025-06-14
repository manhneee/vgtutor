<?php
session_start();
if (!isset($_SESSION['adminid']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../../login.php?error=Unauthorized access");
    exit;
}

include "../../DB_connection.php";
include "../data/course.php";

$success = $error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $courseid = $_POST['courseid'] ?? '';
    $course_name = $_POST['course_name'] ?? '';
    $major = $_POST['major'] ?? '';
    $semester = $_POST['semester'] ?? '';
    $cond = $_POST['cond'] ?? '';

    if ($courseid && $course_name && $major && $semester && $cond) {
        try {
            if (addCourse($conn, $courseid, $course_name, $major, $semester, $cond)) {
                $success = "Course added successfully!";
                // Clear form fields after success
                $_POST = [];
            } else {
                $error = "Failed to add course.";
            }
        } catch (PDOException $e) {
            $error = "SQL Error: " . $e->getMessage();
        }
    } else {
        $error = "All fields are required.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Course</title>
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
                        <h3 class="mb-0">Add New Course</h3>
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
                                <label class="form-label">Course ID</label>
                                <input type="text" name="courseid" class="form-control" value="<?= htmlspecialchars($_POST['courseid'] ?? '') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Course Name</label>
                                <input type="text" name="course_name" class="form-control" value="<?= htmlspecialchars($_POST['course_name'] ?? '') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Major</label>
                                <input type="text" name="major" class="form-control" value="<?= htmlspecialchars($_POST['major'] ?? '') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Semester</label>
                                <input type="text" name="semester" class="form-control" value="<?= htmlspecialchars($_POST['semester'] ?? '') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Conditions</label>
                                <textarea name="cond" class="form-control" rows="4" required><?= htmlspecialchars($_POST['cond'] ?? '') ?></textarea>
                            </div>
                            <div class="d-flex justify-content-center">
                                <button type="submit" class="btn btn-primary" style="background-color: #f47119; border-color: #f47119;">Add Course</button>
                                <a href="course.php" class="btn btn-secondary ms-2">Cancel</a>
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