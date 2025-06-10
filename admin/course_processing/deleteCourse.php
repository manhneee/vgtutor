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

$courseid = $_GET['courseid'];

// Delete course from database using PDO
$sql = "DELETE FROM course WHERE courseid = ?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    if ($stmt->execute([$courseid])) {
        header("Location: course.php?success=Course deleted successfully");
    } else {
        header("Location: course.php?error=Failed to delete course");
    }
} else {
    header("Location: course.php?error=Failed to prepare statement");
}
exit;