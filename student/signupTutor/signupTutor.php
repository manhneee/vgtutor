  <?php
  session_start();
  $name = $_SESSION['name'] ?? 'Not Provided';
  $id = $_SESSION['studentid'] ?? 'Not Provided';

  if (isset($_SESSION['studentid']) && isset($_SESSION['role']) && $_SESSION['role'] === 'Student') {
      include "../../DB_connection.php";
      include "../data/signupTutor.php";

      $success = $error = "";

      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
          $studentid = $_SESSION['studentid'];
          $gpa = $_POST['gpa'];
          $bank_name = $_POST['bank_name'];
          $bank_acc_no = $_POST['bank_account'];
          $self_description = $_POST['self_intro'];

          $result = registerTutorApplication($conn, $studentid, $gpa, $bank_name, $bank_acc_no, $self_description);

          $success = $result === true ? "Register successfully!" : $result;
      }
  ?>
  <!DOCTYPE html>
  <html lang="en">
  <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <link rel="stylesheet" href="../../css/style1.css" />
      <link rel="stylesheet" href="../../css/framework.css">
      <link rel="stylesheet" href="../../css/master.css">
  </head>
      <div class="page d-flex">
          <?php include_once '../inc/navbar.php'; ?> <!-- LEFT SIDEBAR -->

          <div class="content w-full">
          <?php include_once '../inc/upbar.php'; ?> <!-- upbar -->

  <h1 class=" c-orange m-20">Sign up to be a Tutor</h1>
  <div class="profile-page m-20 d-flex gap-20">
  <div style="display: flex; justify-content: center; align-items: center; min-height: calc(100vh - 60px); width: 100%; padding-top: -20px;">
    <div style="width: 100%; max-width: 600px;">
      <div class="info-box w-full bg-white rad-10 p-20">

      <?php if ($success): ?>
        <div class="bg-green c-white p-10 rad-6 fs-14 txt-c"> <?= $success ?> </div>
      <?php elseif ($error): ?>
        <div class="bg-red c-white p-10 rad-6 fs-14 txt-c"> <?= $error ?> </div>
      <?php endif; ?>

      <form action="" method="post">
        <!-- GPA Widget -->
  <div class="card-widget bg-white rad-10 d-flex space-between align-center p-20 mt-20">
    <div class="text-content">
      <h2 class="c-orange m-0">GPA</h2>
      <p class="c-grey fs-14 mt-10">Please enter your GPA accurately to reflect your academic performance.</p>
      <input type="text" name="gpa" class="input-box mt-10" required />
    </div>
    <div class="img-area">
      <img src="../../img/gpa.png" alt="GPA Icon" class="widget-img" />
    </div>
  </div>


  <!-- Self Introduction Widget -->
  <div class="card-widget bg-white rad-10 d-flex space-between align-center p-20 mt-20">
    <div class="text-content w-full" style="margin-right: 20px;">
      <h2 class="c-orange m-0">Self Introduction</h2>
      <p class="c-grey fs-14 mt-10">
        Write a short paragraph about your teaching experience, strengths, or goals.
      </p>
      <textarea
        id="self_intro"
        name="self_intro"
        rows="5"
        maxlength="1000"
        class="input-box mt-10 w-full"
        style="min-width: 100%; max-width: 600px;"
        required
      ></textarea>
      <div class="fs-13 mt-5 c-grey">
        Characters left: <span id="charsLeft">1000</span>
      </div>
    </div>

    <div class="img-area" style="flex-shrink: 0;">
      <img src="../../img/self_intro.png" alt="Intro Icon" class="widget-img" />
    </div>
  </div>

  <!-- Combined Bank Info Widget -->
  <div class="card-widget bg-white rad-10 d-flex space-between align-center p-20 mt-20">
    <div class="text-content w-full">
      <h2 class="c-orange m-0">Bank Information</h2>
      <p class="c-grey fs-14 mt-10">Enter your bank name and account number to receive payments securely.</p>

      <div class="d-flex gap-20 mt-10">
        <div class="w-full">
          <label class="fs-14 c-grey" for="bank_name">Bank Name</label>
          <input type="text" id="bank_name" name="bank_name" class="input-box mt-5" required />
        </div>
        <div class="w-full">
          <label class="fs-14 c-grey" for="bank_account">Bank Account</label>
          <input type="text" id="bank_account" name="bank_account" class="input-box mt-5" required />
        </div>
      </div>
    </div>

    <div class="img-area">
      <img src="../../img/bank.png" alt="Bank Icon" class="widget-img" />
    </div>
  </div>

        <!-- Submit Button -->
        <div class="center-flex mt-10">
          <button type="submit" class="btn-shape bg-orange c-white fs-14 b-none">Submit</button>
        </div>
      </form>
    </div>
  </div>

  </body>
  </html>
  <?php
      } else {
          echo "<div class='alert alert-danger text-center mt-5'>You are not authorized to access this page.</div>";
      }

  ?>
