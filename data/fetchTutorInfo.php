<?php
// Fetch all tutor info from the database
global $conn;
function fetchTutorInfo($conn, $tutorid) {
    $stmt = $conn->prepare("SELECT sa.name, sa.email, sa.major, sa.intake, ta.gpa, ta.bank_name, ta.bank_acc_no, ta.description
        FROM student_account sa
        JOIN tutor_account ta ON sa.accountid = ta.accountid
        WHERE ta.accountid = ?");
    $stmt->execute([$tutorid]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
