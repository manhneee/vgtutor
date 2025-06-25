<?php
session_start();
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

    // Handle transcript upload
    $transcript_path = null;
    if (isset($_FILES['transcript']) && $_FILES['transcript']['error'] === UPLOAD_ERR_OK) {
      $fileTmpPath = $_FILES['transcript']['tmp_name'];
      $fileName = $_FILES['transcript']['name'];
      $fileSize = $_FILES['transcript']['size'];
      $fileType = $_FILES['transcript']['type'];
      $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

      $allowedExt = ['pdf'];
      if ($fileExt !== 'pdf') {
        $error = "Only PDF files are allowed.";
      } elseif ($fileType !== 'application/pdf') {
        $error = "File type must be PDF.";
      } elseif ($fileSize > 2 * 1024 * 1024) {
        $error = "File size must be less than 2MB.";
      } else {
        $fh = fopen($fileTmpPath, 'rb');
        $header = fread($fh, 4);
        fclose($fh);
        if ($header !== '%PDF') {
          $error = "Uploaded file is not a valid PDF.";
        } else {
          $uploadDir = __DIR__ . '/uploads/';
          $adminUploadDir = __DIR__ . '/../../admin/tutor_processing/uploads/';
          if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
          if (!is_dir($adminUploadDir)) mkdir($adminUploadDir, 0777, true);
          $newFileName = 'transcript_' . $studentid . '_' . time() . '.pdf';
          $destPath = $uploadDir . $newFileName;
          $adminDestPath = $adminUploadDir . $newFileName;
          if (move_uploaded_file($fileTmpPath, $destPath)) {
            copy($destPath, $adminDestPath);
            $transcript_path = 'uploads/' . $newFileName;
          } else {
            $error = "Failed to upload file.";
          }
        }
      }
    } else {
      $error = "Transcript file is required.";
    }

    if (empty($error)) {
      $result = registerTutorApplication($conn, $studentid, $gpa, $bank_name, $bank_acc_no, $self_description, $transcript_path);
      if ($result === true) {
        $success = "Register successfully!";
      } else {
        $error = $result;
      }
    }
  }
?>
  <!DOCTYPE html>
  <html lang="en">

  <head>
    <meta charset="UTF-8">
    <title>Sign Up to be a Tutor</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- CSS đẹp như bản widget -->
    <link rel="stylesheet" href="../../css/style1.css" />
    <link rel="stylesheet" href="../../css/framework.css">
    <link rel="stylesheet" href="../../css/master.css">
    <style>
      .widget-img {
        width: 110px;
      }

      .form-label {
        font-weight: bold;
      }

      .alert {
        margin-top: 10px;
      }

      .mb-30 {
        margin-bottom: 30px;
      }

      .mt-30 {
        margin-top: 30px;
      }

      @media (max-width: 700px) {
        .card-widget {
          flex-direction: column !important;
        }

        .img-area {
          margin-top: 15px;
        }
      }
    </style>
  </head>

  <body class="body-home">
    <div class="page d-flex">
      <?php include_once '../inc/navbar.php'; ?>
      <div class="content w-full">
        <?php include_once '../inc/upbar.php'; ?>
        <h1 class="c-orange m-20">Sign up to be a Tutor</h1>
        <div class="profile-page m-20 d-flex gap-20">
          <div style="width:100%; max-width: 700px; margin:auto;">
            <div class="info-box w-full bg-white rad-10 p-20">
              <?php if ($success): ?>
                <div class="bg-green c-white p-10 rad-6 fs-14 txt-c mb-10"><?= $success ?></div>
              <?php elseif ($error): ?>
                <div class="bg-red c-white p-10 rad-6 fs-14 txt-c mb-10"><?= $error ?></div>
              <?php endif; ?>
              <!-- Chỉ có 1 form -->
              <form action="" method="post" enctype="multipart/form-data">
                <!-- GPA Widget -->
                <div class="card-widget bg-white rad-10 d-flex space-between align-center p-20 mt-20">
                  <div class="text-content w-full">
                    <h2 class="c-orange m-0">GPA</h2>
                    <p class="c-grey fs-14 mt-10">Please enter your GPA accurately to reflect your academic performance.</p>
                    <input type="text" name="gpa" class="input-box mt-10 w-full" required />
                  </div>
                  <div class="img-area">
                    <img src="../../img/gpa.png" alt="GPA Icon" class="widget-img" />
                  </div>
                </div>
                <!-- Transcript Upload Widget (nằm độc lập, KHÔNG lồng thêm form, KHÔNG nằm trong mb-3 của Bootstrap) -->
                <div class="card-widget bg-white rad-10 d-flex space-between align-center p-20 mt-20">
                  <div class="text-content w-full">
                    <h2 class="c-orange m-0">Transcript (PDF)</h2>
                    <p class="c-grey fs-14 mt-10">
                      Please upload your transcript (PDF only, max 2MB).
                    </p>
                    <input type="file" name="transcript" class="input-box mt-10 w-full" accept="application/pdf" required>
                  </div>
                  <div class="img-area">
                    <img src="../../img/pdf_icon.jpg" alt="Transcript PDF" class="widget-img" />
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
                      required></textarea>
                    <div class="fs-13 mt-5 c-grey">
                      Characters left: <span id="charsLeft">1000</span>
                    </div>
                  </div>
                  <div class="img-area" style="flex-shrink: 0;">
                    <img src="../../img/self_intro.png" alt="Intro Icon" class="widget-img" />
                  </div>
                </div>
                <!-- Bank Info Widget -->
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
                <div class="center-flex mt-30">
                  <button type="submit" class="btn-shape bg-orange c-white fs-14 b-none px-20 py-10">Submit</button>
                </div>
              </form>
              <!-- End form -->
            </div>
          </div>
        </div>
      </div>
    </div>
    <script>
      // Character count limit (1000 characters)
      const textarea = document.getElementById('self_intro');
      const charsLeft = document.getElementById('charsLeft');
      textarea.addEventListener('input', function() {
        const left = 1000 - this.value.length;
        charsLeft.textContent = left >= 0 ? left : 0;
        if (this.value.length > 1000) {
          this.value = this.value.substring(0, 1000);
        }
      });
    </script>
  </body>

  </html>
<?php
} else {
  echo "<div class='alert alert-danger text-center mt-5'>You are not authorized to access this page.</div>";
}
?>