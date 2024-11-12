<?php
    include_once("includes/session.php");
    include_once("includes/add_notification.php");
    include_once("includes/display_notifications.php");
    if(isset($_GET["page"])){
        $page = $_GET["page"];
    } else {
        $page = "home";
    }

    switch($page){
        case "home":
            $title = "Trang chủ";
            $content = "pages/home.php";
            break;
        case "submit_profile":
            $title = "Nộp hồ sơ";
            $content = "pages/submit_profile.php";
            break;
        case "profile_detail":
            $title = "Xem hồ sơ";
            $content = "pages/profile_detail.php";
            break;
        case "profile_statistic":
            $title = "Thống kê hồ sơ";
            $content = "pages/profile_statistic.php";
            break;
        case "user_statistic":
            $title = "Thống kê người dùng";
            $content = "pages/user_statistic.php";
            break;
        case "account_detail":
            $title = "Tài khoản";
            $content = "pages/account_detail.php";
            break;
        default: // luôn về trang chủ nếu không tìm thấy trang
            $title = "Trang chủ";
            $content = "pages/home.php";
            break;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/notification.css">
    <link rel="stylesheet" href="assets/css/navigation.css">
    <link rel="stylesheet" href="assets/css/home.css">
    <link rel="stylesheet" href="assets/css/submit-profile.css">
    <link rel="stylesheet" href="assets/css/account-info.css">
    <script src="assets/js/notification.js" defer></script>
    <script src="assets/js/navigation.js"></script>
</head>
<body>
    <div class="container">
        <?php
            include("includes/navigation.php");
            include($content);
        ?>
    </div>
    <div class="notifications">
        <?php display_notifications(); ?>
    </div>
</body>
</html>