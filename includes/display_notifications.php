<?php
    // Start the session
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Function to display notifications
    function display_notifications() {
        if (isset($_SESSION['notifications']) && count($_SESSION['notifications']) > 0) {
            // Loop through all notifications
            foreach ($_SESSION['notifications'] as $notification) {
                echo "<div class='notification {$notification['type']}' data-timeout='{$notification['timeout']}'>
                        <span>{$notification['message']}</span>
                    </div>";
            }
            
            // Clear notifications after displaying
            unset($_SESSION['notifications']);
        }
    }
    
?>
