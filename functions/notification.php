<?php
function addNotification($conn, $user_id, $title, $message)
{
    $stmt = $conn->prepare("INSERT INTO notifications (user_id, title, message, is_read, created_at) VALUES (?, ?, ?, 0, NOW())");
    $stmt->execute([$user_id, $title, $message]);
}
