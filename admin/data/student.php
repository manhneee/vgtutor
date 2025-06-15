<?php

function getAllStudents($conn) {
    $sql = "SELECT * FROM student_account";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getStudent($conn, $studentid) {
    $sql = "SELECT * FROM student_account WHERE accountid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$studentid]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function updateStudent($conn, $studentid, $email, $name, $major, $intake) {
    $sql = "UPDATE student_account SET email = ?, name = ?, major = ?, intake = ? WHERE accountid = ?";
    $stmt = $conn->prepare($sql);
    return $stmt->execute([$email, $name, $major, $intake, $studentid]);
}

function deleteStudent($conn, $studentid) {
    // Delete from student_account
    $sql1 = "DELETE FROM student_account WHERE accountid = ?";
    $stmt1 = $conn->prepare($sql1);
    $success = $stmt1->execute([$studentid]);
    // Delete from account table
    $sql2 = "DELETE FROM account WHERE userid = ?";
    $stmt2 = $conn->prepare($sql2);
    $success2 = $stmt2->execute([$studentid]);
    return $success && $success2;
}
?>