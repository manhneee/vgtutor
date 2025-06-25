<?php
session_start();
if (isset($_SESSION['studentid']) && $_SESSION['role'] === 'Student') {
  include "../../DB_connection.php";
  include "../data/session.php";
  include "../data/courseSelection.php";
  include_once dirname(__DIR__, 2) . '/student/data/notifications.php';

  $tutorid = $_GET['tutorid'] ?? null;
  $courseid = $_GET['courseid'] ?? null;

  if (!$tutorid || !$courseid) {
    echo "Missing tutor or course information.";
    exit;
  }

  $tutor_name = getTutorName($conn, $tutorid);
  $course_name = getCourseName($conn, $courseid);

  $error = '';
  $success = '';

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'] ?? '';
    $time = $_POST['time'] ?? '';
    $duration = $_POST['duration'] ?? '';
    $place = $_POST['place'] ?? '';
    $date_and_time = "$date $time";

    if ($_SESSION['studentid'] == $tutorid) {
      $error = "You cannot set up a session with yourself as the tutor.";
    } else {
      // Correct order: ...duration, place, paid (default 0)
      $inserted = insertSession($conn, $_SESSION['studentid'], $tutorid, $courseid, $date_and_time, $duration, $place, 0);
      if ($inserted) {
        // --- Gửi notification cho tutor ---
        $student_name = $_SESSION['name'];
        $message = "$student_name has requested a session with you for the course \"$course_name\" at $date_and_time.";
        addNotification(
          $conn,
          $tutorid,                  // user_id_receive: tutor
          $_SESSION['studentid'],    // user_id_send: student
          "Session",         // title
          $message,                  // message
          "Session"                  // type
        );
      }
    }
    $success = $inserted ? "Session request sent!" : "Failed to set up session. Please try again.";
  }
?>
  <!DOCTYPE html>
  <html lang="en">

  <head>
    <meta charset="UTF-8" />
    <title>Set Up Session</title>
    <link rel="stylesheet" href="../../css/style1.css">
    <link rel="stylesheet" href="../../css/framework.css">
    <link rel="stylesheet" href="../../css/master.css">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;500&display=swap" rel="stylesheet">
  </head>

  <body class="body-home">
    <div class="page d-flex">
      <?php include_once '../inc/navbar.php'; ?>

      <div class="content w-full">
        <?php include_once '../inc/upbar.php'; ?> <!-- upbar -->

        <h1 class="c-orange m-20"><i class="fa fa-calendar-check me-2"></i>Set Up a Session</h1>

        <div style="display: flex; justify-content: center; align-items: center; min-height: calc(100vh - 60px); width: 100%;">
          <div style="width: 100%; max-width: 600px;">
            <div class="info-box w-full bg-white rad-10 p-20">

              <?php if (!empty($error)): ?>
                <div class="bg-red c-white p-10 rad-6 fs-14 txt-c mb-10"><?= $error ?></div>
              <?php elseif (!empty($success)): ?>
                <div class="bg-green c-white p-10 rad-6 fs-14 txt-c mb-10"><?= $success ?></div>
              <?php endif; ?>

              <form method="post">
                <!-- Tutor Info -->
                <div class="card-widget bg-white rad-10 p-20 mt-20">
                  <h2 class="c-orange m-0 mb-10"><i class="fa fa-chalkboard-teacher me-2"></i> Tutor Information</h2>
                  <p class="fs-14 c-black"><strong>👨‍🏫 Tutor:</strong> <?= htmlspecialchars($tutor_name) ?></p>
                  <p class="fs-14 c-black"><strong>📘 Course:</strong> <?= htmlspecialchars($course_name) ?></p>
                </div>

                <!-- Date Field -->
                <div class="card-widget bg-white rad-10 p-20 mt-20">
                  <h2 class="c-orange m-0"><i class="fa fa-calendar-alt me-2"></i> Select Date</h2>
                  <p class="c-grey fs-13 mt-5">Choose a date for your session.</p>
                  <input type="date" name="date" class="input-box mt-10 w-full" required min="<?= date('Y-m-d') ?>">
                </div>

                <!-- Time Field -->
                <div class="card-widget bg-white rad-10 p-20 mt-20">
                  <h2 class="c-orange m-0"><i class="fa fa-clock me-2"></i> Select Time</h2>
                  <p class="c-grey fs-13 mt-5">Pick a suitable time to start the session.</p>
                  <input type="time" name="time" class="input-box mt-10 w-full" required>
                </div>

                <!-- Duration Field -->
                <div class="card-widget bg-white rad-10 p-20 mt-20">
                  <h2 class="c-orange m-0"><i class="fa fa-hourglass-half me-2"></i> Duration</h2>
                  <p class="c-grey fs-13 mt-5">Enter the number of hours (e.g., 1, 1.5).</p>
                  <input type="number" name="duration" class="input-box mt-10 w-full" min="0.5" step="0.5" required>
                </div>

                <!-- Place Field -->
                <div class="card-widget bg-white rad-10 p-20 mt-20">
                  <h2 class="c-orange m-0"><i class="fa fa-map-marker-alt me-2"></i> Meeting Place</h2>
                  <p class="c-grey fs-13 mt-5">Example: "Zoom", "Library", "Google Meet"</p>
                  <input type="text" name="place" class="input-box mt-10 w-full" placeholder="e.g., Online, Library" required>
                </div>

                <!-- Submit -->
                <div class="center-flex mt-30">
                  <button type="submit" class="btn-shape bg-orange c-white fs-14 b-none px-20 py-10">
                    <i class="fa fa-paper-plane me-1"></i> Request Session
                  </button>

                </div>
              </form>

            </div>
          </div>
        </div>
      </div>
    </div>

  </body>

  </html>
<?php
} else {
  $em = "Unauthorized access.";
  header("Location: ../login.php?error=$em");
  exit;
}
?>