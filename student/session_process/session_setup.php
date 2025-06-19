<?php
session_start();
if (isset($_SESSION['studentid']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Student') {

        include "../../DB_connection.php";
        include "../data/session.php";
        include "../data/courseSelection.php";

        // Get tutorid and courseid from URL
        $tutorid = isset($_GET['tutorid']) ? $_GET['tutorid'] : null;
        $courseid = isset($_GET['courseid']) ? $_GET['courseid'] : null;

        if (!$tutorid || !$courseid) {
            echo "Missing tutor or course information.";
            exit;
        }

        // Fetch tutor and course info
        $tutor_name = getTutorName($conn, $tutorid);
        $course_name = getCourseName($conn, $courseid);

        $error = '';
        $success = '';

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $date = $_POST['date'] ?? '';
            $time = $_POST['time'] ?? '';
            $duration = $_POST['duration'] ?? '';
            $place = $_POST['place'] ?? '';


            // Combine date and time
            $date_and_time = $date . ' ' . $time;

            // Prevent student from booking themselves as tutor
            if ($_SESSION['studentid'] == $tutorid) {
                $error = "You cannot set up a session with yourself as the tutor.";
            } else {
                $inserted = insertSession($conn, $_SESSION['studentid'], $tutorid, $courseid, $date_and_time, $duration, 0, $place);
                if ($inserted) {
                    $success = "Session request sent!";
                } else {
                    $error = "Failed to set up session. Please try again.";
                }
            }
        }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Set Up Session</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="icon" href="../../img/logo.png">
</head>
<body class="body-home">
    <?php include "../inc/navbar.php"; ?>
    <div class="container mt-5" style="max-width:600px;">
        <div class="card shadow">
            <div class="card-body">
                <h2 class="mb-4">Set Up Session</h2>
                <div class="mb-3">
                    <strong>Tutor:</strong> <?= htmlspecialchars($tutor_name) ?><br>
                    <strong>Course:</strong> <?= htmlspecialchars($course_name) ?>
                </div>
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success"><?= $success ?></div>
                <?php endif; ?>
                <form method="post">
                    <div class="mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" name="date" id="date" class="form-control" required min="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="time" class="form-label">Time</label>
                        <input type="time" name="time" id="time" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="duration" class="form-label">Duration (hours)</label>
                        <input type="number" name="duration" id="duration" class="form-control" min="0.5" step="0.5" required>
                    </div>
                    <div class="mb-3">
                        <label for="place" class="form-label">Place</label>
                        <input type="text" name="place" id="place" class="form-control" placeholder="e.g., Online, Library, etc." required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Request Session</button>
                    <a href="../course_process/courseSelection.php" class="btn btn-secondary ms-2">Back</a>
                </form>
            </div>
        </div>
    </div>
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