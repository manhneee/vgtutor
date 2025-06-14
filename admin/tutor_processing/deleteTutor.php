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
include "../data/tutor.php";

if (!isset($conn) || !($conn instanceof PDO)) {
    die("Database connection error.");
}

// Sanitize input
$tutorid = intval($_GET['tutorid']);

// Delete tutor from database using PDO
if (deleteTutor($conn, $tutorid)) {
    header("Location: tutor.php?success=Tutor deleted successfully");
} else {
    header("Location: tutor.php?error=Failed to delete tutor");
}
exit;