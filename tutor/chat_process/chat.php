<?php
session_start();
if (!isset($_SESSION['tutorid']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'Tutor') {
    $em = "You are not authorized to access this page.";
    header("Location: ../login.php?error=" . urlencode($em));
    exit;
}

$userid   = $_SESSION['tutorid'];
$username = $_SESSION['name'];
$role     = $_SESSION['role'];

// Include required files.
include "../../DB_connection.php";
include "../data/chatHandle.php";
include "../data/session.php";

// Get the selected student from the URL if available.
$selectedStudent = isset($_GET['studentid']) ? $_GET['studentid'] : null;

// Handle AJAX request for pending sessions.
if (isset($_GET['action']) && $_GET['action'] === 'getPendingSessions' && isset($_GET['studentid'])) {
    $pendingSessions = getPendingSessions($conn, $userid, $_GET['studentid']);
    if (!empty($pendingSessions)) {
        echo '<table class="table">
                <thead>
                    <tr>
                        <th>Course Name</th>
                        <th>Duration (hours)</th>
                        <th>Place</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>';
        foreach ($pendingSessions as $session) {
            $dt   = strtotime($session['date_and_time']);
            $date = date('Y-m-d', $dt);
            $time = date('H:i:s', $dt);
            // Row with Accept, Deny and Edit buttons.
            echo '<tr>
                    <td>' . htmlspecialchars($session['course_name']) . '</td>
                    <td>' . htmlspecialchars($session['duration']) . '</td>
                    <td>' . htmlspecialchars($session['place'] ?? 'N/A') . '</td>
                    <td>' . $date . '</td>
                    <td>' . $time . '</td>
                    <td>
                        <form action="" method="post" class="d-inline">
                            <input type="hidden" name="tutorid" value="' . htmlspecialchars($session['tutorid']) . '">
                            <input type="hidden" name="studentid" value="' . htmlspecialchars($session['studentid']) . '">
                            <input type="hidden" name="date_and_time" value="' . htmlspecialchars($session['date_and_time']) . '">
                            <input type="hidden" name="courseid" value="' . htmlspecialchars($session['courseid']) . '">
                            <button type="submit" name="action" value="accept" class="btn btn-permit btn-sm">Accept</button>
                        </form>
                        <form action="" method="post" class="d-inline">
                            <input type="hidden" name="tutorid" value="' . htmlspecialchars($session['tutorid']) . '">
                            <input type="hidden" name="studentid" value="' . htmlspecialchars($session['studentid']) . '">
                            <input type="hidden" name="date_and_time" value="' . htmlspecialchars($session['date_and_time']) . '">
                            <input type="hidden" name="courseid" value="' . htmlspecialchars($session['courseid']) . '">
                            <button type="submit" name="action" value="deny" class="btn btn-deny btn-sm">Deny</button>
                        </form>
                        <button type="button" class="btn btn-secondary btn-sm editSessionBtn"
                            data-tutorid="' . htmlspecialchars($session['tutorid']) . '"
                            data-studentid="' . htmlspecialchars($session['studentid']) . '"
                            data-date_and_time="' . htmlspecialchars($session['date_and_time']) . '"
                            data-courseid="' . htmlspecialchars($session['courseid']) . '"
                            data-place="' . htmlspecialchars($session['place'] ?? '') . '"
                            data-duration="' . htmlspecialchars($session['duration']) . '"
                        >Edit</button>
                    </td>
                 </tr>';
        }
        echo '</tbody></table>';
    } else {
        echo '<p>No pending sessions found for the selected student.</p>';
    }
    exit;
}

// Fetch the list of students the tutor has contacted.
$students = fetchAllStudentMessage($conn, $userid);

// If a student is selected via URL, preload their pending sessions.
$pendingSessions = [];
if ($selectedStudent) {
    $pendingSessions = getPendingSessions($conn, $userid, $selectedStudent);
}

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
    } elseif (isset($_POST['tutorid'], $_POST['courseid'])) {
        $tutorid = intval($_POST['tutorid']);
        $studentid = intval($_POST['studentid']);
        $courseid = intval($_POST['courseid']);
        $date_and_time = $_POST['date_and_time'] ?? null;
        updateConsensus($conn, $action, $tutorid, $studentid, $courseid, $date_and_time);
    }
}
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
<?php include "../inc/navbar.php"; ?>
<div class="messenger-main">
    <div class="messenger-sidebar">
        <h5>Contacted Students</h5>
        <ul class="student-list" id="studentList">
            <?php if (empty($students)): ?>
                <li>No students to chat with.</li>
            <?php else: ?>
                <?php foreach ($students as $student): ?>
                    <li data-studentid="<?= htmlspecialchars($student['studentid']); ?>">
                        <?= htmlspecialchars($student['name']); ?>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>

    <div class="messenger-chat-area">
        <div class="messenger-header" id="chatHeader">Select a student to chat</div>
        <div class="messenger-messages" id="messages">
            <!-- Chat messages will be dynamically loaded -->
        </div>
    </div>

    <!-- Messenger form with the 'i' (information) button -->
    <form class="messenger-input-bar d-flex align-items-center" id="chatForm" autocomplete="off" style="display:none;">
        <button type="button" class="btn btn-info me-2" data-bs-toggle="modal" data-bs-target="#pendingSessionsModal">
            <strong>i</strong>
        </button>
        <input type="text" id="messageInput" placeholder="Type a message..." required class="flex-grow-1">
        <button type="submit" class="btn btn-primary ms-2">Send</button>
    </form>
</div>

<!-- Pending Sessions Modal -->
<div class="modal fade" id="pendingSessionsModal" tabindex="-1" aria-labelledby="pendingSessionsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pendingSessionsModalLabel">Pending Sessions Info</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="pendingSessionsContent">
            <?php if (!empty($pendingSessions)) : ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Course Name</th>
                            <th>Duration (hours)</th>
                            <th>Place</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pendingSessions as $session): 
                            $dt   = strtotime($session['date_and_time']);
                            $date = date('Y-m-d', $dt);
                            $time = date('H:i:s', $dt);
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($session['course_name']); ?></td>
                                <td><?= htmlspecialchars($session['duration']); ?></td>
                                <td><?= htmlspecialchars($session['place'] ?? 'N/A'); ?></td>
                                <td><?= $date; ?></td>
                                <td><?= $time; ?></td>
                                <td>
                                    <form action="" method="post" class="d-inline">
                                        <input type="hidden" name="tutorid" value="<?= htmlspecialchars($session['tutorid']) ?>">
                                        <input type="hidden" name="studentid" value="<?= htmlspecialchars($session['studentid']) ?>">
                                        <input type="hidden" name="date_and_time" value="<?= htmlspecialchars($session['date_and_time']) ?>">
                                        <input type="hidden" name="courseid" value="<?= htmlspecialchars($session['courseid']) ?>">
                                        <button type="submit" name="action" value="accept" class="btn btn-permit btn-sm">Accept</button>
                                    </form>
                                    <form action="" method="post" class="d-inline">
                                        <input type="hidden" name="tutorid" value="<?= htmlspecialchars($session['tutorid']) ?>">
                                        <input type="hidden" name="studentid" value="<?= htmlspecialchars($session['studentid']) ?>">
                                        <input type="hidden" name="date_and_time" value="<?= htmlspecialchars($session['date_and_time']) ?>">
                                        <input type="hidden" name="courseid" value="<?= htmlspecialchars($session['courseid']) ?>">
                                        <button type="submit" name="action" value="deny" class="btn btn-deny btn-sm">Deny</button>
                                    </form>
                                    <button type="button" class="btn btn-secondary btn-sm editSessionBtn"
                                        data-tutorid="<?= htmlspecialchars($session['tutorid']) ?>"
                                        data-studentid="<?= htmlspecialchars($session['studentid']) ?>"
                                        data-date_and_time="<?= htmlspecialchars($session['date_and_time']) ?>"
                                        data-courseid="<?= htmlspecialchars($session['courseid']) ?>"
                                        data-place="<?= htmlspecialchars($session['place'] ?? '') ?>"
                                        data-duration="<?= htmlspecialchars($session['duration']) ?>"
                                    >Edit</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No pending sessions found for the selected student.</p>
            <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Edit Session Modal -->
<div class="modal fade" id="editSessionModal" tabindex="-1" aria-labelledby="editSessionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" method="post" id="editSessionForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="editSessionModalLabel">Edit Session</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Hidden fields to identify the session -->
                    <input type="hidden" name="action" value="editSession">
                    <input type="hidden" name="tutorid" id="editTutorId">
                    <input type="hidden" name="studentid" id="editStudentId">
                    <input type="hidden" name="courseid" id="editCourseId">
                    <input type="hidden" name="original_date_and_time" id="editOriginalDateTime">
                    
                    <div class="mb-3">
                        <label for="editPlace" class="form-label">Place</label>
                        <input type="text" class="form-control" name="place" id="editPlace">
                    </div>
                    <div class="mb-3">
                        <label for="editDate" class="form-label">Date</label>
                        <input type="date" class="form-control" name="date" id="editDate">
                    </div>
                    <div class="mb-3">
                        <label for="editTime" class="form-label">Time</label>
                        <input type="time" class="form-control" name="time" id="editTime">
                    </div>
                    <div class="mb-3">
                        <label for="editDuration" class="form-label">Duration (hours)</label>
                        <input type="number" step="0.1" class="form-control" name="duration" id="editDuration">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
// Set currentStudentId from the URL (if available) or null.
let currentStudentId = <?= $selectedStudent ? json_encode($selectedStudent) : 'null' ?>;

function fetchMessages() {
    if (!currentStudentId) return;
    $.get('../data/chat_api.php?action=fetch&studentid=' + currentStudentId, function(data) {
        let messages = JSON.parse(data);
        let html = '';
        messages.forEach(function(msg) {
            let cls = msg.userid == <?= json_encode($userid); ?> ? 'sent' : 'received';
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

function fetchPendingSessions() {
    if (!currentStudentId) return;
    $.get('chat.php', { action: 'getPendingSessions', studentid: currentStudentId }, function(data) {
        $('#pendingSessionsContent').html(data);
    });
}

// When a student is selected from the sidebar, update currentStudentId.
$('#studentList').on('click', 'li', function() {
    $('#studentList li').removeClass('active');
    $(this).addClass('active');
    currentStudentId = $(this).data('studentid');
    $('#chatHeader').text('Chat with ' + $(this).text());
    $('#chatForm').show();
    fetchMessages();
    fetchPendingSessions();
});

// Handle message sending.
$('#chatForm').on('submit', function(e) {
    e.preventDefault();
    if (!currentStudentId) return;
    let text = $('#messageInput').val();
    $.post('../data/chat_api.php?action=send', { text: text, studentid: currentStudentId }, function() {
        $('#messageInput').val('');
        fetchMessages();
    });
});
setInterval(fetchMessages, 1500);

// Preselect and fetch messages and pending sessions if a studentid is passed in the URL.
if (currentStudentId) {
    let $li = $('#studentList li[data-studentid="' + currentStudentId + '"]');
    if ($li.length) {
        $li.addClass('active');
        $('#chatHeader').text('Chat with ' + $li.text());
        $('#chatForm').show();
        fetchMessages();
        fetchPendingSessions();
    }
}

// Edit session button handler to populate the edit modal.
$(document).on('click', '.editSessionBtn', function(){
    let btn = $(this);
    $('#editTutorId').val(btn.data('tutorid'));
    $('#editStudentId').val(btn.data('studentid'));
    $('#editCourseId').val(btn.data('courseid'));
    $('#editOriginalDateTime').val(btn.data('date_and_time'));
    $('#editPlace').val(btn.data('place'));

    // Parse the date_and_time value and split into date and time fields.
    let dt = new Date(btn.data('date_and_time'));
    let year = dt.getFullYear();
    let month = ("0" + (dt.getMonth() + 1)).slice(-2);
    let day = ("0" + dt.getDate()).slice(-2);
    let hours = ("0" + dt.getHours()).slice(-2);
    let minutes = ("0" + dt.getMinutes()).slice(-2);
    $('#editDate').val(`${year}-${month}-${day}`);
    $('#editTime').val(`${hours}:${minutes}`);
    
    $('#editDuration').val(btn.data('duration'));

    // Show the Edit Session modal.
    $('#editSessionModal').modal('show');
});
</script>
</body>
</html>
<?php
// End of file.
?>