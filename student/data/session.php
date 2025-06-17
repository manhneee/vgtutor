
<?php
function insertSession($conn, $studentid, $tutorid, $courseid, $date_and_time, $duration, $paid = 0, $place) {
    $stmt = $conn->prepare(
        "INSERT INTO session (studentid, tutorid, courseid, date_and_time, duration, paid, place)
         VALUES (?, ?, ?, ?, ?, ?, ?)"
    );
    return $stmt->execute([$studentid, $tutorid, $courseid, $date_and_time, $duration, $paid, $place]);
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



?>