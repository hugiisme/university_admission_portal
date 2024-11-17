<?php
    $db_server = "localhost";
    $db_user = "root";
    $db_password = "";
    $db_name = "university_admission_portal";

    $conn = mysqli_connect($db_server, $db_user, $db_password, $db_name);
    if (!$conn) {
        die("Không thể kết nối với cơ sở dữ liệu: " . mysqli_connect_error());
        exit();
    }
    date_default_timezone_set('Asia/Ho_Chi_Minh');
?>