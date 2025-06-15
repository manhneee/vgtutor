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
                s.student_chat_requested AS student_chat_request,
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

function updateSessionConsensus($conn, $studentid, $courseid, $consensus) {
    $stmt = $conn->prepare("UPDATE session SET consensus = ? WHERE studentid = ? AND courseid = ?");
    return $stmt->execute([$consensus, $studentid, $courseid]);
}

function handleSessionAction($conn, $studentid, $courseid, $action) {
    if ($action === 'accept') {
        updateSessionConsensus($conn, $studentid, $courseid, 'accepted');
        header("Location: session.php");
        exit;
    } elseif ($action === 'deny') {
        updateSessionConsensus($conn, $studentid, $courseid, 'denied');
        header("Location: session.php");
        exit;
    } elseif ($action === 'accept_chat' || $action === 'request_chat') {
        $stmt = $conn->prepare("UPDATE session SET tutor_chat_requested = 1 WHERE studentid = ? AND courseid = ?");
        $stmt->execute([$studentid, $courseid]);
        header("Location: ../chat/chat.php?studentid=" . urlencode($studentid));
        exit;
    }
}

?>