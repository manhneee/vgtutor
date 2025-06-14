<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$showBecomeTutor = true;
if (isset($_SESSION['studentid'])) {
    include_once $_SERVER['DOCUMENT_ROOT'] . "/vgtutor/DB_connection.php";
    if (isset($conn)) {
        $stmt = $conn->prepare("SELECT accountid FROM tutor_account WHERE accountid = ?");
        $stmt->execute([$_SESSION['studentid']]);
        if ($stmt->fetch()) {
            $showBecomeTutor = false;
        }
    } else {
        $showBecomeTutor = false;
    }
}
?>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">
            <img src="../img/logo.png" alt="Logo" width="200" height="" class="d-inline-block align-text-top">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0" id="navLinks">
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="../index.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="tutor.php">Tutors</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Students</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Registration-Office</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Class</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Section</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Schedule</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Course</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Message</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Settings</a>
                </li>
            </ul>
            <ul class="navbar-nav me-right mb-2 mb-lg-0">
                <?php if ($showBecomeTutor): ?>
                <li class="nav-item">
                    <a class="btn me-2" 
                        style="border: 2px solid #f47119; color: #f47119; background: #fff;" 
                        href="/vgtutor/student/signupTutor/signupTutor.php">
                        Become a Tutor
                    </a>
                </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="btn btn-outline-primary me-2" href="switch_to_tutor.php">Switch to Tutor Mode</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../logout.php">Sign Out</a>
                </li>
            </ul>
        </div>
    </div>
</nav>