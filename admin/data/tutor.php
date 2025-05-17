<?php
    function getAllTutors($conn) {
        $sql = "SELECT * FROM `tutor_account`
                INNER JOIN `account` ON tutor_account.accountid = account.userid
                INNER JOIN `student_account` ON tutor_account.accountid = student_account.accountid";
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $tutors = $stmt->fetchAll();
            return $tutors;
        } else {
            return 0;
        }
    }

?>