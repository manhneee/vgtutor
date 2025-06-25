<?php
session_start();
if (!isset($_SESSION['tutorid']) || $_SESSION['role'] !== 'Tutor') {
  header("Location: ../login.php?error=" . urlencode("Unauthorized access"));
  exit;
}

$userid = $_SESSION['tutorid'];
$username = $_SESSION['name'];

include "../../DB_connection.php";
include "../data/chatHandle.php";
include "../data/session.php";
include_once dirname(__DIR__, 2) . '/student/data/notifications.php';


$students = fetchAllStudentMessage($conn, $userid);
$selectedStudent = $_GET['studentid'] ?? null;
$pendingSessions = $selectedStudent ? getPendingSessions($conn, $userid, $selectedStudent) : [];

// Handle permit/deny and edit actions.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
  $action = $_POST['action'];
  if ($action === 'editSession') {
    // Handle edit session updates.
    $tutorid  = intval($_POST['tutorid']);
    $studentid = intval($_POST['studentid']);
    $courseid = intval($_POST['courseid']);
    $originalDateTime = $_POST['original_date_and_time'];
    $newPlace = $_POST['place'] ?? '';
    $newDate = $_POST['date'] ?? '';
    $newTime = $_POST['time'] ?? '';
    $newDuration = $_POST['duration'] ?? '';
    $newDateTime = $newDate . ' ' . $newTime;
    // This function should update the session details in your database.
    updateSessionDetails($conn, $tutorid, $studentid, $courseid, $originalDateTime, $newPlace, $newDateTime, $newDuration);
    header("Location: chat.php?studentid=" . urlencode($studentid));
  } elseif (isset($_POST['tutorid'], $_POST['courseid'])) {
    $tutorid = intval($_POST['tutorid']);
    $studentid = intval($_POST['studentid']);
    $courseid = intval($_POST['courseid']);
    $date_and_time = $_POST['date_and_time'] ?? null;
    updateConsensus($conn, $action, $tutorid, $studentid, $courseid, $date_and_time);
    $tutor_name = $_SESSION['name'];
    $course_name = getCourseName($conn, $courseid);

    if ($action === 'accept') {
      $message = "Your session request for course \"$course_name\" has been accepted by tutor $tutor_name.";
      addNotification(
        $conn,
        $studentid,         // user_id_receive: student nhận
        $tutorid,           // user_id_send: tutor gửi
        "Session Accepted", // title
        $message,           // message
        "Session"           // type
      );
    } elseif ($action === 'deny') {
      $message = "Your session request for course \"$course_name\" has been denied by tutor $tutor_name.";
      addNotification(
        $conn,
        $studentid,
        $tutorid,
        "Session Denied",
        $message,
        "Session"
      );
    }
  }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Chat</title>
  <link rel="stylesheet" href="../../css/ChatForTutor.css">
  <link rel="stylesheet" href="../../css/style1.css">
  <link rel="stylesheet" href="../../css/framework.css">
  <link rel="stylesheet" href="../../css/master.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;500&display=swap" rel="stylesheet">

</head>

<body class="body-home">
  <div class="page d-flex">
    <?php include_once '../inc/navbar.php'; ?>
    <div class="content w-full">
      <?php include_once '../inc/upbar.php'; ?>
      <div class="chat-container">
        <div class="sidebar">
          <h3><i class="fa fa-users me-1"></i> Students</h3>
          <ul id="studentList">
            <?php if (empty($students)): ?>
              <li>No students yet</li>
            <?php else: ?>
              <?php foreach ($students as $student): ?>
                <li data-studentid="<?= htmlspecialchars($student['studentid']) ?>">
                  <i class="fa fa-user me-1"></i> <?= htmlspecialchars($student['name']) ?>
                </li>
              <?php endforeach; ?>
            <?php endif; ?>
          </ul>
        </div>

        <div class="chat-box">
          <div class="chat-header" id="chatHeader">Select a student to start chatting</div>
          <div class="messages" id="messages"></div>

          <form class="input-bar" id="chatForm" style="display:none;" autocomplete="off">
            <button type="button" class="btn btn-info me-2" id="infoBtn">
              <strong>i</strong>
            </button>
            <input type="text" id="messageInput" placeholder="Type your message..." required />
            <button type="submit"><i class="fa fa-paper-plane"></i></button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Pending Sessions Modal -->
  <div class="modal fade" id="pendingSessionsModal" tabindex="-1">
    <div class="modal-dialog custom-modal">
      <div class="modal-content custom-modal-content">
        <div class="custom-modal-header c-orange">
          <h4>Pending Session</h4>
          <button type="button" class="close-btn" data-bs-dismiss="modal" aria-label="Close">✕</button>
        </div>
        <div class="custom-modal-body" id="pendingSessionsContent">
          <?php if (!empty($pendingSessions)) : ?>
            <table class="pending-table">
              <thead>
                <tr>
                  <th>Course</th>
                  <th>Duration</th>
                  <th>Place</th>
                  <th>Date</th>
                  <th>Time</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($pendingSessions as $session):
                  $dt = strtotime($session['date_and_time']);
                  $date = date('Y-m-d', $dt);
                  $time = date('H:i:s', $dt);
                ?>
                  <tr>
                    <td><?= htmlspecialchars($session['course_name']) ?></td>
                    <td><?= htmlspecialchars($session['duration']) ?></td>
                    <td><?= htmlspecialchars($session['place']) ?></td>
                    <td><?= $date ?></td>
                    <td><?= $time ?></td>
                    <td>
                      <div class="action-buttons">
                        <!-- Accept -->
                        <form action="" method="post" style="display:inline-block; margin-bottom: 5px;">
                          <input type="hidden" name="action" value="accept">
                          <input type="hidden" name="tutorid" value="<?= $session['tutorid'] ?>">
                          <input type="hidden" name="studentid" value="<?= $session['studentid'] ?>">
                          <input type="hidden" name="date_and_time" value="<?= $session['date_and_time'] ?>">
                          <input type="hidden" name="courseid" value="<?= $session['courseid'] ?>">
                          <button class="btn-session accept accept-session-btn"
                            data-action="accept"
                            data-tutorid="<?= $session['tutorid'] ?>"
                            data-studentid="<?= $session['studentid'] ?>"
                            data-date_and_time="<?= $session['date_and_time'] ?>"
                            data-courseid="<?= $session['courseid'] ?>">
                            Accept
                          </button>
                        </form>

                        <!-- Deny -->
                        <form action="" method="post" style="display:inline-block; margin-bottom: 5px;">
                          <input type="hidden" name="action" value="deny">
                          <input type="hidden" name="tutorid" value="<?= $session['tutorid'] ?>">
                          <input type="hidden" name="studentid" value="<?= $session['studentid'] ?>">
                          <input type="hidden" name="date_and_time" value="<?= $session['date_and_time'] ?>">
                          <input type="hidden" name="courseid" value="<?= $session['courseid'] ?>">
                          <button class="btn-session deny deny-session-btn"
                            data-action="deny"
                            data-tutorid="<?= $session['tutorid'] ?>"
                            data-studentid="<?= $session['studentid'] ?>"
                            data-date_and_time="<?= $session['date_and_time'] ?>"
                            data-courseid="<?= $session['courseid'] ?>">
                            Deny
                          </button>
                        </form>

                        <!-- Edit -->
                        <button type="button" class="btn-session edit editSessionBtn"
                          data-tutorid="<?= $session['tutorid'] ?>"
                          data-studentid="<?= $session['studentid'] ?>"
                          data-courseid="<?= $session['courseid'] ?>"
                          data-date_and_time="<?= $session['date_and_time'] ?>"
                          data-place="<?= $session['place'] ?>"
                          data-duration="<?= $session['duration'] ?>">
                          Edit
                        </button>
                      </div>
                    </td>

                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php else: ?>
            <p>No pending sessions for this student.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>


  <div class="modal fade" id="editSessionModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form action="" method="post">
          <div class="modal-header custom-modal-header">
            <h5 class="modal-title">Edit Session</h5>
            <button type="button" class="close-btn" data-bs-dismiss="modal" aria-label="Close">✕</button>
          </div>

          <div class="modal-body">
            <input type="hidden" name="action" value="editSession">
            <input type="hidden" name="tutorid" id="editTutorId">
            <input type="hidden" name="studentid" id="editStudentId">
            <input type="hidden" name="courseid" id="editCourseId">
            <input type="hidden" name="original_date_and_time" id="editOriginalDateTime">

            <div class="mb-3">
              <label class="form-label">Place</label>
              <input type="text" class="form-control" name="place" id="editPlace">
            </div>
            <div class="mb-3">
              <label class="form-label">Date</label>
              <input type="date" class="form-control" name="date" id="editDate">
            </div>
            <div class="mb-3">
              <label class="form-label">Time</label>
              <input type="time" class="form-control" name="time" id="editTime">
            </div>
            <div class="mb-3">
              <label class="form-label">Duration</label>
              <input type="number" step="0.1" class="form-control" name="duration" id="editDuration">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn-save">Save</button>
          </div>

        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script>
    let currentStudentId = <?= json_encode($selectedStudent) ?>;

    document.getElementById('infoBtn').addEventListener('click', function() {
      var pendingModal = new bootstrap.Modal(document.getElementById('pendingSessionsModal'));
      pendingModal.show();
    });

    function fetchMessages() {
      if (!currentStudentId) return;
      const $msgContainer = $('#messages');
      const isAtBottom = $msgContainer[0].scrollTop + $msgContainer[0].clientHeight >= $msgContainer[0].scrollHeight - 20;

      $.get('../data/chat_api.php?action=fetch&studentid=' + currentStudentId, function(data) {
        let messages = [];
        try {
          messages = JSON.parse(data);
        } catch (e) {}

        let html = '';
        messages.forEach(function(msg) {
          const cls = msg.userid == <?= json_encode($userid) ?> ? 'sent' : 'received';
          html += `
            <div class="message-wrapper ${cls}">
              <div class="message-username">${msg.username}</div>
              <div class="message">${msg.text}</div>
              <div class="timestamp">${msg.time}</div>
            </div>`;
        });

        $msgContainer.html(html);
        if (isAtBottom) {
          $msgContainer.scrollTop($msgContainer[0].scrollHeight);
        }
      });
    }

    $('#studentList').on('click', 'li', function() {
      $('#studentList li').removeClass('active');
      $(this).addClass('active');
      currentStudentId = $(this).data('studentid');
      $('#chatHeader').text('Chat with ' + $(this).text());
      $('#chatForm').show();
      fetchMessages();
    });

    $('#chatForm').on('submit', function(e) {
      e.preventDefault();
      if (!currentStudentId) return;
      const text = $('#messageInput').val();
      $.post('../data/chat_api.php?action=send', {
        text,
        studentid: currentStudentId
      }, function() {
        $('#messageInput').val('');
        fetchMessages();
      });
    });

    setInterval(fetchMessages, 1500);

    if (currentStudentId) {
      const $li = $('#studentList li[data-studentid="' + currentStudentId + '"]');
      if ($li.length) {
        $li.addClass('active');
        $('#chatHeader').text('Chat with ' + $li.text());
        $('#chatForm').show();
        fetchMessages();
      }
    }

    $(document).on('click', '.editSessionBtn', function() {
      const btn = $(this);
      $('#editTutorId').val(btn.data('tutorid'));
      $('#editStudentId').val(btn.data('studentid'));
      $('#editCourseId').val(btn.data('courseid'));
      $('#editOriginalDateTime').val(btn.data('date_and_time'));
      $('#editPlace').val(btn.data('place'));

      const dt = new Date(btn.data('date_and_time'));
      $('#editDate').val(dt.toISOString().split('T')[0]);
      $('#editTime').val(dt.toTimeString().slice(0, 5));
      $('#editDuration').val(btn.data('duration'));

      const modal = new bootstrap.Modal(document.getElementById('editSessionModal'));
      modal.show();
    });

    $(document).on('click', '.accept-session-btn, .deny-session-btn', function(e) {
      e.preventDefault();
      const $btn = $(this);
      const $form = $btn.closest('form');
      const payload = $form.serialize();
      $.post('chat.php', payload, function() {
        // Refresh the modal content after action
        location.reload(); // Quick way: reload page to update sessions and chat
        // Alternatively, you can use AJAX to reload only the modal content
      });
    });
  </script>
</body>

</html>