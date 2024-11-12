<?php
    // Start the session if not already started
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Function to add a notification
    function add_notification($message, $timeout = 5000, $type = 'info') {
        // Check if notifications already exist, if not initialize an array
        if (!isset($_SESSION['notifications'])) {
            $_SESSION['notifications'] = [];
        }
        
        // Add notification to the stack (array)
        $_SESSION['notifications'][] = [
            'message' => $message,
            'type' => $type,
            'timeout' => $timeout,
        ];
    }
?>