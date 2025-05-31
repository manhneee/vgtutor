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

            $result = registerTutorApplication($conn, $studentid, $gpa, $bank_name, $bank_acc_no, $self_description);

            if ($result === true) {
                $success = "Register successfully!";
            } else {
                $error = $result;
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
    <?php include "navbar.php"; ?>

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
                        <form action="" method="post">
                            <!-- Part 1: Tutor Info -->
                            <h5 class="mb-3">Tutor Information</h5>
                            <div class="mb-3">
                                <label for="gpa" class="form-label">GPA</label>
                                <input type="text" class="form-control" id="gpa" name="gpa" required>
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