<?php
session_start();
if (isset($_SESSION['studentid']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    include "../../DB_connection.php";

    $studentid = $_POST['studentid'];
    $tutorid = $_POST['tutorid'];
    $courseid = $_POST['courseid'];
    $reviewText = trim($_POST['reviewText']);
    $rating = intval($_POST['rating']);

    if ($rating < 1 || $rating > 5) {
        header("Location: ../session_process/session.php?error=Invalid rating value.");
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO review (studentid, tutorid, courseid, review, rating, date) VALUES (?, ?, ?, ?, ?, NOW())");
    if ($stmt->execute([$studentid, $tutorid, $courseid, $reviewText, $rating])) {
        header("Location: ../session_process/session.php?success=Review submitted successfully.");
        exit;
    } else {
        header("Location: ../session_process/session.php?error=Could not submit review.");
        exit;
    }

} else {
    header("Location: ../login.php?error=Unauthorized access.");
    exit;
}
?>