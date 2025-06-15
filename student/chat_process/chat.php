<?php
session_start();
if (isset($_SESSION['studentid']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Student') {
        $userid = $_SESSION['studentid'];
        $username = $_SESSION['name'];
        $role = $_SESSION['role'];

        include "../../DB_connection.php";
        include "../data/chatHandle.php";
        // Fetch all tutors the student has chatted with
        $tutors = fetchAllTutorMessage($conn, $userid);
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
    <!-- Sidebar: Contacted Tutors -->
    <div class="messenger-sidebar">
        <h5>Contacted Tutors</h5>
        <ul class="student-list" id="studentList">
            <?php
            if (empty($tutors)) {
                echo '<li>No tutors to chat with.</li>';
            }
            foreach ($tutors as $tutor): ?>
                <li data-tutorid="<?= htmlspecialchars($tutor['tutorid']) ?>">
                    <?= htmlspecialchars($tutor['name']) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <!-- Chat Area -->
    <div class="messenger-chat-area">
        <div class="messenger-header" id="chatHeader">
            Select a tutor to chat
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
let currentTutorId = null;

function fetchMessages() {
    if (!currentTutorId) return;
    $.get('../data/chat_api.php?action=fetch&tutorid=' + currentTutorId, function(data) {
        let messages = [];
        try {
            messages = JSON.parse(data);
        } catch (e) {
            messages = [];
        }
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
    currentTutorId = $(this).data('tutorid');
    $('#chatHeader').text('Chat with ' + $(this).text());
    $('#chatForm').show();
    fetchMessages();
});

$('#chatForm').on('submit', function(e) {
    e.preventDefault();
    if (!currentTutorId) return;
    let text = $('#messageInput').val();
    $.post('../data/chat_api.php?action=send', {text: text, tutorid: currentTutorId}, function() {
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