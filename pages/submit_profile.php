<?php
    include_once("includes/session.php");
?>
<main>    
    <?php 
        $user_role = $_SESSION["role"];
        switch ($user_role) {
            case 'student':
                include_once("pages/submit_profile_student.php");
                break;
            case 'teacher':
            case "admin":
                include_once("pages/submit_profile_not_student.php");
                break;
            default:
                header("Location: index.php");
                break;
        }
    ?>
</main>

