<?php
    include_once("../config/database.php");
    header('Content-Type: application/json');

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['application_id'])) {
        $application_id = $_POST['application_id']; 

        $getImagePathQuery = "SELECT profile_picture FROM applications WHERE application_id = '$application_id'";
        $getImagePathResult = mysqli_query($conn, $getImagePathQuery);
        if ($getImagePathResult && mysqli_num_rows($getImagePathResult) > 0) {
            $imagePathRow = mysqli_fetch_assoc($getImagePathResult);
            $imagePath = "../uploads/" . $imagePathRow['profile_picture'];

            if (file_exists($imagePath)) {
                unlink($imagePath); 
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Không thể tìm thấy đường dẫn tới ảnh hồ sơ.']);
            exit();
        }

        $query = "DELETE FROM applications WHERE application_id = '$application_id'";

        if (mysqli_query($conn, $query)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Không thể thực hiện truy vấn.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Thiếu dữ liệu.']);
    }

?>
