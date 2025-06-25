<?php
// Fetch all student info from the database
global $conn;
function fetchStudentInfo($conn, $studentid) {
    $stmt = $conn->prepare("SELECT * FROM student_account WHERE accountid = ?");
    $stmt->execute([$studentid]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
