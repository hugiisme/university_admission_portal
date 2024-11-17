<?php
    include_once("auth/session.php");
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
        case "assign_teacher":
            $title = "Phân ngành giáo viên";
            $content = "pages/assign_teacher.php";
            break;
        case "major_management":
            $title = "Quản lý ngành";
            $content = "pages/major_management.php";
            break;
        case "block_management":
            $title = "Quản lý khối";
            $content = "pages/block_management.php";
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
    
    
    
    <link rel="stylesheet" href="assets/css/pagination.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="assets/js/notification.js" defer></script>
    <script src="assets/js/navigation.js"></script>
    <script src="assets/js/toggle-password.js"></script>
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