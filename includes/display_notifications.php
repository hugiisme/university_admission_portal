<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_GET["page"])){
        header("Location: ../index.php");
        exit();
    }

    function display_notifications() {
        if (isset($_SESSION['notifications']) && count($_SESSION['notifications']) > 0) {
            $currentTime = time();  

            foreach ($_SESSION['notifications'] as $key => $notification) {
                if ($currentTime < $notification['expireTime']) {
                    $message = htmlspecialchars($notification['message'], ENT_QUOTES, 'UTF-8');
                    $type = htmlspecialchars($notification['type'], ENT_QUOTES, 'UTF-8');
                    $timeout = intval($notification['timeout']);

                    echo "<div class='notification {$type}' data-timeout='{$timeout}'>
                            <span>{$message}</span>
                        </div>";
                } else {
                    unset($_SESSION['notifications'][$key]);
                }
            }
        }
    }
?>
