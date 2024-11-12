<?php
    include_once("../config/database.php");
    header('Content-Type: application/json');

    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['major_id'])) {
        $majorId = intval($_POST['major_id']);

        $query = "SELECT is_shown FROM majors WHERE major_id = $majorId";
        $result = mysqli_query($conn, $query);
        $row = mysqli_fetch_assoc($result);
        $isShown = $row['is_shown'];

        $newVisibility = $isShown ? 0 : 1;
        $updateQuery = "UPDATE majors SET is_shown = $newVisibility WHERE major_id = $majorId";
        mysqli_query($conn, $updateQuery);

        echo json_encode(['success' => true, 'newVisibility' => $newVisibility ? 0 : 1]);
    } else {
        echo json_encode(['success' => false]);
    }

    // TODO: merge with delete_major file
?>
