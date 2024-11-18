<?php
    include_once("../config/database.php");
    if (!isset($_GET["page"])){
        header("Location: ../index.php");
        exit();
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['major_id'])) {
        $majorId = intval($_POST['major_id']);
        $checkQuery = "SELECT * FROM majors WHERE major_id = $majorId";
        $checkResult = mysqli_query($conn, $checkQuery);
        
        if (mysqli_num_rows($checkResult) > 0) {
            $deleteQuery = "DELETE FROM majors WHERE major_id = $majorId";
            
            if (mysqli_query($conn, $deleteQuery)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Major ID not found']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid request']);
    }
?>
