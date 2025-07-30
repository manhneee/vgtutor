<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

require_once __DIR__ . '/../../DB_connection.php';
require_once '../vgtutor/config.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/vgtutor/student/data/notifications.php';


// Đổi đoạn này cho đúng session hệ thống của bạn!
if (!isset($_SESSION['studentid']) && !isset($_SESSION['tutorid'])) {
    echo json_encode(['status' => 'error', 'message' => 'You are not logged in.']);
    exit;
}


$admin_id = DEFAULT_ADMIN_ID;
$type = 'report';
// Ưu tiên lấy studentid nếu có, còn không lấy tutorid
$user_id = isset($_SESSION['studentid']) ? intval($_SESSION['studentid']) : intval($_SESSION['tutorid']);
$user_type = isset($_SESSION['studentid']) ? 'Student' : 'Tutor';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');
if ($subject === '' || $message === '') {
    echo json_encode(['status' => 'error', 'message' => 'Please enter both a title and content.']);
    exit;
}

// Gửi report (chuẩn PDO)
try {
    $sql = "INSERT INTO error_reports (user, source, subject, message, datetime) VALUES (?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$user_id, $user_type, $subject, $message]);
    $report_id = $conn->lastInsertId();
} catch (PDOException $e) {
    exit;
}

// Upload ảnh
$saved = 0;
if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'bmp'];
    // ĐẢM BẢO đường dẫn thư mục dùng đúng id report vừa tạo
    $folder_report = __DIR__ . '/../../uploads/report_images/' . $report_id . '/';
    if (!is_dir($folder_report)) {
        mkdir($folder_report, 0777, true);
    }
    $file_count = is_array($_FILES['images']['name']) ? count($_FILES['images']['name']) : 0;
    if ($file_count > 5) $file_count = 5;
    for ($i = 0; $i < $file_count; $i++) {
        $name = $_FILES['images']['name'][$i];
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) continue;
        if ($_FILES['images']['error'][$i] == 0 && is_uploaded_file($_FILES['images']['tmp_name'][$i])) {
            // Đặt tên file không trùng
            $new_name = uniqid('img_', true) . '.' . $ext;
            // Lưu file vào đúng thư mục report id
            $target = $folder_report . $new_name;
            $relative_path = 'uploads/report_images/' . $report_id . '/' . $new_name; // lưu DB để admin lấy ra
            if (move_uploaded_file($_FILES['images']['tmp_name'][$i], $target)) {
                try {
                    $stmt2 = $conn->prepare("INSERT INTO error_report_images (report_id, image_path) VALUES (?, ?)");
                    $stmt2->execute([$report_id, $relative_path]);
                    $saved++;
                } catch (PDOException $e) {
                    // log lỗi nếu muốn
                }
            }
        }
    }
}

echo json_encode([
    'status' => 'ok',
    'message' => 'You have successfully submitted your feedback!',
    'images_saved' => $saved,
    'report_id' => $report_id
]);
addNotification($conn, $user_id, $admin_id, $subject, "You have successfully submitted your concern about $subject. Please wait for the admin to process it.", $type);
exit;
