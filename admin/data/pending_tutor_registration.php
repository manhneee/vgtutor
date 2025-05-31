<?php
function getAllPendingTutorRegistrations($conn) {
    $sql = "SELECT sa.name, sa.major, tr.gpa, tr.self_description, tr.studentid, tr.status
            FROM tutor_registration tr
            JOIN student_account sa ON tr.studentid = sa.accountid
            WHERE tr.status = 'pending'";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getPendingTutorStatus($conn, $studentid) {
    $sql = "SELECT status FROM tutor_registration WHERE studentid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$studentid]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? $row['status'] : null;
}

function processPendingTutorAction($conn, $action, $studentid) {
    if ($action === 'permit') {
        $status = 'permitted';

    } elseif ($action === 'deny') {
        $status = 'denied';
    } else {
        return false;
    }
    $sql = "UPDATE tutor_registration SET status = ? WHERE studentid = ?";
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute([$status, $studentid]);
    // If permitted, insert into tutor_account
    if ($result && $action === 'permit') {
        $sqlGet = "SELECT bank_name, bank_acc_no, gpa, self_description FROM tutor_registration tr JOIN student_account sa ON tr.studentid = sa.accountid WHERE studentid = ? AND tr.status = 'permitted'";
        $stmtGet = $conn->prepare($sqlGet);
        $stmtGet->execute([$studentid]);
        $reg = $stmtGet->fetch(PDO::FETCH_ASSOC);

        if ($reg) {
            $insertSql = "INSERT INTO tutor_account (accountid, bank_name, bank_acc_no, gpa, description, overall_rating) VALUES (?, ?, ?, ?, ?, NULL)";
            $stmtInsert = $conn->prepare($insertSql);
            $stmtInsert->execute([
                $studentid,
                $reg['bank_name'],
                $reg['bank_acc_no'],
                $reg['gpa'],
                $reg['self_description']
            ]);
        }
    }

    return $result;
}
?>