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

<!-- Begin Sidebar -->
<div class="sidebar bg-white p-20 p-relative">
  <div class="sidebar-header">
    <h3 class="txt-c c-orange mt-0">Vgtutor</h3>
      </div>
    <ul>
        <li><a class="d-flex align-center fs-14 c-orange rad-6 p-10" href="/vgtutor/tutor/index.php"><i class="fa-regular fa-chart-bar fa-fw"></i><span>Dashboard</span></a></li>
        <li><a class="d-flex align-center fs-14 c-orange rad-6 p-10" href="/vgtutor/tutor/chat/chat.php"><i class="fa-solid fa-message fa-fw"></i><span>Messages</span></a></li>
        <li><a class="d-flex align-center fs-14 c-orange rad-6 p-10" href="/vgtutor/tutor/switch_to_student.php"><i class="fa-solid fa-user fa-fw"></i><span>Student Mode</span></a></li>
        <li><a class="d-flex align-center fs-14 c-orange rad-6 p-10" href="/vgtutor/tutor/session/session.php"><i class="fa-solid fa-calendar-days fa-fw"></i><span>Session</span></a></li>
        <li><a class="d-flex align-center fs-14 c-orange rad-6 p-10" href="/vgtutor/tutor/course/course_offered.php"><i class="fa-solid fa-book fa-fw"></i><span>Offered course</span></a></li>
        <li><a class="d-flex align-center fs-14 c-orange rad-6 p-10" href="/vgtutor/tutor/course/course.php"><i class="fa-solid fa-book fa-fw"></i><span>Available Course</span></a></li>
        <li><a class="d-flex align-center fs-14 c-orange rad-6 p-10" href="/vgtutor/profile.php"><i class="fa-regular fa-user fa-fw"></i><span>Profile</span></a></li>
        <li><a class="d-flex align-center fs-14 c-orange rad-6 p-10" href="/vgtutor/logout.php">Sign Out</a>

</div>
<!-- End Sidebar -->