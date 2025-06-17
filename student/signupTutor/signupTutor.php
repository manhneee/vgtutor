<?php
session_start();
if (isset($_SESSION['studentid']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Student') {
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
                // Check extension and MIME type separately
                if ($fileExt !== 'pdf') {
                    $error = "Only PDF files are allowed.";
                } elseif ($fileType !== 'application/pdf') {
                    $error = "File type must be PDF.";
                } elseif ($fileSize > 2 * 1024 * 1024) {
                    $error = "File size must be less than 2MB.";
                } else {
                    // Optional: check file content for PDF signature
                    $fh = fopen($fileTmpPath, 'rb');
                    $header = fread($fh, 4);
                    fclose($fh);
                    if ($header !== '%PDF') {
                        $error = "Uploaded file is not a valid PDF.";
                    } else {
                        $uploadDir = __DIR__ . '/uploads/';
                        $adminUploadDir = __DIR__ . '/../../admin/tutor_processing/uploads/';
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0777, true);
                        }
                        if (!is_dir($adminUploadDir)) {
                            mkdir($adminUploadDir, 0777, true);
                        }
                        $newFileName = 'transcript_' . $studentid . '_' . time() . '.pdf';
                        $destPath = $uploadDir . $newFileName;
                        $adminDestPath = $adminUploadDir . $newFileName;
                        if (move_uploaded_file($fileTmpPath, $destPath)) {
                            // Copy to admin directory as well
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To be a Tutor</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="icon" href="../../img/logo.png">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body class="body-home">
    <?php include "../inc/navbar.php"; ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-lg" style="border: 2px solid #000;">
                    <div class="card-header text-white text-center" style="background-color: #f47119;">
                        <h3 class="mb-0">To be a Tutor</h3>
                    </div>
                    <?php if ($success): ?>
                        <div class="alert alert-success" role="alert">
                            <?= $success ?>
                        </div>
                    <?php elseif ($error): ?>
                        <div class="alert alert-danger" role="alert">
                            <?= $error ?>
                        </div>
                    <?php endif; ?>
                    <div class="card-body">
                        <form action="" method="post" enctype="multipart/form-data">
                            <!-- Part 1: Tutor Info -->
                            <h5 class="mb-3">Tutor Information</h5>
                            <div class="mb-3">
                                <label for="gpa" class="form-label">GPA</label>
                                <input type="text" class="form-control" id="gpa" name="gpa" required>
                            </div>
                            <form action="" method="post" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="transcript" class="form-label">Upload Transcript (PDF only, max 2MB)</label>
                                <input type="file" class="form-control" id="transcript" name="transcript" accept="application/pdf" required>
                            </div>
                            <div class="mb-3">
                                <label for="self_intro" class="form-label">Self Introduction (max 1000 characters)</label>
                                <textarea class="form-control" id="self_intro" name="self_intro" rows="6" maxlength="1000" required></textarea>
                                <div class="form-text">
                                    Maximum 1000 characters. Characters left: <span id="charsLeft">1000</span>
                                </div>
                            </div>
                            <hr>
                            <!-- Part 2: Payment Info -->
                            <h5 class="mb-3">Payment Information</h5>
                            <div class="mb-3">
                                <label for="bank_name" class="form-label">Bank Name</label>
                                <input type="text" class="form-control" id="bank_name" name="bank_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="bank_account" class="form-label">Bank Account</label>
                                <input type="text" class="form-control" id="bank_account" name="bank_account" required>
                            </div>
                            <div class="d-flex justify-content-center">
                                <button type="submit" class="btn btn-primary" style="background-color: #f47119; border-color: #f47119;">Submit</button>
                            </div>
                        </form>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function(){
            $("#navLinks li:nth-child(1) a").addClass('active');
        });
    </script>
</body>
</html>
<?php
    } else {
        echo "<div class='alert alert-danger text-center mt-5'>You are not authorized to access this page.</div>";
    }
} else {
    echo "<div class='alert alert-danger text-center mt-5'>You are not logged in.</div>";
}
?>