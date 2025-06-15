<?php

function getAllTutors($conn) {
    $sql = "SELECT * FROM `tutor_account`
            INNER JOIN `account` ON tutor_account.accountid = account.userid
            INNER JOIN `student_account` ON tutor_account.accountid = student_account.accountid";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $tutors = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $tutors;
    } else {
        return [];
    }
}

function getTutor($conn, $tutorid) {
    $sql = "SELECT * FROM `tutor_account`
            INNER JOIN `account` ON tutor_account.accountid = account.userid
            INNER JOIN `student_account` ON tutor_account.accountid = student_account.accountid
            WHERE tutor_account.accountid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$tutorid]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function deleteTutor($conn, $tutorid) {
    $sql = "DELETE FROM tutor_account WHERE accountid = ?";
    $stmt = $conn->prepare($sql);
    return $stmt->execute([$tutorid]);
}
?>