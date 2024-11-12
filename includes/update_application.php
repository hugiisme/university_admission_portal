<?php
    include_once("../config/database.php");
    header('Content-Type: application/json');

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['application_id'], $_POST['action'])) {
        $application_id = $_POST['application_id']; 
        $action = $_POST['action']; 

        if ($action === 'accept' || $action === 'deny') {
            $status = ($action === 'accept') ? 'Accepted' : 'Denied';

            $query = "UPDATE applications SET status = '$status' WHERE application_id = '$application_id'";

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
    // TODO: early exit, merge with delete_application file
?>
