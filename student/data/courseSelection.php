<?php
function searchCourse($conn, $search_name = '', $search_major = '', $search_semester = '')
{
    $sql = "SELECT courseid, course_name, major, semester FROM course WHERE 1";
    $params = [];

    if ($search_name !== '') {
        $sql .= " AND course_name LIKE ?";
        $params[] = "%$search_name%";
    }
    if ($search_major !== '') {
        $sql .= " AND major = ?";
        $params[] = $search_major;
    }
    if ($search_semester !== '') {
        $sql .= " AND semester = ?";
        $params[] = $search_semester;
    }

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function getCourseName($conn, $courseid)
{
    $course_stmt = $conn->prepare("SELECT course_name FROM course WHERE courseid = ?");
    $course_stmt->execute([$courseid]);
    $course_row = $course_stmt->fetch(PDO::FETCH_ASSOC);
    return $course_row ? $course_row['course_name'] : '';
}

function tutorFetching($conn, $courseid)
{
    $stmt = $conn->prepare("
        SELECT 
            ta.accountid AS tutorid,
            sa.name AS tutor_name,
            sa.major,
            ta.gpa,
            ta.description,
            co.price,
            sa.email,
            ROUND(COALESCE(AVG(r.rating), 0), 1) AS rating
        FROM course_offering co
        JOIN tutor_account ta ON co.tutorid = ta.accountid
        JOIN student_account sa ON ta.accountid = sa.accountid
        LEFT JOIN review r ON r.tutorid = ta.accountid
        WHERE co.courseid = ?
        GROUP BY ta.accountid, sa.name, sa.major, ta.gpa, ta.description, co.price, sa.email
    ");
    $stmt->execute([$courseid]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getTutorName($conn, $tutorid)
{
    $stmt = $conn->prepare("SELECT name FROM student_account WHERE accountid = ?");
    $stmt->execute([$tutorid]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? $row['name'] : '';
}


function getTutorReviews($conn, $tutorid)
{
    $stmt = $conn->prepare("SELECT rating, review FROM review WHERE tutorid = ?");
    $stmt->execute([$tutorid]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
