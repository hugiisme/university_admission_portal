<?php 
    include_once("includes/session.php");
    include_once("config/database.php");
    include_once("includes/add_notification.php");
    include_once("includes/display_notifications.php");

    $major_id = isset($_GET["major_id"]) ? $_GET["major_id"] : '0';
    $user_id = $_SESSION["user_id"];

    $findMajorQuery = " SELECT name,
                        CASE 
                            WHEN CURDATE() BETWEEN start_date AND end_date THEN 1 
                            ELSE 0 
                        END AS is_current
                        FROM majors 
                        WHERE major_id = '$major_id'";
    $findMajorResult = mysqli_query($conn, $findMajorQuery);
    if (!$findMajorResult){
        add_notification("Lỗi query: " . mysqli_error($conn), 5000, "error");
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } elseif (mysqli_num_rows($findMajorResult) == 0){
        add_notification("Không tìm thấy ngành này", 5000, "error");
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $majorRows = mysqli_fetch_assoc($findMajorResult);
        $major_name = $majorRows["name"];
    }

    $allowCreateApplicationQuery = "SELECT * FROM applications WHERE student_id = '$user_id' AND major_id = '$major_id'";
    $allowCreateApplicationResult = mysqli_query($conn, $allowCreateApplicationQuery);
    if(!$allowCreateApplicationResult){
        add_notification("Lỗi query: " . mysqli_error($conn), 5000, "error");
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
    elseif(mysqli_num_rows($allowCreateApplicationResult) > 0){
        add_notification("Không thể nộp lại hồ sơ cho ngành này", 5000, "error");
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } elseif (!$majorRows["is_current"]){
        add_notification("Không trong khoảng thời gian cho phép nộp hồ sơ ngành này", 5000, "error");
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    $findBlockQuery = " SELECT b.block_id, b.code 
                        FROM blocks b
                        JOIN major_blocks mb ON b.block_id = mb.block_id
                        WHERE mb.major_id = '$major_id'";
    $findBlockResult = mysqli_query($conn, $findBlockQuery);
    if (!$findBlockResult){
        add_notification("Lỗi query: " . mysqli_error($conn), 5000, "error");
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } elseif (mysqli_num_rows($findBlockResult) == 0) {
        add_notification("Ngành chưa có khối xét tuyển không thể đăng ký", 5000, "error");
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $blocks_info = mysqli_fetch_all($findBlockResult, MYSQLI_ASSOC);
    }

    $selectedBlockId = isset($_POST['block_choosen']) ? intval($_POST['block_choosen']) : 0;

    $subjects_info = [];
    if($selectedBlockId){
        $subjectQuery = "SELECT s.name, s.subject_id
                        FROM subjects s
                        JOIN block_subjects bs ON s.subject_id = bs.subject_id
                        WHERE bs.block_id = '$selectedBlockId'";
        $subjectResult = mysqli_query($conn, $subjectQuery);
        if(!$subjectResult){
            add_notification("Lỗi query: " . mysqli_error($conn), 5000, "error");
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } elseif (mysqli_num_rows($subjectResult) == 0) {
            add_notification("Khối chưa gán môn học không thể chọn", 5000, "error");
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } else {
            $subjects_info = mysqli_fetch_all($subjectResult, MYSQLI_ASSOC);
        }
    }

    if(isset($_POST["create_application_btn"])){
        $block_id = $_POST["block_id"];
        $createApplicationQuery = "INSERT INTO applications (student_id,     major_id,    block_id)
                                                    VALUES ('$user_id', '$major_id', '$block_id')";
        $createApplicationResult = mysqli_query($conn, $createApplicationQuery);
        if(!$createApplicationResult){
            add_notification("Lỗi query: " . mysqli_error($conn), 5000, "error");
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } else {
            $application_id = mysqli_insert_id($conn);
            foreach($_POST["subjects_score"] as $subject_id => $subject_score){
                $insertScoreQuery = "INSERT INTO application_scores (application_id, subject_id, score) VALUES ($application_id, $subject_id, $subject_score)";
                $insertScoreResult = mysqli_query($conn, $insertScoreQuery);
                if(!$insertScoreResult){
                    add_notification("Lỗi query: " . mysqli_error($conn), 5000, "error");
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit;
                } else {
                    add_notification("Thêm điểm cho id:" . $subject_id . " - Điểm:  " . $subject_score . " thành công", 3000, "success");
                }
            }

            $targetDir = "uploads/";

            // nếu folder chưa tồn tại thì tạo
            // if (!is_dir($targetDir)) {
            //     mkdir($targetDir, 0755, true);
            // }

            $file = $_FILES['profile_picture'];
            $fileName = $file['name'];
            $fileTmpName = $file['tmp_name'];
            $fileSize = $file['size'];
            $fileError = $file['error'];

            $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png'];
            $maxSize = 100 * 1024 * 1024; // 100 MB

            if (in_array($fileExt, $allowed) && $fileSize <= $maxSize) {
                if ($fileError === 0) {
                    
                    $baseName = pathinfo($fileName, PATHINFO_FILENAME);
                    $newFileName = $baseName;
                    $counter = 1;
        
                    while (file_exists($targetDir . $newFileName . "." . $fileExt)) {
                        $newFileName = $baseName . "_" . $counter;
                        $counter++;
                    }
        
                    $newFileName .= "." . $fileExt;
                    $targetPath = $targetDir . $newFileName;
        
                    if (move_uploaded_file($fileTmpName, $targetPath)) {
                        $updateApplicationQuery = "UPDATE applications SET profile_picture = '$newFileName' WHERE application_id = '$application_id'";
                        $updateApplicationResult = mysqli_query($conn, $updateApplicationQuery);
        
                        if (!$updateApplicationResult) {
                            add_notification("Lỗi query: " . mysqli_error($conn), 5000, "error");
                            header("Location: " . $_SERVER['PHP_SELF']);
                            exit;
                        } else {
                            add_notification("Hồ sơ đã được thêm thành công và file đã được tải lên", 3000, "success");
                        }
                    } else {
                        add_notification("Có lỗi khi di chuyển file vào thư mục", 3000, "error");
                    }
                } else {
                    add_notification("Có lỗi khi upload file: " . $fileError, 3000, "error");
                }
            } else {
                add_notification("File nộp phải dưới 100mb và có định dạng .jpg hoặc png", 3000, "error");
            }

            header("Location: " . $_SERVER['PHP_SELF']);
            exit; 
        }
        
    }
    mysqli_close($conn);
?>
<div class="submit-profile-wrapper">
    <div class="submit-profile-container">
        <h1 class="page-title">Nộp hồ sơ</h1>
        <div class="major-name">Ngành xét tuyển: <span class="info"><?php echo $major_name ?></span></div>
        <form action="" method="post" class="blocks-form">
            <label for="block_choosen">Chọn khối xét tuyển</label>
            <select name="block_choosen" onchange="this.form.submit()">
                <option value="">Chọn khối</option>
                <?php 
                    foreach($blocks_info as $block_info){
                        $block_id = $block_info["block_id"];
                        $block_code = $block_info["code"];
                        $selected = $block_id == $selectedBlockId ? "selected" : '';
                        echo "<option value='$block_id' $selected>$block_code</option>";
                    }
                ?>
            </select>
            
        </form>
        <form action="" method="post" class="score-submit-form" enctype="multipart/form-data">
            <input type="hidden" name="major_id" value="<?php echo $major_id; ?>">
            <input type="hidden" name="block_id" value="<?php echo $selectedBlockId; ?>">
            
            
            <?php
                if ($selectedBlockId) {
                    foreach($subjects_info as $subject_info){
                        $subject_id = $subject_info["subject_id"];
                        $subject_name = $subject_info["name"];
                        echo "<div class='subject-container'>";
                            echo "<label for='subject_$subject_id'>Điểm <span class='info'>$subject_name</span>: </label>";
                            echo "<input type='number' name='subjects_score[$subject_id]' class='subject_input' min='0' max='10' step='any' required>";
                        echo "</div>";
                    }
                    echo "<div class='input-container'>";
                        echo "<label for='profile_picture'>Upload ảnh học bạ <span class='info'>(JPG/PNG, <100MB)</span>:</label>";
                        echo "<input type='file' name='profile_picture' accept='.jpg, .jpeg, .png' required>";
                    echo "</div>";
                    echo "<input type='submit' name='create_application_btn' class='create-application-btn' value='Nộp hồ sơ'>";
                } else {
                    echo '<h2 class="warning">Vui lòng chọn khối xét tuyển</h2>';
                }
            ?>
        </form>
    </div>
</div>