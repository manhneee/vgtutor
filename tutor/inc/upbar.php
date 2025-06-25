<?php
if (session_status() === PHP_SESSION_NONE) session_start();

include_once $_SERVER['DOCUMENT_ROOT'] . "/vgtutor/DB_connection.php";

// Lấy thông tin user
$user_id_receive = $_SESSION['studentid'] ?? ($_SESSION['tutorid'] ?? null);
$name = $_SESSION['name'] ?? '';
$student_id = $_SESSION['studentid'] ?? '';

// Lấy 10 notification mới nhất (chưa đọc ưu tiên lên đầu)
$notifications = [];
$unread_count = 0;
if ($user_id_receive) {
  $stmt = $conn->prepare("SELECT id, title, message, is_read, created_at
                            FROM notifications
                            WHERE user_id_receive = ?
                            ORDER BY is_read ASC, created_at DESC
                            LIMIT 10");
  $stmt->execute([$user_id_receive]);
  $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // Đếm số chưa đọc
  $unread_count = 0;
  foreach ($notifications as $notif) {
    if (!$notif['is_read']) $unread_count++;
  }
}
?>

<!-- Upbar CSS -->
<style>
  .fixed-upbar {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    min-width: 100vw;
    z-index: 999;
    background: #fff;
    box-shadow: 0 2px 12px #FFD59A44;
    /* Đổ bóng nhẹ cam nhạt */
  }

  .upbar-spacer {
    height: 68px;
    /* Điều chỉnh nếu upbar của bạn cao hơn/thấp hơn */
    width: 100%;
    display: block;
  }

  @media (max-width: 800px) {
    .fixed-upbar {
      min-width: unset;
      width: 100vw;
    }
  }
</style>

<!-- Upbar luôn hiển thị trên cùng -->
<div class="fixed-upbar">
  <!-- Start Head -->
  <div class="head bg-white p-15 between-flex">
    <div class="d-flex align-items-center" style="margin-left:auto;">
      <!-- User Info -->
      <div class="text-end" style="font-family: 'Open Sans', sans-serif; margin-right:10px;">
        <div class="fw-semibold text-dark"><?= htmlspecialchars($name) ?></div>
        <div class="text-muted small">ID: <?= htmlspecialchars($student_id) ?></div>
      </div>
      <!-- Notification Bell -->
      <div class="notif-wrapper position-relative" style="position: relative;">
        <div style="position: relative; display: inline-block;">
          <i id="notifBell" class="fa-solid fa-bell" style="margin-right: 10px;"></i>
          <?php if ($unread_count > 0): ?>
            <span id="notifDot"
                style="
                  position: absolute;
                  top: 0px;
                  right: -3px;
                  width: 8px;
                  height: 8px;
                  background-color: red;
                  border-radius: 50%;
                  display: inline-block;
                  color: #fff;
                  font-size: 8px;
                  font-weight: 600;
                  text-align: center;
                  line-height: 11px;
                  margin-right: 10px;
                "><?= $unread_count > 1 ? $unread_count : '' ?></span>
          <?php endif; ?>
        </div>
        <!-- Dropdown Panel -->
        <div id="notifPanel"
          class="notification-dropdown"
          style="
             display: none;
             position: absolute;
             top: 120%;
             right: 0;
             background-color: white;
             border: 1px solid #ddd;
             box-shadow: 0 4px 8px rgba(0,0,0,0.1);
             z-index: 999;
             width: 320px;
             border-radius:8px;
           ">
          <div class="dropdown-header"
            style="
              text-align:center;
              font-size:1.18em;
              font-weight:800;
              color:#FF951F;
              padding: 14px 0 10px 0;
              border-bottom: 1px solid #FFD59A;
              background: transparent;
              letter-spacing: 0.5px;
           ">
            Notifications
          </div>
          <div class="dropdown-body" style="max-height: 300px; overflow-y: auto;">
            <?php if (empty($notifications)): ?>
              <div class="dropdown-item p-2">
                <span>No notifications.</span>
              </div>
            <?php else: ?>
              <?php foreach ($notifications as $notif): ?>
                <div
                  class="dropdown-item"
                  style="
                    background: #fff;
                    border: 2px solid #FFD59A;             /* Viền cam nhạt */
                    border-radius: 12px;                   /* Bo góc mềm hơn */
                    margin: 16px 12px;                     /* Cách các cạnh trái phải trên dưới */
                    box-shadow: 0 2px 10px 0 rgba(255,149,31,0.10); /* Đổ bóng cam nhạt */
                    padding: 12px 16px;
                    position: relative;
                  ">
                  <div style="font-weight:700; color:#FF951F; font-size: 1.09em; margin-bottom: 2px;">
                    <?= htmlspecialchars($notif['title']) ?>
                  </div>
                  <div style="font-size:15px; color:#222; margin-bottom:3px;">
                    <?= nl2br(htmlspecialchars($notif['message'])) ?>
                  </div>
                  <div style="font-size:12px;color:#888">
                    <?= date('d/m/Y H:i', strtotime($notif['created_at'])) ?>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
          <div class="dropdown-footer"
            style="
              text-align:center;
              padding: 12px 0 6px 0;
              border-top: 1px solid #FFD59A;
              background: transparent;
           ">
            <a href="/vgtutor/tutor/notification_tutor.php"
              style="
                color:#FF951F;
                font-weight:700;
                font-size:1.09em;
                text-decoration: none;
                border-radius: 8px;
                padding: 4px 18px;
                transition: background 0.2s;
                display: inline-block;
             "
              onmouseover="this.style.background='#FFF5E6'"
              onmouseout="this.style.background='transparent'">See all</a>
          </div>
        </div>
      </div>
      <!-- End Notification Bell -->
    </div>
  </div>
  <!-- End Head -->
</div>
<!-- Spacer để tránh bị upbar che mất nội dung -->
<div class="upbar-spacer"></div>

<script>
  document.addEventListener("DOMContentLoaded", function() {
    const bell = document.getElementById("notifBell");
    const panel = document.getElementById("notifPanel");

    if (bell && panel) {
      bell.addEventListener("click", function(e) {
        e.stopPropagation();
        panel.style.display = panel.style.display === "block" ? "none" : "block";
      });
      document.addEventListener("click", function(e) {
        if (!bell.contains(e.target) && !panel.contains(e.target)) {
          panel.style.display = "none";
        }
      });
    }
  });
</script>