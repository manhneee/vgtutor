<?php
if (!isset($hasNotification)) {
    if (session_status() === PHP_SESSION_NONE) session_start();

    include_once __DIR__ . '/../../DB_connection.php';

    $hasNotification = false;
    $notification = '';

    // This example checks if their tutor application was recently denied
    if (isset($_SESSION['studentid'])) {
        $stmt = $conn->prepare("SELECT status, denied_at FROM tutor_registration WHERE studentid = ? ORDER BY denied_at DESC LIMIT 1");
        $stmt->execute([$_SESSION['studentid']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && $row['status'] === 'denied' && $row['denied_at']) {
            $deniedAt = strtotime($row['denied_at']);
            $daysSinceDenial = (time() - $deniedAt) / (60 * 60 * 24);

            if ($daysSinceDenial < 3) {
                $notification = "Your application to become a Tutor has been <strong>denied</strong>. Please register again after 3 days.";
                $hasNotification = true;
            }
        }
    }
}
?>


<!-- Start Head -->
<div class="head bg-white p-15 between-flex">
  <!-- Search Bar -->
  <div class="search p-relative">
    <input class="p-10" type="search" placeholder="Type A Keyword" />
  </div>

  <!-- Right Section: User Info + Icons -->
  <div class="d-flex align-items-center gap-4">
    <!-- User Info -->
    <div class="text-end" style="font-family: 'Open Sans', sans-serif;">
      <div class="fw-semibold text-dark"><?= htmlspecialchars($_SESSION['name']) ?></div>
      <div class="text-muted small">ID: <?= htmlspecialchars($_SESSION['studentid']) ?></div>
    </div>

    <!-- Icons Container -->
    <div class="d-flex align-items-center gap-3">

      <!-- Profile Icon -->
      <div style="width: 36px; height: 36px;">
        <i class="fa fa-user fs-4" style="color: #333;"></i>
      </div>

      <!-- Message Icon -->
<a href="/vgtutor/student/chat_process/chat.php"
   class="d-flex align-items-center justify-content-center"
   style="width: 36px; height: 36px; text-decoration: none; transform: translate(-4px, 3px);">
  <i class="fa fa-message fs-4" style="color: #333;"></i>
</a>

      <!-- Notification Wrapper -->
      <div class="notif-wrapper position-relative" style="position: relative;">
        <!-- Bell Icon Wrapper -->
        <div style="position: relative; display: inline-block;">
          <i id="notifBell"
             class="fa fa-bell fs-4"
             style="cursor: pointer; color: #333;"></i>

          <?php if (!empty($notification)): ?>
            <span id="notifDot"
                  style="
                    position: absolute;
                    top: 0;
                    right: 0;
                    width: 10px;
                    height: 10px;
                    background-color: red;
                    border-radius: 50%;
                    display: inline-block;
                  "></span>
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
               z-index: 1000;
               width: 300px;
             ">
          <div class="dropdown-header d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
            <strong>Notifications</strong>
          </div>

          <div class="dropdown-body" style="max-height: 300px; overflow-y: auto;">
            <?php if (!empty($notification)): ?>
              <div class="dropdown-item p-2 border-bottom">
                <i class="fa fa-info-circle text-primary me-2"></i>
                <span><?= $notification ?></span>
                <div class="text-end small text-muted mt-1">Just now</div>
              </div>
            <?php else: ?>
              <div class="dropdown-item p-2">
                <span>No new notifications.</span>
              </div>
            <?php endif; ?>
          </div>

          <div class="dropdown-footer text-center p-2 border-top">
            <a href="/vgtutor/student/notification_student.php" class="text-decoration-none text-orange">See all</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- End Head -->

<script>
document.addEventListener("DOMContentLoaded", function () {
  const bell = document.getElementById("notifBell");
  const panel = document.getElementById("notifPanel");
  const dot = document.getElementById("notifDot");

  if (bell && panel) {
    bell.addEventListener("click", function (e) {
      e.stopPropagation();
      panel.style.display = panel.style.display === "block" ? "none" : "block";
      if (dot) dot.style.display = "none";
    });

    document.addEventListener("click", function (e) {
      if (!bell.contains(e.target) && !panel.contains(e.target)) {
        panel.style.display = "none";
      }
    });
  }
});
</script>
