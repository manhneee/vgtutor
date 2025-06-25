<?php


function addNotification($conn, $user_id_receive, $user_id_send, $title, $message, $type = 'system')
{
    $stmt = $conn->prepare("INSERT INTO notifications 
        (user_id_receive, user_id_send, title, message, type, is_read, created_at) 
        VALUES (?, ?, ?, ?, ?, 0, NOW())");
    $stmt->execute([$user_id_receive, $user_id_send, $title, $message, $type]);
}


function getAllNotifications($conn, $user_id, $limit = 100)
{
    $stmt = $conn->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT ?");
    $stmt->execute([$user_id, $limit]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function countUnreadNotifications($conn, $user_id)
{
    $stmt = $conn->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
    $stmt->execute([$user_id]);
    return $stmt->fetchColumn();
}
function markAllNotificationsRead($conn, $user_id)
{
    $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
    $stmt->execute([$user_id]);
}
