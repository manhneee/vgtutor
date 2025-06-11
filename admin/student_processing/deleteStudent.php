<?php
session_start();
if (!isset($_SESSION['adminid']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../../login.php?error=Unauthorized access");
    exit;
}

if (!isset($_GET['studentid'])) {
    header("Location: student.php?error=No student ID specified");
    exit;
}

include "../../DB_connection.php";
include "../data/student.php";

$studentid = intval($_GET['studentid']);

if (deleteStudent($conn, $studentid)) {
    header("Location: student.php?success=Student deleted successfully");
} else {
    header("Location: student.php?error=Failed to delete Student");
}
exit;