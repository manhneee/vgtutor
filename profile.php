<?php 
session_start();

if (!isset($_SESSION['role'])) {
    header("Location: ../login.php?error=Unauthorized");
    exit;
}

include_once $_SERVER['DOCUMENT_ROOT'] . "/vgtutor/DB_connection.php";

// Shared fields
$name = $_SESSION['name'] ?? 'Unknown';
$email = $_SESSION['email'] ?? 'Not Provided';
$major = $_SESSION['major'] ?? 'Not Provided';
$intake = $_SESSION['intake'] ?? 'Not Provided';

if ($_SESSION['role'] === 'Student') {
    $id = $_SESSION['studentid'];
    include_once __DIR__ . '/data/fetchStudentInfo.php';
    $studentInfo = fetchStudentInfo($conn, $id);
    $name = $studentInfo['name'] ?? 'Unknown';
    $email = $studentInfo['email'] ?? 'Not Provided';
    $major = $studentInfo['major'] ?? 'Not Provided';
    $intake = $studentInfo['intake'] ?? 'Not Provided';
} elseif ($_SESSION['role'] === 'Tutor') {
    $id = $_SESSION['tutorid'];
    include_once __DIR__ . '/data/fetchTutorInfo.php';
    $tutorInfo = fetchTutorInfo($conn, $id);
    $name = $tutorInfo['name'] ?? 'Unknown';
    $email = $tutorInfo['email'] ?? 'Not Provided';
    $major = $tutorInfo['major'] ?? 'Not Provided';
    $intake = $tutorInfo['intake'] ?? 'Not Provided';
    $gpa = $tutorInfo['gpa'] ?? 'Not Provided';
    $bankName = $tutorInfo['bank_name'] ?? 'Not Provided';
    $bankAcc = $tutorInfo['bank_acc_no'] ?? 'Not Provided';
    $description = $tutorInfo['description'] ?? 'Not Provided';    
} else {
    header("Location: ../login.php?error=Unauthorized");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Profile</title>
    <link rel="stylesheet" href="css/all.min.css" />
    <link rel="stylesheet" href="css/profile.css" />
    <link rel="stylesheet" href="css/framework.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;500&display=swap" rel="stylesheet" />
</head>

<body>
    <div class="head bg-white p-15 between-flex">
        <div>
            <a href="/vgtutor/<?= $_SESSION['role'] === 'Tutor' ? 'tutor' : 'student' ?>/index.php"
               class=" d-block fs-14 bg-orange c-white w-fit btn-shape">Back</a>
        </div>
        <div class="icons d-flex align-center">
            <div class="d-flex align-items-center gap-2">
                <div class="text-end me-2" style="font-family: 'Open Sans', sans-serif;">
                    <div class="fw-semibold text-dark"><?= htmlspecialchars($name) ?></div>
                    <div class="text-muted small">ID: <?= htmlspecialchars($id) ?></div>
                </div>
                <div style="width: 36px; height: 36px;">
                    <i class="fa fa-user"></i>
                </div>
            </div>
        </div>
    </div>

    <h1 class="p-relative c-orange">Profile</h1>
    <div class="profile-page m-20">
        <div class="overview bg-white rad-10 d-flex align-center">
            <div class="avatar-box txt-c p-20">
                <a href="#" class="d-block fs-14 bg-orange c-white w-fit btn-shape edit-profile-btn">Edit Profile</a>
                <img class="rad-half mb-10" src="img/avatar.png" alt="" />
                <h3 class="m-0"><?= htmlspecialchars($name) ?></h3>
                <p class="c-grey mt-10"><?= htmlspecialchars($major) ?></p>
                <div class="level rad-6 bg-eee p-relative">
                    <span style="width: 70%"></span>
                </div>
                <div class="rating mt-10 mb-10">
                    <i class="fa-solid fa-star c-orange fs-13"></i>
                    <i class="fa-solid fa-star c-orange fs-13"></i>
                    <i class="fa-solid fa-star c-orange fs-13"></i>
                    <i class="fa-solid fa-star c-orange fs-13"></i>
                    <i class="fa-solid fa-star c-orange fs-13"></i>
                </div>
                <p class="c-grey m-0 fs-13">Intake: <?= htmlspecialchars($intake) ?></p>
            </div>
            <div class="info-box w-full txt-c-mobile">
                <div class="box p-20 d-flex align-center">
                    <h4 class="c-grey fs-15 m-0 w-full">Full name</h4>
                    <div class="fs-14">
                        <span><?= htmlspecialchars($name) ?></span>
                    </div>
                </div>
                <div class="box p-20 d-flex align-center">
                    <h4 class="c-grey w-full fs-15 m-0">Major</h4>
                    <div class="fs-14">
                        <span><?= htmlspecialchars($major) ?></span>
                    </div>
                </div>
                <div class="box p-20 d-flex align-center">
                    <h4 class="c-grey w-full fs-15 m-0">Intake</h4>
                    <div class="fs-14">
                        <span><?= htmlspecialchars($intake) ?></span>
                    </div>
                </div>
                <div class="box p-20 d-flex align-center">
                    <h4 class="c-grey w-full fs-15 m-0">Email</h4>
                    <div class="fs-14">
                        <span><?= htmlspecialchars($email) ?></span>
                    </div>
                </div>

                <?php if ($_SESSION['role'] === 'Tutor'): ?>
                    <div class="box p-20 d-flex align-center">
                        <h4 class="c-grey w-full fs-15 m-0">GPA</h4>
                        <div class="fs-14">
                            <span><?= htmlspecialchars($gpa ?? 'Not Provided') ?></span>
                        </div>
                    </div>
                    <div class="box p-20 d-flex align-center">
                        <h4 class="c-grey w-full fs-15 m-0">Bank Name</h4>
                        <div class="fs-14">
                            <span><?= htmlspecialchars($bankName ?? 'Not Provided') ?></span>
                        </div>
                    </div>
                    <div class="box p-20 d-flex align-center">
                        <h4 class="c-grey w-full fs-15 m-0">Bank Account</h4>
                        <div class="fs-14">
                            <span><?= htmlspecialchars($bankAcc ?? 'Not Provided') ?></span>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>

