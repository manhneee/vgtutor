<?php
function fetchAllTutorMessage($conn, $studentid) {
    $stmt = $conn->prepare(
        "SELECT DISTINCT s.tutorid, sa.name
         FROM session s
         JOIN student_account sa ON s.tutorid = sa.accountid
         WHERE s.studentid = ?"
    );
    $stmt->execute([$studentid]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

?>