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
<div class="sidebar bg-white p-20 p-relative">
  <div class="sidebar-header">
    <h3 class="txt-c c-orange mt-0">Vgtutor</h3>
      </div>
    <ul>
        <li><a class="d-flex align-center fs-14 c-orange rad-6 p-10" href="/vgtutor/student/index.php"><i class="fa-regular fa-chart-bar fa-fw"></i><span>Dashboard</span></a></li>
        <li><a class="d-flex align-center fs-14 c-orange rad-6 p-10" href="/vgtutor/student/chat_process/chat.php"><i class="fa-solid fa-message fa-fw"></i><span>Mesage</span></a></li>
        <li><a class="d-flex align-center fs-14 c-orange rad-6 p-10" href="/vgtutor/student/switch_to_tutor.php"><i class="fa-solid fa-user fa-fw"></i><span>Tutor Mode</span></a></li>
        <li><a class="d-flex align-center fs-14 c-orange rad-6 p-10" href="/vgtutor/student/course_process/courseSelection.php"><i class="fa-solid fa-book fa-fw"></i><span>Courses</span></a></li>
        <li><a class="d-flex align-center fs-14 c-orange rad-6 p-10" href="/vgtutor/student/session_process/session.php"><i class="fa-solid fa-calendar-days fa-fw"></i><span>Session</span></a></li>
        <li><a class="d-flex align-center fs-14 c-orange rad-6 p-10" href="/vgtutor/profile.php"><i class="fa-regular fa-user fa-fw"></i><span>Profile</span></a></li>
        <li><a class="d-flex align-center fs-14 c-orange rad-6 p-10" href="/vgtutor/student/signupTutor/signupTutor.php"><i class="fa-solid fa-users fa-fw"></i><span>Become a tutor</span></a></li>
        <li><a class="d-flex align-center fs-14 c-orange rad-6 p-10" href="/vgtutor/logout.php">Sign Out</a>

</div>
<!-- End Sidebar -->