<?php
include __DIR__ . '/../DB_connection.php';

function registerStudent($conn, $studentid, $password, $fullname, $email, $major, $intake) {
    try {
        // Check if account already exists
        $checkSql = "SELECT userid FROM account WHERE userid = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->execute([$studentid]);
        if ($checkStmt->fetch()) {
            return "The account has existed.";
        }

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert into account table
        $sql1 = "INSERT INTO account (userid, password) VALUES (?, ?)";
        $stmt1 = $conn->prepare($sql1);
        $stmt1->execute([$studentid, $hashedPassword]);

        // Insert into student_account table
        $sql2 = "INSERT INTO student_account (accountid, email, name, major, intake) VALUES (?, ?, ?, ?, ?)";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->execute([$studentid, $email, $fullname, $major, $intake]);

        return true;
    } catch (PDOException $e) {
        return $e->getMessage();
    }
}
?>