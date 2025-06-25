<?php
session_start();
if (isset($_SESSION['studentid']) && $_SESSION['role'] === 'Student') {
  $userid = $_SESSION['studentid'];
  $username = $_SESSION['name'];
  $role = $_SESSION['role'];

  include "../../DB_connection.php";
  include "../data/chatHandle.php";

  $tutors = fetchAllTutorMessage($conn, $userid);
  $selectedTutor = $_GET['tutorid'] ?? null;
?>
  <!DOCTYPE html>
  <html lang="en">

  <head>
    <meta charset="UTF-8">
    <title>Chat</title>
    <link rel="stylesheet" href="../../css/ChatForStudent.css" />
    <link rel="stylesheet" href="../../css/style1.css" />
    <link rel="stylesheet" href="../../css/framework.css" />
    <link rel="stylesheet" href="../../css/master.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;500&display=swap" rel="stylesheet" />

  </head>

  <body class="body-home">
    <div class="page d-flex">
      <?php include_once '../inc/navbar.php'; ?>
      <!-- LEFT  -->

      <div class="content w-full">
        <?php include_once '../inc/upbar.php'; ?>
        <!-- Search bar -->

        <div class="chat-container">
          <!-- Sidebar -->
          <div class="sidebar">
            <h3><i class="fa fa-users me-1"></i> Tutors</h3>
            <ul id="studentList">
              <?php if (empty($tutors)): ?>
                <li>No tutors yet</li>
              <?php else: ?>
                <?php foreach ($tutors as $tutor): ?>
                  <li data-tutorid="<?= htmlspecialchars($tutor['tutorid']) ?>">
                    <i class="fa fa-user-graduate c-orange me-1"></i> <?= htmlspecialchars($tutor['name']) ?>
                  </li>
                <?php endforeach; ?>
              <?php endif; ?>
            </ul>
          </div>

          <!-- Chat Box -->
          <div class="chat-box">
            <div class="chat-header" id="chatHeader">Select a tutor to start chatting</div>
            <div class="messages" id="messages"></div>

            <form class="input-bar" id="chatForm" style="display:none;" autocomplete="off">
              <input type="text" id="messageInput" placeholder="Type your message..." required />
              <button type="submit"><i class="fa fa-paper-plane"></i></button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
      let currentTutorId = <?= json_encode($selectedTutor) ?>;

      function fetchMessages() {
        if (!currentTutorId) return;

        const $msgContainer = $('#messages');
        const isAtBottom = $msgContainer[0].scrollTop + $msgContainer[0].clientHeight >= $msgContainer[0].scrollHeight - 20;

        $.get('../data/chat_api.php?action=fetch&tutorid=' + currentTutorId, function(data) {
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

          // Only auto-scroll if the user was already at the bottom
          if (isAtBottom) {
            $msgContainer.scrollTop($msgContainer[0].scrollHeight);
          }
        });
      }


      $('#studentList').on('click', 'li', function() {
        $('#studentList li').removeClass('active');
        $(this).addClass('active');
        currentTutorId = $(this).data('tutorid');
        $('#chatHeader').text('Chat with ' + $(this).text());
        $('#chatForm').show();
        fetchMessages();
      });

      $('#chatForm').on('submit', function(e) {
        e.preventDefault();
        if (!currentTutorId) return;
        const text = $('#messageInput').val();
        $.post('../data/chat_api.php?action=send', {
          text,
          tutorid: currentTutorId
        }, function() {
          $('#messageInput').val('');
          fetchMessages();
        });
      });

      setInterval(fetchMessages, 1500);

      // Preselect tutor if passed in URL
      if (currentTutorId) {
        const $li = $('#studentList li[data-tutorid="' + currentTutorId + '"]');
        if ($li.length) {
          $li.addClass('active');
          $('#chatHeader').text('Chat with ' + $li.text());
          $('#chatForm').show();
          fetchMessages();
        }
      }
    </script>
  </body>

  </html>
<?php
} else {
  header("Location: ../login.php?error=Unauthorized access");
  exit;
}
?>