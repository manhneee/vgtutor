
<?php
function checkRateLimit($identifier, $max_attempts = 5, $time_window = 300) {
    $file = sys_get_temp_dir() . '/rate_limit_' . md5($identifier);
    
    if (file_exists($file)) {
        $data = json_decode(file_get_contents($file), true);
        if (time() - $data['time'] < $time_window) {
            if ($data['attempts'] >= $max_attempts) {
                return false;
            }
            $data['attempts']++;
        } else {
            $data = ['attempts' => 1, 'time' => time()];
        }
    } else {
        $data = ['attempts' => 1, 'time' => time()];
    }
    
    file_put_contents($file, json_encode($data));
    return true;
}







