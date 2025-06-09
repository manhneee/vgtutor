<?php
    function getAllCourseOfferings($conn) {
        $sql = "SELECT * FROM `course_offering`";
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $course_offering = $stmt->fetchAll();
            return $course_offering;
        } else {
            return 0;
        }
    }

?>