<?php
if (!isset($notifications)) $notifications = [];
if (isset($_POST['submit_error'])) {
    $subject = trim($_POST['error_subject']);
    $message = trim($_POST['error_message']);
    $user = isset($_SESSION['studentid']) ? $_SESSION['studentid'] : (isset($_SESSION['tutorid']) ? $_SESSION['tutorid'] : 'Guest');
    $source = 'tutor';
    // Save to database (match your table columns)
    $stmt = $conn->prepare("INSERT INTO error_reports (datetime, user, subject, message, source) VALUES (NOW(), ?, ?, ?, ?)");
    $stmt->execute([$user, $subject, $message, $source]);
    // Optional: Show a success message (JS alert)
    echo "<script>alert('Your message has been sent to the admin.');</script>";
}
// Payment confirmation notification (pending payments)
$stmt = $conn->prepare("
    SELECT pc.*, sa.name AS student_name, c.course_name
    FROM payment_confirmation pc
    JOIN student_account sa ON pc.studentid = sa.accountid
    JOIN course c ON pc.courseid = c.courseid
    WHERE pc.tutorid = ? AND pc.status = 'pending'
    ORDER BY pc.date_and_time DESC
");

$stmt->execute([$_SESSION['tutorid']]);
$pendingPayments = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($pendingPayments as $pay) {
    $notifications[] = [
        'type' => 'info',
        'msg' => "Payment confirmation from <strong>" . htmlspecialchars($pay['student_name']) . "</strong> for <strong>" . htmlspecialchars($pay['course_name']) . "</strong> is pending. <a href='/vgtutor/tutor/session_process/session.php' class='alert-link'>Check now</a>."
    ];
}

?>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="/vgtutor/tutor/index.php">
            <img src="/vgtutor/img/logo.png" alt="Logo" width="200" height="" class="d-inline-block align-text-top">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0" id="navLinks">
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="/vgtutor/tutor/index.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/vgtutor/tutor/session_process/session.php">Sessions</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Schedule</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/vgtutor/tutor/course_process/course.php">Course</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/vgtutor/tutor/chat_process/chat.php">Message</a>
                </li>
            </ul>

            <ul class="navbar-nav me-right mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="btn btn-outline-primary me-2" href="/vgtutor/tutor/switch_to_student.php">Switch to Student Mode</a>
                </li>



                <form method="post" id="notifSeenForm" style="display:none;">
                    <input type="hidden" name="notif_seen" value="1">
                </form>

                <li class="nav-item dropdown">
                    <a class="btn btn me-2 dropdown-toggle position-relative" href="#" id="notifDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-bell"></i>
                        <?php if (count($notifications) > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <?= count($notifications) ?>
                            </span>
                        <?php endif; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notifDropdown" style="min-width: 350px;">
                        <?php foreach ($notifications as $notif): ?>
                            <li>
                                <div class="alert alert-<?= $notif['type'] ?> mb-1 py-2 px-3" style="font-size: 0.95em;">
                                    <?= $notif['msg'] ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/vgtutor/logout.php">Sign Out</a>
                </li>
            </ul>
        </div>
    </div>
    <!-- Report Error / Contact Admin Floating Button
    <button type="button" id="contactAdminBtn" class="btn btn-danger rounded-circle"
            style="position: fixed; bottom: 30px; left: 30px; z-index: 1050; width:60px; height:60px; box-shadow: 0 2px 8px rgba(0,0,0,0.2);"
            data-bs-toggle="modal" data-bs-target="#contactAdminModal" title="Report Error / Contact Admin">
        <i class="fa fa-exclamation-triangle"></i>
    </button>

    Contact Admin Modal -->
    <!-- <div class="modal fade" id="contactAdminModal" tabindex="-1" aria-labelledby="contactAdminModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="post" action="" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="contactAdminModalLabel">Report Error / Contact Admin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                <label for="error_subject" class="form-label">Subject</label>
                <input type="text" class="form-control" id="error_subject" name="error_subject" required>
                </div>
                <div class="mb-3">
                <label for="error_message" class="form-label">Message</label>
                <textarea class="form-control" id="error_message" name="error_message" rows="5" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" name="submit_error" class="btn btn-primary">Send</button>
            </div>
            </form>
        </div>
    </div> -->
    <!-- Nút Liên lạc với admin -->
    <button id="contactAdminBtn" style="position:fixed;bottom:30px;right:30px;z-index:9999;padding:12px 22px;background:#4F8AFF;color:#fff;border:none;border-radius:30px;font-size:18px;box-shadow:0 2px 8px #0002;cursor:pointer;transition:background 0.2s;">Liên lạc với admin</button>

    <!-- Modal form liên lạc với admin -->
    <div id="contactAdminModal" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:10000;background:rgba(0,0,0,0.35);">
        <div style="max-width:400px;margin:60px auto;background:#fff;border-radius:14px;box-shadow:0 6px 32px #0003;padding:28px 26px;position:relative;">
            <h2 style="margin-top:0;font-size:22px;font-weight:600;color:#205cb2;">Liên lạc với admin</h2>
            <form id="contactAdminForm" enctype="multipart/form-data">
                <label>Tiêu đề:</label>
                <input type="text" name="subject" required style="width:100%;margin-bottom:10px;padding:7px 8px;border-radius:5px;border:1px solid #c6d1e6;">
                <label>Nội dung:</label>
                <textarea name="message" required style="width:100%;height:80px;margin-bottom:10px;padding:7px 8px;border-radius:5px;border:1px solid #c6d1e6;"></textarea>
                <label>Ảnh đính kèm (tối đa 5):</label><br>
                <input type="file" id="imagesInput" name="images[]" accept="image/*" multiple style="margin-bottom:10px;">
                <div id="imagesPreview" style="margin-bottom:12px;"></div>
                <button type="submit" style="background:#205cb2;color:#fff;padding:8px 18px;border:none;border-radius:6px;font-size:16px;">Gửi</button>
                <button type="button" id="closeContactAdmin" style="margin-left:12px;padding:7px 16px;border:none;border-radius:6px;background:#e4e6eb;color:#333;font-size:15px;">Hủy</button>
                <div id="contactAdminMsg" style="margin-top:12px;font-weight:500;"></div>
            </form>
        </div>
    </div>
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
                    credentials: 'include' // để gửi cookie session
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
    </script>


</nav>