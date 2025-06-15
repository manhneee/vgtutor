
<?php
function insertSession($conn, $studentid, $tutorid, $courseid, $date_and_time, $duration, $paid = 0, $request_chat, $place) {
    $stmt = $conn->prepare(
        "INSERT INTO session (studentid, tutorid, courseid, date_and_time, duration, paid, student_chat_requested, place)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
    );
    return $stmt->execute([$studentid, $tutorid, $courseid, $date_and_time, $duration, $paid, $request_chat, $place]);
}

function getStudentSessions($conn, $studentid) {
            $stmt = $conn->prepare(
                "SELECT s.duration AS duration,
                        s.studentid AS studentid,
                        s.tutorid AS tutorid,
                        s.date_and_time AS date_and_time,
                        s.consensus AS consensus, 
                        s.place AS place,
                        s.paid AS paid,
                        s.courseid AS courseid,
                        s.student_chat_requested AS student_chat_requested,
                        s.tutor_chat_requested AS tutor_chat_requested,
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


function updateSessionConsensus($conn, $tutorid, $courseid, $consensus) {
    $stmt = $conn->prepare("UPDATE session SET consensus = ? WHERE tutorid = ? AND courseid = ?");
    return $stmt->execute([$consensus, $tutorid, $courseid]);
}

function handleChatResponse(PDO $conn, $studentid, $tutorid, $courseid, $date_and_time, $response) {
    if ($response === 'accept') {
        $stmt = $conn->prepare("
            UPDATE session 
            SET student_chat_requested = 1 
            WHERE studentid = ? AND tutorid = ? AND courseid = ? AND date_and_time = ?
        ");
        $stmt->execute([$studentid, $tutorid, $courseid, $date_and_time]);

        // Optional redirect to chat page
        header("Location: ../chat_process/chat.php?tutorid=" . urlencode($tutorid));
        exit;

    } elseif ($response === 'deny') {
        $stmt = $conn->prepare("
            UPDATE session 
            SET consensus = 'denied' 
            WHERE studentid = ? AND tutorid = ? AND courseid = ? AND date_and_time = ?
        ");
        $stmt->execute([$studentid, $tutorid, $courseid, $date_and_time]);
    } else {
        return false; // Invalid response
    }
}


?>