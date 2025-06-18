<?php
session_start();
if (!isset($_SESSION['adminid']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../../login.php?error=Unauthorized access");
    exit;
}

if (!isset($_GET['tutorid'])) {
    header("Location: tutor.php?error=No tutor ID specified");
    exit;
}

include "../../DB_connection.php";

if (!isset($conn) || !($conn instanceof PDO)) {
    die("Database connection error.");
}

// Sanitize input
$tutorid = intval($_GET['tutorid']);

// Delete tutor from database using PDO
$sql = "DELETE FROM tutor_account WHERE accountid = ?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    if ($stmt->execute([$tutorid])) {
        header("Location: tutor.php?success=Tutor deleted successfully");
    } else {
        header("Location: tutor.php?error=Failed to delete tutor");
    }
} else {
    header("Location: tutor.php?error=Failed to prepare statement");
}
exit;