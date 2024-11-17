<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    

    function add_notification($message, $timeout = 5000, $type = 'info') {
        if (!isset($_SESSION['notifications'])) {
            $_SESSION['notifications'] = [];
        }

        $expireTime = time() + ($timeout / 1000); 
        $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

        $_SESSION['notifications'][] = [
            'message' => $message,
            'type' => $type,
            'timeout' => $timeout,
            'expireTime' => $expireTime,  
        ];
    }
?>
