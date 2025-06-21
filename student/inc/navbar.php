<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once $_SERVER['DOCUMENT_ROOT'] . "/vgtutor/DB_connection.php";

// XỬ LÝ ẨN THÔNG BÁO (sau khi người dùng click "x")
$user_id = $_SESSION['studentid'] ?? $_SESSION['tutorid'] ?? null;

if ($user_id && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['notif_id'])) {
    $notif_id = intval($_POST['notif_id']);
    $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?");
    $stmt->execute([$notif_id, $user_id]);
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit("OK");
}

// LẤY CÁC THÔNG BÁO CHƯA ĐỌC CHO NGƯỜI DÙNG
$user_notifications = [];
$unread_count = 0;
if ($user_id) {
    $stmt = $conn->prepare("SELECT id, title, message, created_at FROM notifications WHERE user_id = ? AND is_read = 0 ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    $user_notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $unread_count = count($user_notifications);
}



// CÁC NOTIFICATION KHÁC (become tutor, payment denied v.v.)
$showBecomeTutor = true;
$notifications = [];
if (isset($_SESSION['studentid'])) {
    if (isset($conn)) {
        // Student đã là tutor?
        $stmt = $conn->prepare("SELECT accountid FROM tutor_account WHERE accountid = ?");
        $stmt->execute([$_SESSION['studentid']]);
        if ($stmt->fetch()) $showBecomeTutor = false;

        // Thông báo đăng ký tutor bị từ chối
        $stmt = $conn->prepare("SELECT status, denied_at FROM tutor_registration WHERE studentid = ? ORDER BY denied_at DESC LIMIT 1");
        $stmt->execute([$_SESSION['studentid']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row && $row['status'] === 'denied' && $row['denied_at']) {
            $deniedAt = strtotime($row['denied_at']);
            if (time() - $deniedAt < 86400) {
                $notifications[] = [
                    'type' => 'danger',
                    'msg' => 'Your application to become a Tutor has been <strong>denied</strong>. Please register again after 3 days.'
                ];
            }
        }
        // Payment denied notification
        $stmt = $conn->prepare("
            SELECT pc.*, sa.name AS tutor_name 
            FROM payment_confirmation pc
            JOIN student_account sa ON pc.tutorid = sa.accountid
            WHERE pc.studentid = ? 
              AND pc.status = 'denied'
              AND NOT EXISTS (
                SELECT 1 FROM payment_confirmation pc2
                WHERE pc2.studentid = pc.studentid
                  AND pc2.tutorid = pc.tutorid
                  AND pc2.courseid = pc.courseid
                  AND pc2.date_and_time = pc.date_and_time
                  AND pc2.status = 'accepted'
                  AND pc2.id > pc.id
              )
            ORDER BY pc.date_and_time DESC
        ");
        $stmt->execute([$_SESSION['studentid']]);
        $paymentDenied = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($paymentDenied as $pay) {
            $notifications[] = [
                'type' => 'danger',
                'msg' => 'Your payment to tutor <strong>' . htmlspecialchars($pay['tutor_name']) . '</strong> for session on <strong>' . htmlspecialchars($pay['date_and_time']) . '</strong> has been <strong>denied</strong>. Please <a href="/vgtutor/student/chat_process/chat.php?tutorid=' . htmlspecialchars($pay['tutorid']) . '" class="alert-link">chat with tutor</a>.'
            ];
        }
    } else $showBecomeTutor = false;
}
?>


<!-- HIỂN THỊ CÁC ALERT CHO NGƯỜI DÙNG NHẬN THÔNG BÁO XỬ LÝ REPORT -->
<?php foreach ($notifications as $notif): ?>
    <div class="alert alert-<?= $notif['type'] ?> alert-dismissible fade show" role="alert" style="z-index:99999;">
        <?= $notif['msg'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endforeach; ?>


<!-- NAVBAR + BELL -->
<style>
    .notif-badge {
        position: absolute;
        top: 2px;
        right: 2px;
        background: #f33;
        color: #fff;
        font-size: 11px;
        border-radius: 50%;
        width: 18px;
        height: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #fff;
        font-weight: 600;
    }
</style>
<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="/vgtutor/student/index.php">
            <img src="/vgtutor/img/logo.png" alt="Logo" width="200">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0" id="navLinks">
                <!-- ...các nav-link dashboard/session/schedule/course/message -->
            </ul>
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <?php if ($showBecomeTutor): ?>
                    <li class="nav-item">
                        <a class="btn me-2" style="border:2px solid #f47119;color:#f47119;background:#fff;"
                            href="/vgtutor/student/signupTutor/signupTutor.php">Become a Tutor</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="btn btn-outline-primary me-2" href="/vgtutor/student/switch_to_tutor.php">Switch to Tutor Mode</a>
                    </li>
                <?php endif; ?>

                <!-- BELL NOTIFICATION DROPDOWN -->
                <li class="nav-item dropdown" style="position:relative;">
                    <a class="nav-link position-relative" href="#" id="notifBell" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-bell fa-lg"></i>
                        <?php if ($unread_count > 0): ?>
                            <span class="notif-badge"><?= $unread_count ?></span>
                        <?php endif; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notifBell" style="min-width:320px;max-width:450px;">
                        <?php if ($unread_count == 0): ?>
                            <li><span class="dropdown-item-text text-muted">Không có thông báo mới</span></li>
                        <?php else: ?>
                            <?php foreach ($user_notifications as $n): ?>
                                <li class="border-bottom" style="background:#f9fafd;">
                                    <div style="padding:10px 18px 10px 14px; position:relative;">
                                        <div style="font-weight:600; color:#205cb2;">
                                            <?= htmlspecialchars($n['title']) ?>
                                        </div>
                                        <div style="font-size:15px; margin-bottom:2px; color:#222;">
                                            <?= nl2br(htmlspecialchars($n['message'])) ?>
                                        </div>
                                        <div style="font-size:12px;color:#888"><?= date('d/m/Y H:i', strtotime($n['created_at'])) ?></div>
                                        <form method="post" class="notif-close-form" style="display:inline;position:absolute;top:8px;right:8px;">
                                            <input type="hidden" name="notif_id" value="<?= $n['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-link p-0" title="Đánh dấu đã đọc" style="color:#f33;"><i class="fa fa-times"></i></button>
                                        </form>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </li>
                <li class="nav-item"><a class="nav-link" href="/vgtutor/logout.php">Sign Out</a></li>
            </ul>
        </div>
    </div>
</nav>
<!-- Button "Liên lạc với admin" -->
<button id="contactAdminBtn" style="position:fixed;bottom:30px;right:30px;z-index:9999;padding:12px 22px;background:#4F8AFF;color:#fff;border:none;border-radius:30px;font-size:18px;box-shadow:0 2px 8px #0002;cursor:pointer;transition:background 0.2s;">Contact with admin</button>

<!-- Modal form liên lạc với admin -->
<div id="contactAdminModal" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:10000;background:rgba(0,0,0,0.35);">
    <div style="max-width:400px;margin:60px auto;background:#fff;border-radius:14px;box-shadow:0 6px 32px #0003;padding:28px 26px;position:relative;">
        <h2 style="margin-top:0;font-size:22px;font-weight:600;color:#205cb2;">Contact with admin</h2>
        <form id="contactAdminForm" enctype="multipart/form-data">
            <label>Title:</label>
            <input type="text" name="subject" required style="width:100%;margin-bottom:10px;padding:7px 8px;border-radius:5px;border:1px solid #c6d1e6;">
            <label>Content:</label>
            <textarea name="message" required style="width:100%;height:80px;margin-bottom:10px;padding:7px 8px;border-radius:5px;border:1px solid #c6d1e6;"></textarea>
            <label>Attached images (max 5):</label><br>
            <input type="file" id="imagesInput" name="images[]" accept="image/*" multiple style="margin-bottom:10px;">
            <div id="imagesPreview" style="margin-bottom:12px;"></div>
            <button type="submit" style="background:#205cb2;color:#fff;padding:8px 18px;border:none;border-radius:6px;font-size:16px;">Send</button>
            <button type="button" id="closeContactAdmin" style="margin-left:12px;padding:7px 16px;border:none;border-radius:6px;background:#e4e6eb;color:#333;font-size:15px;">Cancel</button>
            <div id="contactAdminMsg" style="margin-top:12px;font-weight:500;"></div>
        </form>
    </div>
</div>

<style>
    .notif-badge {
        position: absolute;
        top: 2px;
        right: 2px;
        background: #f33;
        color: #fff;
        font-size: 11px;
        border-radius: 50%;
        width: 18px;
        height: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #fff;
        font-weight: 600;
        z-index: 11;
    }
</style>

<script>
    // Mở modal
    document.getElementById('contactAdminBtn').onclick = function() {
        document.getElementById('contactAdminModal').style.display = 'block';
        document.getElementById('contactAdminMsg').textContent = '';
    }
    // Đóng modal
    document.getElementById('closeContactAdmin').onclick = function(e) {
        e.preventDefault();
        document.getElementById('contactAdminModal').style.display = 'none';
        document.getElementById('contactAdminForm').reset();
        document.getElementById('imagesPreview').innerHTML = '';
    }

    // Preview ảnh
    document.getElementById('imagesInput').addEventListener('change', function(e) {
        const preview = document.getElementById('imagesPreview');
        preview.innerHTML = '';
        const files = Array.from(e.target.files);
        if (files.length > 5) {
            preview.innerHTML = '<span style="color:red;">Chỉ được chọn tối đa 5 ảnh!</span>';
            e.target.value = ''; // reset input
            return;
        }
        files.forEach((file, idx) => {
            const url = URL.createObjectURL(file);
            const img = document.createElement('img');
            img.src = url;
            img.style = "max-width:65px;max-height:65px;border-radius:6px;margin:4px;border:1px solid #c6d1e6;cursor:pointer;";
            img.title = file.name;
            img.onclick = () => window.open(url);
            preview.appendChild(img);
        });
    });

    // Submit form bằng AJAX
    document.getElementById('contactAdminForm').onsubmit = function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch('/vgtutor/req/report_error/handle_ajax.php', {
                method: 'POST',
                body: formData,
                credentials: 'include' // Để gửi cookie session
            })
            .then(async res => {
                const text = await res.text();
                try {
                    const data = JSON.parse(text);
                    if (data.status === 'ok') {
                        document.getElementById('contactAdminMsg').innerHTML = "<span style='color:green'>Gửi thành công!</span>";
                        setTimeout(() => {
                            document.getElementById('contactAdminModal').style.display = 'none';
                            this.reset();
                            document.getElementById('imagesPreview').innerHTML = '';
                        }, 1200);
                    } else {
                        document.getElementById('contactAdminMsg').innerHTML = "<span style='color:red'>" + (data.message || "Có lỗi xảy ra") + "</span>";
                    }
                } catch (e) {
                    document.getElementById('contactAdminMsg').innerHTML =
                        "<span style='color:red'>Server trả về lỗi hoặc không phải JSON:<br><pre>" + text + "</pre></span>";
                }
            })
            .catch(err => {
                document.getElementById('contactAdminMsg').innerHTML = "<span style='color:red'>Có lỗi mạng (fetch): " + err + "</span>";
            });
    };

    // AJAX cho nút X trên bell (ẩn thông báo không reload)
    document.querySelectorAll('.notif-close-form').forEach(function(form) {
        form.onsubmit = function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            fetch(window.location.pathname, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            }).then(() => {
                this.closest('li').style.display = 'none';
                // Update badge
                let badge = document.querySelector('.notif-badge');
                if (badge) {
                    let n = parseInt(badge.textContent) - 1;
                    if (n <= 0) badge.remove();
                    else badge.textContent = n;
                }
            });
        }
    });
</script>