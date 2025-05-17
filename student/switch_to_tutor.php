<?php

session_start();
if (isset($_SESSION['tutorid'])) {
    $_SESSION['role'] = 'Tutor';
    header("Location: ../tutor/index.php");
    exit;
} else {
    echo "<h2>Warning: You are not registered as a tutor.</h2>";
    echo "<a href='javascript:history.back()'>Go Back</a>";
    exit;
}
?>