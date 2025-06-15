<?php
    function getAllCourses($conn) {
        $sql = "SELECT * FROM course";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function getCourse($conn, $courseid) {
        $sql = "SELECT * FROM course WHERE courseid = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$courseid]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function addCourse($conn, $courseid, $course_name, $major, $semester, $cond) {
        $sql = "INSERT INTO course (courseid, course_name, major, semester, cond) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([$courseid, $course_name, $major, $semester, $cond]);
    }

    function updateCourse($conn, $courseid, $course_name, $major, $semester, $cond) {
        $sql = "UPDATE course SET course_name = ?, major = ?, semester = ?, cond = ? WHERE courseid = ?";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([$course_name, $major, $semester, $cond, $courseid]);
    }

    function deleteCourse($conn, $courseid) {
        $sql = "DELETE FROM course WHERE courseid = ?";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([$courseid]);
    }
?>