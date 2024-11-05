<?php
    include("includes/session.php");
    $_SESSION["currentPage"] = "Trang chủ";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <title>Document</title>
</head>
<body>
    <!-- <?php
        include("includes/header.php");
    ?> -->
    <header>
        <nav>
            <ul>
                <li>Trang chủ</li>
                <li>Nộp hồ sơ</li>
                <li>Xem hồ sơ</li>
                <li>Thống kê hồ sơ</li>
                <li>Thống kê người dùng</li>
                <li>Tài khoản</li>
            </ul>
        </nav>
    </header>
</body>
</html>