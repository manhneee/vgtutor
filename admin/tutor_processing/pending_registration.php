<?php
session_start();
if (isset($_SESSION['adminid']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Admin') {

        include "../../DB_connection.php";
        include "../data/pending_tutor_registration.php";
        include_once dirname(__DIR__, 2) . '/student/data/notifications.php';
        $pending_tutors = getAllPendingTutorRegistrations($conn);

        // Handle permit/deny actions
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['tutorid'])) {
            $action = $_POST['action'];
            $tutorid = intval($_POST['tutorid']);
            processPendingTutorAction($conn, $action, $tutorid);
            if ($action === 'permit') {
                addNotification(
                    $conn,
                    $tutorid,                      // user_id_receive
                    $_SESSION['adminid'],          // user_id_send
                    "Successful Tutor Registration",          // title
                    "Your request to become a Tutor has been approved by admin.", // message
                    "Registration"           // type
                );
            } elseif ($action === 'deny') {
                addNotification(
                    $conn,
                    $tutorid,                      // user_id_receive
                    $_SESSION['adminid'],          // user_id_send
                    "Failed Tutor Registration",          // title
                    "Your request to become a Tutor has been denied by admin.", // message
                    "Registration"           // type
                );
            }
            // Refresh data after action
            $pending_tutors = getAllPendingTutorRegistrations($conn);
        }


?>
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Pending Tutor Registrations</title>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css">
            <link rel="stylesheet" href="../../css/style.css">
            <link rel="icon" href="../../img/logo.png">
        </head>

        <body class="body-home">
            <?php include "../inc/navbar.php"; ?>
            <div class="container mt-5">
                <h2 class="mb-4">Pending Tutor Registrations</h2>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Major</th>
                                <th>GPA</th>
                                <th>Transcript</th>
                                <th>Self Description</th>
                                <th>Status/Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($pending_tutors && count($pending_tutors) > 0): ?>
                                <?php foreach ($pending_tutors as $i => $row): ?>
                                    <tr>
                                        <td><?= $i + 1 ?></td>
                                        <td><?= htmlspecialchars($row['name']) ?></td>
                                        <td><?= htmlspecialchars($row['major']) ?></td>
                                        <td><?= htmlspecialchars($row['gpa']) ?></td>
                                        <td>
                                            <?php if (!empty($row['transcript_path'])): ?>
                                                <a href="<?php echo htmlspecialchars($row['transcript_path']); ?>" target="_blank" class="btn btn-sm btn-primary">
                                                    Download Transcript
                                                </a>
                                            <?php else: ?>
                                                No file
                                            <?php endif; ?>
                                        </td>
                                        <td><?= nl2br(htmlspecialchars($row['self_description'])) ?></td>
                                        <td>
                                            <?php
                                            $status = getPendingTutorStatus($conn, $row['studentid']);
                                            if ($status === 'permitted') {
                                                echo '<span class="badge bg-success">Permitted</span>';
                                                addNotification($conn, $row['studentid'], "Registration to become Tutor Approved", "Your request to become a Tutor has been approved.");
                                            } elseif ($status === 'denied') {
                                                echo '<span class="badge bg-danger">Denied</span>';
                                                addNotification($conn, $row['studentid'], "Registration to become Tutor Denied", "Your request to become a Tutor has been denied.");
                                            } else {
                                            ?>
                                                <form action="" method="post" class="d-inline">
                                                    <input type="hidden" name="tutorid" value="<?= htmlspecialchars($row['studentid']) ?>">
                                                    <button type="submit" name="action" value="permit" class="btn btn-success btn-sm">Permit</button>
                                                </form>
                                                <form action="" method="post" class="d-inline">
                                                    <input type="hidden" name="tutorid" value="<?= htmlspecialchars($row['studentid']) ?>">
                                                    <button type="submit" name="action" value="deny" class="btn btn-danger btn-sm">Deny</button>
                                                </form>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">No pending tutor registrations.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
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