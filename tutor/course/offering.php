<?php
session_start();
if (isset($_SESSION['tutorid']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Tutor') {
    $tutorid = $_SESSION['tutorid'];
    $name = $_SESSION['name'] ?? 'Not Provided';
    $courseid = $_GET['courseid'] ?? ($_POST['courseid'] ?? '');

    include "../data/course_offering.php";

    $success = $error = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $grade = trim($_POST['grade'] ?? '');
        $price = intval($_POST['price'] ?? 0);
        $self_description = trim($_POST['self_intro'] ?? '');

        if (strlen($self_description) > 1000) {
            $self_description = substr($self_description, 0, 1000);
        }

        $result = insertPendingOffering($tutorid, $courseid, $grade, $price, $self_description);

        $success = $result === true ? "Offer submitted and pending approval." : "Could not submit offer: " . htmlspecialchars($result);
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>  
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offer Course</title>
    <link rel="stylesheet" href="../../css/style1.css" />
    <link rel="stylesheet" href="../../css/framework.css">
    <link rel="stylesheet" href="../../css/master.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body class="bg-eee">
    <div class="page d-flex">
    <?php include_once '../inc/navbar.php'; ?> <!-- Sidebar -->

    <div class="content w-full">
      <?php include_once '../inc/upbar.php'; ?> <!-- Top Bar -->

            <h1 class="c-orange m-20">Offer a Course</h1>
            <div class="profile-page m-20 d-flex gap-20">
                <div style="display: flex; justify-content: center; align-items: center; min-height: calc(100vh - 60px); width: 100%;">
                    <div style="width: 100%; max-width: 600px;">
                        <div class="info-box w-full bg-white rad-10 p-20">

                            <?php if ($success): ?>
                                <div class="bg-green c-white p-10 rad-6 fs-14 txt-c"><?= $success ?></div>
                            <?php elseif ($error): ?>
                                <div class="bg-red c-white p-10 rad-6 fs-14 txt-c"><?= $error ?></div>
                            <?php endif; ?>

                            <form action="" method="post">
                                <input type="hidden" name="courseid" value="<?= htmlspecialchars($courseid) ?>">

                                <!-- Grade Widget -->
                                <div class="card-widget bg-white rad-10 d-flex space-between align-center p-20 mt-20">
                                    <div class="text-content w-full">
                                        <h2 class="c-orange m-0"> Grade</h2>
                                        <p class="c-grey fs-14 mt-10">Your grade of this course.</p>
                                        <input type="text" name="grade" class="input-box mt-10" required>
                                    </div>
                                    <div class="img-area">
                                        <img src="../../img/gpa.png" alt="Grade Icon" class="widget-img">
                                    </div>
                                </div>

                                <!-- Price Widget -->
                                <div class="card-widget bg-white rad-10 d-flex space-between align-center p-20 mt-20">
                                    <div class="text-content w-full">
                                        <h2 class="c-orange m-0">Price</h2>
                                        <p class="c-grey fs-14 mt-10">Specify the price you charge per hour for this course.</p>
                                        <input type="number" name="price" class="input-box mt-10" min="0" required>
                                    </div>
                                    <div class="img-area">
                                        <img src="../../img/price.png" alt="Price Icon" class="widget-img">
                                    </div>
                                </div>

                                <!-- Self Introduction Widget -->
                                <div class="card-widget bg-white rad-10 d-flex space-between align-center p-20 mt-20">
                                    <div class="text-content w-full" style="margin-right: 20px;">
                                        <h2 class="c-orange m-0">Self Introduction</h2>
                                        <p class="c-grey fs-14 mt-10">Describe your experience, skills, or motivation for offering this course.</p>
                                        <textarea id="self_intro" name="self_intro" rows="6" maxlength="1000" class="input-box mt-10 w-full" required></textarea>
                                        <div class="fs-13 mt-5 c-grey">Characters left: <span id="charsLeft">1000</span></div>
                                    </div>
                                    <div class="img-area" style="flex-shrink: 0;">
                                        <img src="../../img/self_intro.png" alt="Intro Icon" class="widget-img">
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="center-flex mt-10">
                                    <button type="submit" class="btn-shape bg-orange c-white fs-14 b-none">Submit Offer</button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>

        </div> <!-- End content -->
    </div> <!-- End page -->

<script>
    const textarea = document.getElementById('self_intro');
    const charsLeft = document.getElementById('charsLeft');
    textarea.addEventListener('input', function() {
        const remaining = 1000 - this.value.length;
        charsLeft.textContent = remaining >= 0 ? remaining : 0;
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
