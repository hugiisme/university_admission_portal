<link rel="stylesheet" href="assets/css/submit-profile.css">
<?php
    if (!isset($_GET["page"])){
        header("Location: ../index.php");
        exit();
    }
    include_once("auth/session.php");
?>
<main>    
    <?php 
        $user_role = $_SESSION["role"];
        switch ($user_role) {
            case 'student':
                include_once("pages/application_form.php");
                break;
            case 'teacher':
            case "admin":
                include_once("pages/applications_statistic.php");
                break;
            default:
                header("Location: index.php");
                break;
        }
    ?>
</main>

