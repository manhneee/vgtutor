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

if (!isset($conn) || !($conn instanceof PDO)) {
    die("Database connection error.");
}

// Sanitize input
$studentid = intval($_GET['studentid']);

// Delete student from student_account table
$sql1 = "DELETE FROM student_account WHERE accountid = ?";
$stmt1 = $conn->prepare($sql1);

// Delete from account table
$sql2 = "DELETE FROM account WHERE userid = ?";
$stmt2 = $conn->prepare($sql2);

$success = false;
if ($stmt1 && $stmt2) {
    // First delete from student_account, then from account
    if ($stmt1->execute([$studentid])) {
        $stmt2->execute([$studentid]);
        $success = true;
    }
}

if ($success) {
    header("Location: student.php?success=Student deleted successfully");
} else {
    header("Location: student.php?error=Failed to delete Student");
}
exit;