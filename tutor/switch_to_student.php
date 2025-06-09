<?php

session_start();
if (isset($_SESSION['studentid'])) {
    $_SESSION['role'] = 'Student';
    header("Location: ../student/index.php");
    exit;
} else {
    header("Location: index.php");
    exit;
}
?>