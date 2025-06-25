<?php
session_start();

function getTutorName($conn, $tutorid)
{
    $stmt = $conn->prepare("SELECT name FROM student_account WHERE accountid = ?");
    $stmt->execute([$tutorid]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? $row['name'] : '';
}


function getTutorReviews($conn, $tutorid)
{
    $stmt = $conn->prepare("SELECT rating, review FROM review WHERE tutorid = ?");
    $stmt->execute([$tutorid]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
if (isset($_SESSION['tutorid']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Tutor') {
        include "../../DB_connection.php";


        $tutorid = isset($_GET['tutorid']) ? $_GET['tutorid'] : null;
        if (!$tutorid) {
            echo "No tutor selected.";
            exit;
        }

        $tutor_name = getTutorName($conn, $tutorid);
        $reviews = getTutorReviews($conn, $tutorid);
?>



        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <title>Reviews about Tutor: <?= htmlspecialchars($tutor_name) ?></title>
            <meta name="viewport" content="width=device-width, initial-scale=1.0">

            <!-- Stylesheets from courseSelection -->
            <link rel="stylesheet" href="../../css/style1.css">
            <link rel="stylesheet" href="../../css/framework.css">
            <link rel="stylesheet" href="../../css/master.css">
            <link rel="icon" href="../../img/logo.png">
            <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;500&display=swap" rel="stylesheet">
        </head>

        <body class="body-home">
            <div class="page d-flex">
                <?php include_once '../inc/navbar.php'; ?> <!-- Sidebar -->

                <div class="content w-full">
                    <?php include_once '../inc/upbar.php'; ?> <!-- upbar -->
                    <!-- End Header -->

                    <h1 class="c-orange mt-20 mb-20 ml-20">📋 Reviews about You</h1>

                    <div class="courses-page d-grid m-20 gap-20">
                        <?php if (count($reviews) > 0): ?>
                            <?php foreach ($reviews as $review): ?>
                                <div class="course bg-white rad-6 p-relative shadow p-20">
                                    <h4 class="fs-15 c-green m-0"><i class="fa fa-star c-orange me-1"></i>Rating: <?= htmlspecialchars($review['rating']) ?>/5</h4>
                                    <p class="fs-14 mt-10"><?= nl2br(htmlspecialchars($review['review'])) ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="course bg-white rad-6 shadow p-20">
                                <p class="c-grey fs-14"><i class="fa fa-info-circle me-2"></i> No reviews about you yet.</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="m-20 mt-10">
                        <a href="javascript:history.back()" class="btn-shape bg-orange c-white w-fit">
                            <i class="fa fa-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>
            </div>
        </body>

        </html>
<?php
    } else {
        header("Location: ../login.php?error=Unauthorized access");
        exit;
    }
} else {
    header("Location: ../login.php?error=You are not logged in.");
    exit;
}
?>