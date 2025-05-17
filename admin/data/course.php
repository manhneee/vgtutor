<?php
    function getAllCourses($conn) {
        $sql = "SELECT * FROM `course`";
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $course = $stmt->fetchAll();
            return $course;
        } else {
            return 0;
        }
    }

?>