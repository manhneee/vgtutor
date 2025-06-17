<?php
// Get new (unnotified) session for this tutor
function getNewSessionNotification($conn, $tutorid) {
    $stmt = $conn->prepare(
        "SELECT s.*, st.name AS student_name, c.course_name
         FROM session s
         JOIN student_account st ON s.studentid = st.accountid
         JOIN course c ON s.courseid = c.courseid
         WHERE s.tutorid = ? AND (s.notified IS NULL OR s.notified = 0)
         ORDER BY s.date_and_time DESC LIMIT 1"
    );
    $stmt->execute([$tutorid]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


// Mark session as notified
function markSessionNotified($conn, $studentid, $tutorid, $courseid, $date_and_time) {
    $stmt = $conn->prepare(
        "UPDATE session SET notified = 1 WHERE studentid = ? AND tutorid = ? AND courseid = ? AND date_and_time = ?"
    );
    return $stmt->execute([$studentid, $tutorid, $courseid, $date_and_time]);
}

//Get tutor sessions
function getTutorSessions($conn, $tutorid) {
    $stmt = $conn->prepare(
        "SELECT s.*, 
                sa.name AS student_name, 
                sa.major AS student_major,
                co.price AS price_per_hour,
                c.course_name AS course_name
         FROM session s
         JOIN course_offering co ON s.tutorid = co.tutorid AND s.courseid = co.courseid
         JOIN course c ON s.courseid = c.courseid
         JOIN student_account sa ON s.studentid = sa.accountid
         WHERE s.tutorid = ?
         ORDER BY s.date_and_time DESC"
    );
    $stmt->execute([$tutorid]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get pending sessions for a tutor and a student.
 * Joins session with course table to obtain course_name.
 */
function getPendingSessions($conn, $tutorid, $studentid) {
    $query = "SELECT s.tutorid as tutorid, s.studentid as studentid, s.consensus as consensus, s.courseid as courseid, c.course_name as course_name, s.duration as duration, s.place as place, s.date_and_time as date_and_time
              FROM session s
              JOIN course c ON s.courseid = c.courseid
              WHERE s.tutorid = ? AND s.studentid = ? 
                AND (s.consensus = 'pending' OR s.consensus IS NULL)";
    $stmt = $conn->prepare($query);
    $stmt->execute([$tutorid, $studentid]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Update the consensus for a session identified by tutorid, studentid, courseid, and date_and_time.
 */
function updateConsensus($conn, $action, $tutorid, $studentid, $courseid, $date_and_time) {
    if($action == "accept") {
        $query = "UPDATE session SET consensus = 'accepted' 
                  WHERE tutorid = ? AND studentid = ? AND courseid = ? AND date_and_time = ?";
    }
    else if($action == "deny") {
        $query = "UPDATE session SET consensus = 'denied' 
                  WHERE tutorid = ? AND studentid = ? AND courseid = ? AND date_and_time = ?";
    }
    $stmt = $conn->prepare($query);
    $stmt->execute([$tutorid, $studentid, $courseid, $date_and_time]);
    return $stmt->rowCount();
}

function updateSessionDetails($conn, $tutorid, $studentid, $courseid, $originalDateTime, $newPlace, $newDateTime, $newDuration) {
    $query = "UPDATE session SET place = ?, date_and_time = ?, duration = ?
              WHERE tutorid = ? AND studentid = ? AND courseid = ? AND date_and_time = ?";
    $stmt = $conn->prepare($query);
    return $stmt->execute([$newPlace, $newDateTime, $newDuration, $tutorid, $studentid, $courseid, $originalDateTime]);
}

?>