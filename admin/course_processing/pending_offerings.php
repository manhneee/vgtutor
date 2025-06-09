<?php
session_start();
if (isset($_SESSION['adminid']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Admin') {

        include "../data/pending_offerings.php";
        $offerings = getAllPendingOfferings($conn);

        // Handle permit/deny actions
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['tutorid'], $_POST['courseid'])) {
            $action = $_POST['action'];
            $tutorid = intval($_POST['tutorid']);
            $courseid = intval($_POST['courseid']);
            $grade = $_POST['grade'] ?? null;
            $price = $_POST['price'] ?? null;
            processPendingOfferingAction($conn, $action, $tutorid, $courseid, $grade, $price);
        }
        
    
?>  
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Course Offerings</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="icon" href="../../img/logo.png">
    <style>
        .btn-permit {
            background-color: #198754 !important;
            color: #fff !important;
        }
        .btn-deny {
            background-color: #b02a37 !important;
            color: #fff !important;
        }
    </style>
</head>
<body class="body-home">
    <?php include "navbar.php"; ?>

    <div class="container mt-5">
        <h2 class="mb-4">Pending Course Offerings</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Tutor Name</th>
                        <th>Course Name</th>
                        <th>Major</th>
                        <th>Grade</th>
                        <th>Price</th>
                        <th>Self Introduction</th>
                        <th>Permission</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (count($offerings) > 0): ?>
                    <?php foreach ($offerings as $i => $row): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= htmlspecialchars($row['tutor_name']) ?></td>
                            <td><?= htmlspecialchars($row['course_name']) ?></td>
                            <td><?= htmlspecialchars($row['major']) ?></td>
                            <td><?= htmlspecialchars($row['grade']) ?></td>
                            <td><?= htmlspecialchars($row['price']) ?></td>
                            <td><?= nl2br(htmlspecialchars($row['self_description'])) ?></td>
                            <td>
                            <?php
                            // Check if this entry is permitted
                            $status = getPendingOfferingStatus($conn, $row['tutorid'], $row['courseid']);
                            if ($status === 'permitted') {
                                echo '<span class="badge bg-success">Permitted</span>';
                            } 
                            elseif ($status === 'denied') {
                                echo '<span class="badge bg-danger">Denied</span>';
                            }
                            else {
                            ?>
                                <form action="" method="post" class="d-inline">
                                    <input type="hidden" name="tutorid" value="<?= htmlspecialchars($row['tutorid']) ?>">
                                    <input type="hidden" name="courseid" value="<?= htmlspecialchars($row['courseid']) ?>">
                                    <input type="hidden" name="grade" value="<?= htmlspecialchars($row['grade']) ?>">
                                    <input type="hidden" name="price" value="<?= htmlspecialchars($row['price']) ?>">
                                    <button type="submit" name="action" value="permit" class="btn btn-permit btn-sm">Permit</button>
                                </form>
                                <form action="" method="post" class="d-inline">
                                    <input type="hidden" name="tutorid" value="<?= htmlspecialchars($row['tutorid']) ?>">
                                    <input type="hidden" name="courseid" value="<?= htmlspecialchars($row['courseid']) ?>">
                                    <button type="submit" name="action" value="deny" class="btn btn-deny btn-sm">Deny</button>
                                </form>
                            <?php } ?>
                        </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">No pending offerings found.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
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