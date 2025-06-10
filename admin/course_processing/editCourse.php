<?php
session_start();
if (!isset($_SESSION['adminid']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../../login.php?error=Unauthorized access");
    exit;
}

if (!isset($_GET['courseid'])) {
    header("Location: course.php?error=No course ID specified");
    exit;
}

include "../../DB_connection.php";

// Fetch course data
$courseid = $_GET['courseid'];
$stmt = $conn->prepare("SELECT * FROM course WHERE courseid = ?");
$stmt->execute([$courseid]);
$course = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$course) {
    header("Location: course.php?error=Course not found");
    exit;
}

// Handle form submission
$success = $error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_name = $_POST['course_name'] ?? '';
    $major = $_POST['major'] ?? '';
    $semester = $_POST['semester'] ?? '';
    $cond = $_POST['cond'] ?? '';

    $update = $conn->prepare("UPDATE course SET course_name = ?, major = ?, semester = ?, cond = ? WHERE courseid = ?");
    if ($update->execute([$course_name, $major, $semester, $cond, $courseid])) {
        $success = "Course updated successfully!";
        // Refresh course data
        $stmt = $conn->prepare("SELECT * FROM course WHERE courseid = ?");
        $stmt->execute([$courseid]);
        $course = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $error = "Failed to update course.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Course</title>
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
                        <h3 class="mb-0">Edit Course</h3>
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
                                <input type="text" class="form-control" value="<?= htmlspecialchars($course['courseid']) ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Course Name</label>
                                <input type="text" name="course_name" class="form-control" value="<?= htmlspecialchars($_POST['course_name'] ?? $course['course_name'] ?? '') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Major</label>
                                <input type="text" name="major" class="form-control" value="<?= htmlspecialchars($_POST['major'] ?? $course['major'] ?? '') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Semester</label>
                                <input type="text" name="semester" class="form-control" value="<?= htmlspecialchars($_POST['semester'] ?? $course['semester'] ?? '') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Conditions</label>
                                <textarea name="cond" class="form-control" rows="4" required><?= htmlspecialchars($_POST['cond'] ?? $course['cond'] ?? '') ?></textarea>
                            </div>
                            <div class="d-flex justify-content-center">
                                <button type="submit" class="btn btn-primary" style="background-color: #f47119; border-color: #f47119;">Update Course</button>
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