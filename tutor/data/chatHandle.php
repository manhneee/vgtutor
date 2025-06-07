<?php
function fetchAllStudentMessage($conn, $tutorid) {
    $stmt = $conn->prepare(
        "SELECT DISTINCT s.studentid, sa.name
         FROM session s
         JOIN student_account sa ON s.studentid = sa.accountid
         WHERE s.tutorid = ? AND s.tutor_chat_requested = 1"
    );
    $stmt->execute([$tutorid]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

?>