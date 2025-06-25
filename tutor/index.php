<?php
session_start();
if (isset($_SESSION['tutorid']) && isset($_SESSION['role'])) {
  if ($_SESSION['role'] == 'Tutor') {
    include "../DB_connection.php";
    include "data/session.php";

?>


    <!DOCTYPE html>
    <html lang="en">

    <head>
      <meta charset="UTF-8" />
      <meta http-equiv="X-UA-Compatible" content="IE=edge" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <title>Dashboard</title>


      <link rel="stylesheet" href="../css/style1.css" />
      <link rel="stylesheet" href="../css/framework.css" />
      <link rel="stylesheet" href="../css/master.css" />
      <link rel="preconnect" href="https://fonts.googleapis.com" />
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
      <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;500&display=swap" rel="stylesheet" />
    </head>


    <body>
      <div class="page d-flex">
        <?php include_once '../tutor/inc/navbar.php'; ?> <!-- LEFT BAR -->

        <div class="content w-full">
          <?php include_once '../tutor/inc/upbar.php'; ?> <!-- upbar -->


          <h1 class="c-orange p-relative">Dashboard</h1>
          <div class="wrapper d-grid gap-20">
            <!-- Start Welcome Widget -->
            <div class="welcome bg-white rad-10 txt-c-mobile block-mobile">
              <div class="intro p-20 d-flex space-between bg-orange">
                <div>
                  <h2 class="c-white m-0">Welcome Back,</h2>
                  <p class="c-white mt-5"><?= htmlspecialchars($_SESSION['name']) ?></p>
                </div>
                <img class="hide-mobile" src="../imgs/welcome_tutor.png" alt="" />
              </div>
              <img src="../img/avatar.png" alt="" class="avatar" />
              <div class="body txt-c d-flex p-20 mt-20 mb-20 block-mobile">
                <div>
                  <span class="d-block fs-14 fw-bold c-orange"><?= htmlspecialchars($_SESSION['name']) ?></span>
                  <span class="d-block c-grey fs-14 mt-10">
                    You are logged in as <?= htmlspecialchars($_SESSION['role']) ?>
                  </span>
                </div>
                <div>
                  <span class="d-block fs-14 fw-bold c-orange">3</span>
                  <span class="d-block c-grey fs-14 mt-10"> Curent offered Courses</span>
                </div>

              </div>
              <a href="switch_to_student.php" class="visit d-block fs-14 bg-orange c-white w-fit btn-shape">Swtich to Student Mode</a>
            </div>
            <!-- End Welcome Widget -->

            <!-- begin tutor Widget -->
            <div class="welcome bg-white rad-10 txt-c-mobile block-mobile">
              <!-- Intro Section -->
              <div class="intro p-20 d-flex space-between bg-orange">
                <div>
                  <h2 class="m-0 c-white">Let's begin our Tutor's journey</h2>
                  <span class="d-block c-white fs-14 mt-10">A new chapter of guiding minds, sharing wisdom, and making a lasting impact.</span>
                </div>
                <img class="hide-mobile" src="../img/tutor.png" />
              </div>

              <!-- Body Section -->
              <div class="body txt-c d-flex p-20 mt-20 mb-20 block-mobile">
                <div>
                  <span class="d-block fs-14 fw-bold c-orange">80</span>
                  <span class="d-block c-grey fs-14 mt-10">Current tutors</span>
                </div>
                <div>
                  <span class="d-block fs-14 fw-bold c-orange">80</span>
                  <span class="d-block c-grey fs-14 mt-10">Courses</span>
                </div>
                <div>
                  <span class="d-block fs-14 fw-bold c-orange">80</span>
                  <span class="d-block c-grey fs-14 mt-10">Courses</span>
                </div>
              </div>
              <a href="/vgtutor/tutor/course/course.php" class="visit d-block fs-14 bg-orange c-white w-fit btn-shape">Offering course</a>
            </div>

            <!-- End tuttor Widget -->

            <!-- Start Dynamic Session Widget -->
            <div class="latest-news p-20 bg-white rad-10 txt-c-mobile">
              <h2 class="mt-0 c-orange mb-20">
                <i class="fa fa-clock-o me-2 text-orange"></i> Your Pending Sessions With Students
              </h2>

              <?php
              include_once "data/session.php";
              $sessions = getTutorPendingSessions($conn, $_SESSION['tutorid']);
              if (!empty($sessions)):
                $limit = 0;
                foreach ($sessions as $session):
                  if ($limit >= 2) break;
                  $datetime = htmlspecialchars($session['date_and_time']);
                  $studentName = htmlspecialchars($session['student_name']);
                  $courseName = htmlspecialchars($session['course_name']);
              ?>
                  <div class="news-row d-flex align-center mb-15 border-bottom pb-2">

                    <div class="info text-start">
                      <h3 class="fs-16 fw-bold mb-5 c-green">
                        <i class="fa fa-user me-1 text-success"></i> <?= $studentName ?>
                      </h3>
                      <p class="fs-13 c-black m-0">
                        <i class="fa fa-book me-1 text-primary"></i> <?= $courseName ?>
                      </p>
                      <p class="fs-13 c-grey mt-1">
                        <i class="fa fa-calendar me-1 text-warning"></i> <?= $datetime ?>
                      </p>
                    </div>
                  </div>
              <?php
                  $limit++;
                endforeach;
              else:
                echo "<p class='c-grey'><i class='fa fa-info-circle me-2 text-muted'></i>No pending sessions at the moment.</p>";
              endif;
              ?>

              <a href="session/session.php" class="visit d-block fs-14 bg-orange c-white w-fit btn-shape mt-15">
                <i class="fa fa-list me-1"></i> See All Sessions
              </a>
            </div>
            <!-- End Dynamic Session Widget -->



            <!-- Start Reading Rate tutor Widget -->
            <div class="welcome bg-white rad-10 txt-c-mobile block-mobile">

              <!-- Intro Section -->
              <div class="intro p-20 d-flex space-between bg-orange">
                <div>
                  <h2 class="m-0 c-white">Read What Other Student Talk About You</h2>
                  <p class="c-white fs-14 mt-10">
                  </p>
                </div>
                <img class="hide-mobile" src="../img/rateTutor.png" />
              </div>

              <!-- Body Section -->
              <div class="body txt-c d-flex block-mobile" style="padding: 0;"></div>
              <a href="/vgtutor/tutor/course/tutorReviews.php?tutorid=<?= $_SESSION['tutorid'] ?>"
                class="visit d-block fs-14 bg-orange c-white w-fit btn-shape"
                style="margin-top: 3rem;">
                View
              </a>


            </div>
            <!-- End Reading rate tutor Widget -->

    </body>

    </html>
<?php
  } else {
    $em = "You are not authorized to access this page.";
    header("Location: ../login.php?error=$em");
    // header("Location: ../login.php");
    exit;
  }
} else {
  $em = "You are not logged in.";
  header("Location: ../login.php?error=$em");
  // header("Location: ../login.php");
  exit;
}
?>