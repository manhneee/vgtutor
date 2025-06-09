<?php
session_start();
if (isset($_SESSION['tutorid']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Tutor') {
        $userid = $_SESSION['tutorid'];
        $username = $_SESSION['name'];
        $role = $_SESSION['role'];

        include "../../DB_connection.php";
        include "../data/chatHandle.php";
        // Fetch messages for this tutor
        $students = fetchAllStudentMessage($conn, $userid);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Messenger Chat</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/chatForTutor.css">
</head>
<body>
<?php include "navbar.php"; ?>
<div class="messenger-main">
    <!-- Sidebar: Contacted Students -->
    <div class="messenger-sidebar">
        <h5>Contacted Students</h5>
        <ul class="student-list" id="studentList">
            <?php
            if (empty($students)) {
                echo '<li>No students to chat with.</li>';
            }
            foreach ($students as $student): ?>
                <li data-studentid="<?= htmlspecialchars($student['studentid']) ?>">
                    <?= htmlspecialchars($student['name']) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <!-- Chat Area -->
    <div class="messenger-chat-area">
        <div class="messenger-header" id="chatHeader">
            Select a student to chat
        </div>
        <div class="messenger-messages" id="messages"></div>
    </div>
    <form class="messenger-input-bar" id="chatForm" autocomplete="off" style="display:none;">
        <input type="text" id="messageInput" placeholder="Type a message..." required>
        <button type="submit">Send</button>
    </form>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
let currentStudentId = null;

function fetchMessages() {
    if (!currentStudentId) return;
    $.get('../data/chat_api.php?action=fetch&studentid=' + currentStudentId, function(data) {
        let messages = JSON.parse(data);
        let html = '';
        messages.forEach(function(msg) {
            let cls = msg.userid == <?= json_encode($userid) ?> ? 'sent' : 'received';
            html += '<div class="message ' + cls + '">' +
                '<div>' + msg.username + ':</div>' +
                '<div>' + msg.text + '</div>' +
                '<div style="font-size:0.8em;color:#888;">' + msg.time + '</div>' +
                '</div>';
        });
        $('#messages').html(html);
        $('#messages').scrollTop($('#messages')[0].scrollHeight);
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
    let text = $('#messageInput').val();
    $.post('../data/chat_api.php?action=send', {text: text, studentid: currentStudentId}, function() {
        $('#messageInput').val('');
        fetchMessages();
    });
});
setInterval(fetchMessages, 1500);
</script>
</body>
</html>
<?php
 } else {
    $em = "You are not authorized to access this page.";
    header("Location: ../login.php?error=$em");
    exit;
 } 
} else {
    $em = "You are not logged in.";
    header("Location: ../login.php?error=$em");
    exit;
 }
?>