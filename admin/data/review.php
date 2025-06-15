<?php

// Get all reviews (with joined names)
function getAllReviews($conn) {
    $sql = "SELECT 
                r.*, 
                s.name AS student_name, 
                t.accountid AS tutorid, 
                sa.name AS tutor_name, 
                c.course_name 
            FROM review r
            JOIN student_account s ON r.studentid = s.accountid
            JOIN tutor_account t ON r.tutorid = t.accountid
            JOIN student_account sa ON t.accountid = sa.accountid
            JOIN course c ON r.courseid = c.courseid";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get all reviews for a specific tutor
function getReviewsByTutor($conn, $tutorid) {
    $sql = "SELECT 
                r.*, 
                s.name AS student_name, 
                c.course_name 
            FROM review r
            JOIN student_account s ON r.studentid = s.accountid
            JOIN course c ON r.courseid = c.courseid
            WHERE r.tutorid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$tutorid]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get all reviews for a specific student
function getReviewsByStudent($conn, $studentid) {
    $sql = "SELECT 
                r.*, 
                sa.name AS tutor_name, 
                c.course_name 
            FROM review r
            JOIN tutor_account t ON r.tutorid = t.accountid
            JOIN student_account sa ON t.accountid = sa.accountid
            JOIN course c ON r.courseid = c.courseid
            WHERE r.studentid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$studentid]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get a specific review
function getReview($conn, $studentid, $tutorid, $courseid) {
    $sql = "SELECT r.*, 
                   s.name AS student_name, 
                   t.accountid AS tutorid, 
                   sa.name AS tutor_name, 
                   c.course_name 
            FROM review r
            JOIN student_account s ON r.studentid = s.accountid
            JOIN tutor_account t ON r.tutorid = t.accountid
            JOIN student_account sa ON t.accountid = sa.accountid
            JOIN course c ON r.courseid = c.courseid
            WHERE r.studentid = ? AND r.tutorid = ? AND r.courseid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$studentid, $tutorid, $courseid]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Add a review
function addReview($conn, $studentid, $tutorid, $courseid, $rating, $review) {
    $sql = "INSERT INTO review (studentid, tutorid, courseid, rating, review) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    return $stmt->execute([$studentid, $tutorid, $courseid, $rating, $review]);
}

// Update a review
function updateReview($conn, $studentid, $tutorid, $courseid, $rating, $review) {
    $sql = "UPDATE review SET rating = ?, review = ? WHERE studentid = ? AND tutorid = ? AND courseid = ?";
    $stmt = $conn->prepare($sql);
    return $stmt->execute([$rating, $review, $studentid, $tutorid, $courseid]);
}

// Delete a review
function deleteReview($conn, $studentid, $tutorid, $courseid) {
    $sql = "DELETE FROM review WHERE studentid = ? AND tutorid = ? AND courseid = ?";
    $stmt = $conn->prepare($sql);
    return $stmt->execute([$studentid, $tutorid, $courseid]);
}
?>