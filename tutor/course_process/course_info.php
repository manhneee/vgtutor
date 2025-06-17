<?php
session_start();
if (isset($_SESSION['tutorid']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Tutor') {
        include "../../DB_connection.php";

        // Get courseid from URL
        $courseid = isset($_GET['courseid']) ? $_GET['courseid'] : null;
        $cond = null;

        if ($courseid) {
            // Fetch only the 'cond' column for this course
            $stmt = $conn->prepare("SELECT cond FROM course WHERE courseid = ?");
            $stmt->execute([$courseid]);
            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $cond = $row['cond'];
            }
        }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Condition</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="icon" href="../../img/logo.png">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body class="body-home">
    <?php include "../inc/navbar.php"; ?>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Course Condition</h2>
            <a href="offering.php?courseid=<?= urlencode($courseid) ?>" class="btn bg-orange text-white">
                <i class="fa fa-plus"></i> Offering
            </a>
        </div>
        <?php if ($cond !== null): ?>
            <div class="card shadow-sm">
                <div class="card-body">
                    <p class="card-text"><strong>Condition:</strong></p>
                    <p class="card-text"><?= nl2br(htmlspecialchars($cond)) ?></p>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">Course condition not found.</div>
        <?php endif; ?>
    </div>
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