<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$showBecomeTutor = true;
if (isset($_SESSION['studentid'])) {
    include_once $_SERVER['DOCUMENT_ROOT'] . "/vgtutor/DB_connection.php";
    if (isset($conn)) {
        $stmt = $conn->prepare("SELECT accountid FROM tutor_account WHERE accountid = ?");
        $stmt->execute([$_SESSION['studentid']]);
        if ($stmt->fetch()) {
            $showBecomeTutor = false;
        }
    } else {
        $showBecomeTutor = false;
    }
}
?>

<!-- SIDEBAR1 CSS -->
<style>
    .sidebar1 {
        position: fixed;
        top: 0;
        left: 0;
        width: 240px;
        height: 100vh;
        background: #fff;
        box-shadow: 2px 0 16px #f0e7e7;
        z-index: 1001;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .sidebar1-header {
        padding: 26px 0 12px 0;
        text-align: center;
    }

    .sidebar1-menu ul {
        padding-left: 0;
        list-style: none;
        margin: 0;
    }

    .sidebar1-menu li {
        margin-bottom: 0;
        /* Remove extra gap between items */
    }

    .sidebar1-menu a {
        display: flex;
        align-items: center;
        font-size: 15px;
        color: #ff951f;
        border-radius: 6px;
        padding: 12px 18px;
        text-decoration: none;
        font-weight: 500;
        transition: background 0.13s;
        gap: 9px;
    }

    .sidebar1-menu a:hover {
        background: #fff4e3;
        color: #ff951f;
    }

    .sidebar1-menu i {
        font-size: 18px;
        min-width: 20px;
        text-align: center;
    }

    .sidebar1-bottom {
        padding-bottom: 22px;
        padding-top: 6px;
    }

    #contactAdminBtn {
        width: calc(100% - 36px);
        margin-left: 18px;
        background: #FF8D8D;
        color: #fff;
        border: none;
        border-radius: 22px;
        font-size: 16px;
        font-weight: 600;
        box-shadow: 0 2px 8px #FF8D8D44;
        cursor: pointer;
        padding: 12px 0;
        transition: background 0.2s;
        display: block;
    }

    #contactAdminBtn:hover {
        background: #FF5757;
    }

    #sidebar1-spacer {
        width: 240px;
        min-width: 240px;
        height: 1px;
        display: block;
        float: left;
    }
</style>


<!-- Begin Sidebar1 -->
<div class="sidebar1 bg-white p-20 p-relative">
    <div>
        <div class="sidebar1-header">
            <h3 class="txt-c c-orange mt-0" style="margin-bottom: 10px;">Vgtutor</h3>
        </div>
        <div class="sidebar1-menu">
            <ul>
                <li><a class="d-flex align-center fs-14 c-orange rad-6 p-10" href="/vgtutor/student/index.php"><i class="fa-regular fa-chart-bar fa-fw"></i><span>Dashboard</span></a></li>
                <li><a class="d-flex align-center fs-14 c-orange rad-6 p-10" href="/vgtutor/student/chat_process/chat.php"><i class="fa-solid fa-message fa-fw"></i><span>Mesage</span></a></li>
                <li><a class="d-flex align-center fs-14 c-orange rad-6 p-10" href="/vgtutor/student/switch_to_tutor.php"><i class="fa-solid fa-user fa-fw"></i><span>Tutor Mode</span></a></li>
                <li><a class="d-flex align-center fs-14 c-orange rad-6 p-10" href="/vgtutor/student/course_process/courseSelection.php"><i class="fa-solid fa-book fa-fw"></i><span>Courses</span></a></li>
                <li><a class="d-flex align-center fs-14 c-orange rad-6 p-10" href="/vgtutor/student/session_process/session.php"><i class="fa-solid fa-calendar-days fa-fw"></i><span>Session</span></a></li>
                <li><a class="d-flex align-center fs-14 c-orange rad-6 p-10" href="/vgtutor/profile.php"><i class="fa-regular fa-user fa-fw"></i><span>Profile</span></a></li>
                <li><a class="d-flex align-center fs-14 c-orange rad-6 p-10" href="/vgtutor/student/signupTutor/signupTutor.php"><i class="fa-solid fa-users fa-fw"></i><span>Become a tutor</span></a></li>
                <li><a class="d-flex align-center fs-14 c-orange rad-6 p-10" href="/vgtutor/logout.php">Sign Out</a></li>
            </ul>
        </div>
    </div>
    <div class="sidebar1-bottom">
        <button id="contactAdminBtn"
            onmouseover="this.style.background='#FF5757'"
            onmouseout="this.style.background='#FF8D8D'">Contact with Admin</button>
    </div>
</div>
<!-- End Sidebar1 -->

<!-- Spacer giúp đẩy main-content sang phải -->
<div id="sidebar1-spacer"></div>

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