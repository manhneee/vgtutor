<?php
function registerTutorApplication($conn, $studentid, $gpa, $bank_name, $bank_acc_no, $self_description) {
    try {
        // Check if already registered
        $checkSql = "SELECT studentid FROM tutor_registration WHERE studentid = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->execute([$studentid]);
        if ($checkStmt->fetch()) {
            return "You have already applied to become a tutor.";
        }

        $sql = "INSERT INTO tutor_registration 
                    (studentid, status, gpa, bank_name, bank_acc_no, self_description)
                VALUES (?, 'pending', ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$studentid, $gpa, $bank_name, $bank_acc_no, $self_description]);
        return true;
    } catch (PDOException $e) {
        return $e->getMessage();
    }
}
?>