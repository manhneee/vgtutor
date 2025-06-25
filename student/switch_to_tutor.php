<?php
session_start();

if (isset($_SESSION['tutorid'])) {
    $_SESSION['role'] = 'Tutor';
    header("Location: ../tutor/index.php");
    exit;
} else {
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Access Denied</title>
  <link rel="stylesheet" href="../css/switch_to_tutor.css" />

</head>
<body class="bg-orange">

  <div class="modal-overlay">
    <div class="modal-box">
      <h2 class="m-0 mb-10 fs-15 fw-bold">⚠️ Access Denied</h2>
      <p class="fs-14 c-grey mb-10">You are not registered as a tutor.</p>
      <button onclick="window.history.back()">OK</button>
    </div>
  </div>
</body>
</html>
<?php
exit;
}
?>
