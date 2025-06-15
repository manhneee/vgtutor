<?php

session_start();

// Simple file-based storage for demonstration
$file = $_SERVER['DOCUMENT_ROOT'] . "/vgtutor/chatlog.json";
if (!file_exists($file)) file_put_contents($file, '[]');

// Fetch messages for a specific tutor-student pair
if ($_GET['action'] === 'fetch' && isset($_GET['studentid'])) {
    $tutorid = $_SESSION['tutorid'];
    $studentid = $_GET['studentid'];
    $messages = json_decode(file_get_contents($file), true);
    $filtered = array_filter($messages, function($msg) use ($tutorid, $studentid) {
        return $msg['tutorid'] == $tutorid && $msg['studentid'] == $studentid;
    });
    echo json_encode(array_values($filtered));
    exit;
}

// Send a message from tutor to student
if ($_GET['action'] === 'send' && isset($_POST['text'], $_POST['studentid'])) {
    $tutorid = $_SESSION['tutorid'];
    $studentid = $_POST['studentid'];
    $username = $_SESSION['name'];
    $messages = json_decode(file_get_contents($file), true);
    $messages[] = [
        'userid' => $tutorid,
        'username' => $username,
        'studentid' => $studentid,
        'tutorid' => $tutorid,
        'text' => htmlspecialchars($_POST['text']),
        'time' => date('H:i')
    ];
    file_put_contents($file, json_encode($messages));
    echo 'ok';
    exit;
}

echo 'Invalid request';
exit;
?>