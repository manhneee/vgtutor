<?php

function getAllOfferings($conn) {
    $sql = "
        SELECT 
            o.*, 
            s.name AS tutor_name, 
            c.course_name 
        FROM course_offering o
        JOIN student_account s ON o.tutorid = s.accountid
        JOIN course c ON o.courseid = c.courseid
    ";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getOffering($conn, $tutorid, $courseid) {
    $sql = "
        SELECT 
            o.*, 
            s.name AS tutor_name, 
            c.course_name 
        FROM course_offering o
        JOIN student_account s ON o.tutorid = s.accountid
        JOIN course c ON o.courseid = c.courseid
        WHERE o.tutorid = ? AND o.courseid = ?
    ";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$tutorid, $courseid]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function updateOffering($conn, $tutorid, $courseid, $tutor_grade, $rating, $price) {
    $sql = "UPDATE course_offering SET tutor_grade = ?, rating = ?, price = ? WHERE tutorid = ? AND courseid = ?";
    $stmt = $conn->prepare($sql);
    return $stmt->execute([$tutor_grade, $rating, $price, $tutorid, $courseid]);
}

function deleteOffering($conn, $tutorid, $courseid) {
    $sql = "DELETE FROM course_offering WHERE tutorid = ? AND courseid = ?";
    $stmt = $conn->prepare($sql);
    return $stmt->execute([$tutorid, $courseid]);
}
?>