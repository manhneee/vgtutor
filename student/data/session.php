<?php
function insertSession($conn, $studentid, $tutorid, $courseid, $date_and_time, $duration, $place, $paid = 0)
{
    $stmt = $conn->prepare(
        "INSERT INTO session (studentid, tutorid, courseid, date_and_time, duration, place, paid)
         VALUES (?, ?, ?, ?, ?, ?, ?)"
    );
    return $stmt->execute([$studentid, $tutorid, $courseid, $date_and_time, $duration, $place, $paid]);
}

function getStudentSessions($conn, $studentid)
{
    $stmt = $conn->prepare(
        "SELECT s.duration AS duration,
                        s.studentid AS studentid,
                        s.tutorid AS tutorid,
                        s.date_and_time AS date_and_time,
                        s.consensus AS consensus, 
                        s.place AS place,
                        s.paid AS paid,
                        s.courseid AS courseid,
                        sa.name AS tutor_name, 
                        co.price AS price_per_hour,
                        c.course_name AS course_name
                 FROM session s
                 JOIN course_offering co ON s.tutorid = co.tutorid AND s.courseid = co.courseid
                 JOIN student_account sa ON s.tutorid = sa.accountid
                 JOIN course c ON s.courseid = c.courseid
                 WHERE s.studentid = ?
                 ORDER BY s.date_and_time DESC"
    );
    $stmt->execute([$studentid]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}






function getAllStudentSessionNotifications($conn, $studentid)
{
    $sql = "SELECT s.*, 
                   accountid AS tutor_name, 
                   c.course_name  AS course_name
            FROM session s
            JOIN tutor_account t ON s.tutorid = t.accountid 
            JOIN course c ON s.courseid = c.courseid
            WHERE s.studentid = ? AND s.consensus = 'accepted'
            ORDER BY s.date_and_time DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$studentid]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function markStudentSessionNotified($conn, $studentid, $tutorid, $courseid, $datetime)
{
    $sql = "UPDATE session SET notified = 1 
            WHERE studentid = ? AND tutorid = ? AND courseid = ? AND date_and_time = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$studentid, $tutorid, $courseid, $datetime]);
}
