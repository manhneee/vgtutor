<?php 
session_start();
if (isset($_SESSION['studentid']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'Student') {
        include_once $_SERVER['DOCUMENT_ROOT'] . "/vgtutor/DB_connection.php";
        include __DIR__ . "/data/courseSelection.php";
        include __DIR__ . "/data/session.php";
        $sessions = getStudentSessions($conn, $_SESSION['studentid']);


        $courses = searchCourse($conn, '', '', '');



        // Check tutor application status
        $stmt = $conn->prepare("SELECT status, denied_at FROM tutor_registration WHERE studentid = ? ORDER BY denied_at DESC LIMIT 1");
        $stmt->execute([$_SESSION['studentid']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);




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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;500&display=swap" rel="stylesheet" />
</head>





<body>
    <div class="page d-flex">
        <?php include_once 'inc/navbar.php'; ?> <!-- LEFT SIDEBAR -->
        
        <div class="content w-full">
        <?php include_once 'inc/upbar.php'; ?> <!-- upbar -->


            <h1 class="p-relative c-orange">Dashboard</h1>
            <div class="wrapper d-grid gap-20">
                <!-- Start Welcome Widget -->
                <div class="welcome bg-white rad-10 txt-c-mobile block-mobile">
                    <div class="intro p-20 d-flex space-between bg-orange">
                        <div>
                            <h2 class="c-white m-0">Welcome Back,</h2>
                            <p class="c-white mt-5"><?= htmlspecialchars($_SESSION['name']) ?></p>
                            
                        </div>
                        <img class="hide-mobile" src="../img/welcome.png" alt="" />
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
        <span class="d-block fs-14 fw-bold c-orange">80</span>
        <span class="d-block c-grey fs-14 mt-10">Current join Courses</span>
    </div>
</div>

            <a href="switch_to_tutor.php" class="visit d-block fs-14 bg-orange c-white w-fit btn-shape mt-15">Swtich to Tutor Mode</a>
                </div>
                <!-- End Welcome Widget -->
    
                <!-- begin tutor Widget -->
        <div class="welcome bg-white rad-10 txt-c-mobile block-mobile">
        <!-- Intro Section -->
        <div class="intro p-20 d-flex space-between bg-orange">
            <div>
            <h2 class="m-0 c-white">Sign Up To be a Tutor</h2>
            <p class="c-white fs-14 mt-10">Join our platform to teach, inspire, and earn by sharing your knowledge.</p>
            </div>
            <img class="hide-mobile" src="../img/become_tutor.png"  />
        </div>

        <!-- Body Section -->
        <div class="body txt-c d-flex p-20 mt-20 mb-20 block-mobile">
            <div>
            <span class="d-block fs-14 fw-bold c-orange">80</span>
            <span class="d-block c-grey fs-14 mt-10">Current tutors</span>
            </div>
            <div>
            <span class="d-block fs-14 fw-bold c-orange">80</span>
            <span class="d-block c-grey fs-14 mt-10">Courses </span>
            </div>
            <div>
            <span class="d-block fs-14 fw-bold c-orange">80</span>
            <span class="d-block c-grey fs-14 mt-10">Courses</span>
            </div>
        </div>
        <a href="/vgtutor/student/signupTutor/signupTutor.php" class="visit d-block fs-14 bg-orange c-white w-fit btn-shape">Become a tutor</a>
        </div>

                <!-- End tuttor Widget -->

<!-- Start Available Courses Widget -->
<div class="latest-news p-20 bg-white rad-10 txt-c-mobile">
  <h2 class="mt-0 c-orange mb-20">Available Courses</h2>

  <?php if (!empty($courses)): ?>
    <?php $count = 0; ?>
    <?php foreach ($courses as $course): ?>
      <?php if ($count >= 3) break; ?>
      <div class="news-row d-flex align-center mb-15">
        <img src="../img/news-04.png" alt="Course Image" style="width: 60px; height: 60px; border-radius: 6px; object-fit: cover;">
        <div class="info">
          <h3 class="fs-16 c-orange mb-5"><?= htmlspecialchars($course['course_name']) ?></h3>
          <p class="fs-13 c-grey">Major: <?= htmlspecialchars($course['major']) ?> | ID: <?= htmlspecialchars($course['courseid']) ?></p>
        </div>
      </div>
      <?php $count++; ?>
    <?php endforeach; ?>
  <?php else: ?>
    <p class="c-grey">No available courses at the moment.</p>
  <?php endif; ?>

  <a href="/vgtutor/student/course_process/courseSelection.php" class="visit d-block fs-14 bg-orange c-white w-fit btn-shape mt-15">Available Courses</a>
</div>
<!-- End Available Courses Widget -->

<!-- Start Upcoming Sessions Widget -->
<div class="latest-news p-20 bg-white rad-10 txt-c-mobile mt-20">
  <h2 class="mt-0 c-orange mb-20">Your Registered Tutors</h2>

  <?php
    $count = 0;
    if (!empty($sessions)) :
      foreach ($sessions as $session) :
        if ($count >= 2) break;

        // Remove date check to ensure content appears
        if (in_array(strtolower($session['consensus']), ['accepted', 'pending'])) :
  ?>
        <div class="news-row d-flex align-center mb-15">
          <img src="../img/avatar.png" alt="Tutor Avatar" style="width: 60px; height: 60px; border-radius: 6px; object-fit: cover;">
          <div class="info">
            <h3 class="fs-16 c-orange mb-5"><?= htmlspecialchars($session['course_name']) ?></h3>
            <p class="fs-13 c-grey">
              Tutor: <?= htmlspecialchars($session['tutor_name']) ?><br>
              <?= htmlspecialchars($session['date_and_time']) ?>
            </p>
          </div>
        </div>
  <?php
          $count++;
        endif;
      endforeach;
    else :
  ?>
    <p class="c-grey">No upcoming sessions scheduled.</p>
  <?php endif; ?>

  <a href="/vgtutor/student/session_process/session.php" class="visit d-block fs-14 bg-orange c-white w-fit btn-shape mt-15">View All Sessions</a>
</div>


   <!-- Start Rate tutor Widget -->
<div class="welcome bg-white rad-10 txt-c-mobile block-mobile">
  
  <!-- Intro Section -->
  <div class="intro p-20 d-flex space-between bg-orange">
    <div>
      <h2 class="m-0 c-white">Enjoy? Rate our Tutors</h2>
      <p class="c-white fs-14 mt-10">
        Rate our tutor to share your experience and help improve future lessons.
      </p>
    </div>
    <img class="hide-mobile" src="../img/rateTutor.png" />
  </div>

  <!-- Body Section -->
<div class="body txt-c d-flex block-mobile" style="padding: 0;"></div>
<a href="/vgtutor/student/course_process/tutorReviews.php"
   class="visit d-block fs-14 bg-orange c-white w-fit btn-shape"
   style="margin-top: 3rem;">
   ⭐ Rate Now
</a>


</div>
<!-- End rate tutor Widget -->

<!-- Start Reminders Widget -->
<div class="reminders p-20 bg-white rad-10 p-relative">
  <h2 class="mt-0 c-orange mb-25">Reminders</h2>

  <!-- Input Form -->
  <div class="add-reminder mb-20 d-flex align-center gap-10">
    <input id="reminder-text" type="text" placeholder="Reminder title" class="p-5 rad-6 fs-14" />
    <input id="reminder-date" type="datetime-local" class="p-5 rad-6 fs-14" />
    <select id="reminder-color" class="p-5 rad-6 fs-14">
      <option value="blue">Blue</option>
      <option value="green">Green</option>
      <option value="orange">Orange</option>
      <option value="red">Red</option>
    </select>
    <button onclick="addReminder()" class="btn bg-orange c-white fs-14 rad-6">Add</button>
  </div>

  <ul id="reminder-list" class="m-0">
    <!-- Dynamically added reminders will appear here -->
  </ul>
</div>
<!-- End Reminders Widget -->
                <!-- begin add reminder func Widget -->

<script>

  // Reminder Function
  document.getElementById("reminder-color").value = "blue"; // Default
  document.querySelector(".btn.bg-orange").addEventListener("click", function () {
    const text = document.getElementById("reminder-text").value;
    const date = document.getElementById("reminder-date").value;
    const color = document.getElementById("reminder-color").value;

    if (!text || !date) {
      alert("Please fill in all fields.");
      return;
    }

    const list = document.getElementById("reminder-list");

    const li = document.createElement("li");
    li.className = "d-flex align-center mt-15";

    const dot = document.createElement("span");
    dot.className = `key bg-${color} mr-15 d-block rad-half`;

    const content = document.createElement("div");
    content.className = `pl-15 ${color}`;
    content.innerHTML = `
      <p class="fs-14 fw-bold mt-0 mb-5">${text}</p>
      <span class="fs-13 c-grey">${new Date(date).toLocaleString()}</span>
    `;

    li.appendChild(dot);
    li.appendChild(content);
    list.appendChild(li);

    document.getElementById("reminder-text").value = "";
    document.getElementById("reminder-date").value = "";
    document.getElementById("reminder-color").value = "blue";
  });

</script>


                <!-- end add reminder func Widget -->

 
                

               
</body>

</html>
<?php
    } else {
        $em = "You are not authorized to access this page.";
        header("Location: ../login.php?error=$em");
        exit;
    } 
} else {
    $em = "You are not logged in.";
    header("Location: ../login.php?error=$em");
    exit;
}
?>