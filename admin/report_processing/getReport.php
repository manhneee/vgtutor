<?php
session_start();
if (!isset($_SESSION['adminid']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../../login.php?error=Unauthorized access");
    exit;
}
include "../../DB_connection.php";
include_once dirname(__DIR__, 2) . '/student/data/notifications.php';

// Xử lý đổi status và gửi thông báo khi admin chuyển sang "done"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'], $_POST['report_id'])) {
    $report_id = intval($_POST['report_id']);
    $new_status = $_POST['update_status'];
    $valid_status = ['done', 'not yet', 'in progress'];

    if (in_array($new_status, $valid_status)) {
        // Cập nhật trạng thái
        $stmt = $conn->prepare("UPDATE error_reports SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $report_id]);

        // Nếu chuyển sang "done" thì gửi thông báo cho user gửi report (nếu chưa gửi)
        if ($new_status === 'done') {
            // Lấy user_id_receive (người gửi report)
            $getUserStmt = $conn->prepare("SELECT user FROM error_reports WHERE id = ?");
            $getUserStmt->execute([$report_id]);
            $user_id_receive = $getUserStmt->fetchColumn();

            // Lấy subject/report title
            $getTitle = $conn->prepare("SELECT subject FROM error_reports WHERE id = ?");
            $getTitle->execute([$report_id]);
            $subject = $getTitle->fetchColumn();

            if ($user_id_receive) {
                // Admin gửi
                $user_id_send = $_SESSION['adminid'];
                $type = 'report';
                $title = $subject ?: 'Report';
                $message = "Your concern regarding \"$title\" has been addressed by the admin.";

                // Gửi thông báo với các trường mới
                addNotification($conn, $user_id_receive, $user_id_send, $title, $message, $type);
            }
        }
    }

    header("Location: getReport.php" . (isset($_GET['filter']) ? '?filter=' . urlencode($_GET['filter']) : ''));
    exit;
}

// Thống kê số lượng từng trạng thái
$statusCounts = [];
foreach (['not yet', 'in progress', 'done'] as $st) {
    $stt = $conn->prepare("SELECT COUNT(*) FROM error_reports WHERE status = ?");
    $stt->execute([$st]);
    $statusCounts[$st] = $stt->fetchColumn();
}

// Filter by status nếu có
$filter = $_GET['filter'] ?? '';
if (in_array($filter, ['not yet', 'in progress', 'done'])) {
    $stmt = $conn->prepare("SELECT * FROM error_reports WHERE status = ? ORDER BY datetime DESC");
    $stmt->execute([$filter]);
} else {
    $stmt = $conn->prepare("SELECT * FROM error_reports ORDER BY datetime DESC");
    $stmt->execute();
}
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Hàm lấy vai trò từ user id
function getUserRole($conn, $userid)
{
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

// Hàm lấy ảnh report
function getReportImages($conn, $report_id)
{
    $stmt = $conn->prepare("SELECT image_path FROM error_report_images WHERE report_id = ?");
    $stmt->execute([$report_id]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
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
    <style>
        td,
        th {
            vertical-align: middle !important;
        }

        .status-drop {
            min-width: 110px;
        }

        .img-thumb {
            max-width: 75px;
            max-height: 75px;
            border-radius: 6px;
            margin: 3px;
            border: 1px solid #bbb;
        }
    </style>
</head>

<body class="body-home">
    <?php include "../inc/navbar.php"; ?>
    <div class="container mt-5">
        <h2 class="mb-4">Error / Contact Reports</h2>
        <div class="mb-3">
            <b>Status statistics:</b>
            <span class="badge bg-secondary">Not yet: <?= $statusCounts['not yet'] ?></span>
            <span class="badge bg-warning text-dark">In progress: <?= $statusCounts['in progress'] ?></span>
            <span class="badge bg-success">Done: <?= $statusCounts['done'] ?></span>
        </div>
        <form method="get" class="mb-4">
            <label for="filter">Filter by status:</label>
            <select name="filter" id="filter" onchange="this.form.submit()" style="min-width:130px;">
                <option value="">All</option>
                <option value="not yet" <?= $filter == 'not yet' ? 'selected' : '' ?>>Not yet</option>
                <option value="in progress" <?= $filter == 'in progress' ? 'selected' : '' ?>>In progress</option>
                <option value="done" <?= $filter == 'done' ? 'selected' : '' ?>>Done</option>
            </select>
        </form>
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
                            <th>Status</th>
                            <th>Images</th>
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
                                <td>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="report_id" value="<?= $r['id'] ?>">
                                        <select name="update_status" class="status-drop" onchange="this.form.submit()">
                                            <option value="not yet" <?= $r['status'] == 'not yet' ? 'selected' : '' ?>>Not yet</option>
                                            <option value="in progress" <?= $r['status'] == 'in progress' ? 'selected' : '' ?>>In progress</option>
                                            <option value="done" <?= $r['status'] == 'done' ? 'selected' : '' ?>>Done</option>
                                        </select>
                                    </form>
                                </td>
                                <td>
                                    <?php foreach (getReportImages($conn, $r['id']) as $img): ?>
                                        <a href="../../<?= htmlspecialchars($img) ?>" target="_blank">
                                            <img src="../../<?= htmlspecialchars($img) ?>" class="img-thumb" alt="report image">
                                        </a>
                                    <?php endforeach; ?>
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