<?php
function logSecurityEvent($event, $user_id = null, $ip = null) {
    $log_entry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'event' => $event,
        'user_id' => $user_id ? hash('sha256', $user_id) : null, // Hash user ID
        'ip' => $ip ? hash('sha256', $ip) : null, // Hash IP
        'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 200) : null
    ];
    
    $log_file = '/secure_logs/security.log'; // Outside web root
    file_put_contents($log_file, json_encode($log_entry) . "\n", FILE_APPEND | LOCK_EX);
}