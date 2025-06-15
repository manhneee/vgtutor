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

if (deleteReview($conn, $studentid, $tutorid, $courseid)) {
    header("Location: review.php?success=Review deleted successfully");
} else {
    header("Location: review.php?error=Failed to delete review");
}
exit;