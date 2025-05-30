
<?php
function getAllCourse($conn) {
    $sql = "SELECT 
                course.courseid AS course_courseid,
                course.course_name AS course_name,
                course.major AS major
            FROM course";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        return [];
    }
}
?>