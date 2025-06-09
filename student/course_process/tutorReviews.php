<?php
session_start();
if (isset($_SESSION['studentid']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Student') {
        include "../../DB_connection.php";
        include "../data/courseSelection.php";

        // Get tutorid from URL
        $tutorid = isset($_GET['tutorid']) ? $_GET['tutorid'] : null;
        if (!$tutorid) {
            echo "No tutor selected.";
            exit;
        }

        // Fetch tutor name
        $tutor_name = getTutorName($conn, $tutorid);

        // Fetch reviews for the tutor  
        $reviews = getTutorReviews($conn, $tutorid);
        
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reviews about Tutor: <?= htmlspecialchars($tutor_name) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="icon" href="../../img/logo.png">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <style>
        .review-box {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            max-width: 600px;
        }
        .review-rating {
            font-weight: bold;
            color: #ff9800;
        }
    </style>
</head>
<body class="body-home">
    <?php include "navbar.php"; ?>
    <div class="container mt-5">
        <h2 class="mb-4">Reviews about Tutor: <?= htmlspecialchars($tutor_name) ?></h2>
        <?php if (count($reviews) > 0): ?>
            <?php foreach ($reviews as $review): ?>
                <div class="review-box">
                    <div class="review-rating">Rating: <?= htmlspecialchars($review['rating']) ?>/5</div>
                    <div class="review-text mt-2"><?= nl2br(htmlspecialchars($review['review'])) ?></div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-info">No reviews for this tutor yet.</div>
        <?php endif; ?>
        <a href="javascript:history.back()" class="btn btn-secondary mt-3">Back</a>
    </div>
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