<?php
include "../../DB_connection.php";

function getAllPendingOfferings($conn) {
    $sql = "
        SELECT 
            po.tutorid,
            sa.name AS tutor_name,
            po.courseid,
            c.course_name,
            c.major,
            po.gpa,
            po.price,
            po.self_description
        FROM pending_offering po
        JOIN tutor_account ta ON po.tutorid = ta.accountid
        JOIN student_account sa ON ta.accountid = sa.accountid
        JOIN course c ON po.courseid = c.courseid
        WHERE po.status = 'pending'
        ORDER BY po.tutorid, po.courseid
    ";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function processPendingOfferingAction($conn, $action, $tutorid, $courseid, $gpa = null, $price = null) {
    if ($action === 'permit') {
        // Insert into offering table
        $insertSql = "INSERT INTO course_offering (tutorid, courseid, tutor_grade, rating, price) VALUES (?, ?, ?, NULL, ?)";
        $stmt = $conn->prepare($insertSql);
        $stmt->execute([$tutorid, $courseid, $gpa, $price]);

        // Update status in pending_offering (permitted)
        $updateSql = "UPDATE pending_offering SET status = 'permitted' WHERE tutorid = ? AND courseid = ?";
        $stmt = $conn->prepare($updateSql);
        $stmt->execute([$tutorid, $courseid]);
    } elseif ($action === 'deny') {
        // Update status in pending_offering (denied)
        $updateSql = "UPDATE pending_offering SET status = 'deny' WHERE tutorid = ? AND courseid = ?";
        $stmt = $conn->prepare($updateSql);
        $stmt->execute([$tutorid, $courseid]);
    }
}

function getPendingOfferingStatus($conn, $tutorid, $courseid) {
    $sql = "SELECT status FROM pending_offering WHERE tutorid = ? AND courseid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$tutorid, $courseid]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? $row['status'] : null;
}
?>