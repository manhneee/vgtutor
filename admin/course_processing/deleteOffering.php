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

if (deleteOffering($conn, $tutorid, $courseid)) {
    header("Location: offerings.php?success=Offering deleted successfully");
} else {
    header("Location: offerings.php?error=Failed to delete offering");
}
exit;