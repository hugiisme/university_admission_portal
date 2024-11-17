<?php
    include_once("../config/database.php");
    header('Content-Type: application/json');
    
    if($_SESSION["role"] == "student"){
        header("Location: index.php");
        exit();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['application_id'], $_POST['action'])) {
        $application_id = $_POST['application_id']; 
        $action = $_POST['action']; 

        if ($action === 'accept' || $action === 'deny') {
            $status = ($action === 'accept') ? 'Accepted' : 'Denied';

            $userId = $_SESSION['user_id'];
            $query = "UPDATE applications SET verifier_id = $userId, status = '$status' WHERE application_id = '$application_id'";

            if (mysqli_query($conn, $query)) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Không thể thực thi truy vấn.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Hành động không hợp lệ.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Thiếu dữ liệu.']);
    }
?>
