<?php
include __DIR__ . '/../DB_connection.php';
// Trong req/signup.php
require_once __DIR__ . '/../verifyEmail/sendVerificationEmail.php';

function registerStudent($conn, $studentid, $password, $fullname, $email, $major, $intake)
{
    try {
        // Check if StudentID or Email already exists
        $checkSql = "SELECT userid FROM account WHERE userid = ? OR email = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->execute([$studentid, $email]);
        if ($checkStmt->fetch()) {
            return "Student ID or Email already exists!";
        }


        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $token = md5(uniqid($email, true));

        // Insert into account
        $sql1 = "INSERT INTO account (userid, email, password, is_verified, verify_token) VALUES ( ?, ?, ?,?, ?)";
        $stmt1 = $conn->prepare($sql1);
        $stmt1->execute([$studentid, $email, $hashedPassword, 0, $token]);

        // Insert into student_account
        $sql2 = "INSERT INTO student_account (accountid, email, name, major, intake)
                 VALUES (?, ?, ?, ?, ?)";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->execute([$studentid, $email, $fullname, $major, $intake]);

        // Nếu muốn bổ sung xác minh email, đây là chỗ để gọi sendVerificationEmail($email, $token);
        if (sendVerificationEmail($email, $token)) {
            return true;
        } else {
            return "Registration successful, but failed to send verification email. Please contact support.";
        }
        return true;
    } catch (PDOException $e) {
        // Có thể ghi log ra file nếu là production
        return "Registration error: " . $e->getMessage();
    }
}
