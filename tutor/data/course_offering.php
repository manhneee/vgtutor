<?php
include_once "../../DB_connection.php";

function insertPendingOffering($tutorid, $courseid, $gpa, $price, $self_description) {
    global $conn;
    $status = 'pending';
    try {
        $sql = "INSERT INTO pending_offering (tutorid, courseid, status, gpa, price, self_description)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$tutorid, $courseid, $status, $gpa, $price, $self_description]);
        return true;
    } catch (PDOException $e) {
        return $e->getMessage();
    }
}

function getTutorPendingOfferings($conn, $tutorid) {
    $sql = "
        SELECT 
            po.tutorid,
            po.courseid as course_courseid,
            c.course_name,
            c.major as major,
            po.gpa as gpa,
            po.price as price,
            po.self_description as self_description,
            po.status as status
        FROM pending_offering po
        JOIN course c ON po.courseid = c.courseid
        WHERE po.tutorid = ?
        ORDER BY po.courseid
    ";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$tutorid]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>