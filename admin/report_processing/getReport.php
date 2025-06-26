<?php
session_start();
if (!isset($_SESSION['adminid']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../../login.php?error=Unauthorized access");
    exit;
}

include "../../DB_connection.php";

// Handle toggle is_fixed
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_fixed'], $_POST['report_id'])) {
    $report_id = intval($_POST['report_id']);
    $new_value = intval($_POST['toggle_fixed']);
    $stmt = $conn->prepare("UPDATE error_reports SET is_fixed = ? WHERE id = ?");
    $stmt->execute([$new_value, $report_id]);
    header("Location: getReport.php");
    exit;
}

// Fetch all reports
$stmt = $conn->prepare("SELECT * FROM error_reports ORDER BY datetime DESC");
$stmt->execute();
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Helper to get role by user id
function getUserRole($conn, $userid) {
    $stmt = $conn->prepare("SELECT 1 FROM admin_account WHERE adminid = ?");
    $stmt->execute([$userid]);
    if ($stmt->fetch()) return "Admin";
    $stmt = $conn->prepare("SELECT 1 FROM tutor_account WHERE accountid = ?");
    $stmt->execute([$userid]);
    if ($stmt->fetch()) return "Tutor";
    $stmt = $conn->prepare("SELECT 1 FROM student_account WHERE accountid = ?");
    $stmt->execute([$userid]);
    if ($stmt->fetch()) return "Student";
    return "Guest";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Error Reports</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body class="body-home">
    <?php include "../inc/navbar.php"; ?>
    <div class="container mt-5">
        <h2 class="mb-4">Error / Contact Reports</h2>
        <?php if (empty($reports)): ?>
            <div class="alert alert-info">No reports found.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Date & Time</th>
                            <th>User</th>
                            <th>Role</th>
                            <th>Source</th>
                            <th>Subject</th>
                            <th>Message</th>
                            <th>Fixed?</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reports as $r): ?>
                        <tr>
                            <td><?= htmlspecialchars($r['datetime']) ?></td>
                            <td><?= htmlspecialchars($r['user']) ?></td>
                            <td><?= htmlspecialchars(getUserRole($conn, $r['user'])) ?></td>
                            <td><?= htmlspecialchars($r['source'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($r['subject']) ?></td>
                            <td><?= nl2br(htmlspecialchars($r['message'])) ?></td>
                            <td class="text-center">
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="report_id" value="<?= $r['id'] ?>">
                                    <input type="hidden" name="toggle_fixed" value="<?= $r['is_fixed'] ? 0 : 1 ?>">
                                    <button type="submit" class="btn btn-link p-0" title="Toggle Fixed">
                                        <?php if ($r['is_fixed']): ?>
                                            <span class="fa-regular fa-square-check text-success" style="font-size:1.5em;"></span>
                                        <?php else: ?>
                                            <span class="fa-regular fa-square text-danger" style="font-size:1.5em;"></span>
                                        <?php endif; ?>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>