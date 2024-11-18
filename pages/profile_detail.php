<link rel="stylesheet" href="assets/css/profile-detail.css">

<?php 
    
    include_once("auth/session.php");
    include_once("config/database.php");
    include_once("includes/add_notification.php");
    include_once("includes/display_notifications.php");

    function getApplicationDetail($conn, $application_id){
        $query = "SELECT 
                        a.status AS status,
                        a.profile_picture AS profile_picture,
                        u.name AS student_name, 
                        m.name AS major_name, 
                        b.code AS block_name, 
                        s.name as subject_names,
                        ascore.score as scores
                    FROM applications a
                    JOIN application_scores ascore ON a.application_id = ascore.application_id
                    JOIN users u ON a.student_id = u.user_id
                    JOIN majors m ON a.major_id = m.major_id
                    JOIN blocks b ON a.block_id = b.block_id
                    JOIN subjects s ON ascore.subject_id = s.subject_id
                    WHERE a.application_id = '$application_id'";
        $result = mysqli_query($conn, $query);
        return $result;
    }
    
    function renderApplicationDetail($application_id, $student_name, $status, $major_name, $block_name, $profile_picture, $subjects_scores){
        echo "<div>Mã hồ sơ: <span class='info'>" . $application_id . "</span></div>";
        echo "<div>Tên học sinh: <span class='info'>" . htmlspecialchars($student_name) . "</span></div>";
        echo "<div>Trạng thái xét duyệt: <span class='info'>" . htmlspecialchars($status) . "</span></div>";
        echo "<div>Tên ngành: <span class='info'>" . htmlspecialchars($major_name) . "</span></div>";
        echo "<div>Tên khối xét tuyển: <span class='info'>" . htmlspecialchars($block_name) . "</span></div>";
        foreach ($subjects_scores as $subject_score) {
            echo "<div>Điểm " . htmlspecialchars($subject_score['subject']) . ": <span class='info'>" . htmlspecialchars($subject_score['score']) . "</span></div>";
        }
        echo "<div>";
            echo "<h3>Ảnh hồ sơ</h3>";
            echo "<img src='$profile_picture' alt='Ảnh hồ sơ' style='width: 500px;'>";
        echo "<div>";
    }

    $application_id = $_POST["application_id"] ?? '';
    if(!$application_id){
        add_notification("Hồ sơ không tồn tại", 5000, "error");
        header("Location: index.php");
    } else {
        $result = getApplicationDetail($conn, $application_id);
        
    }
    mysqli_close($conn);
?>

<main>
    <h1 class="page-title">Thông tin hồ sơ</h1>
    <div class="profile-wrapper">
        <div class="profile-info">
            <?php
                if ($result) {
                    $subjects_scores = [];

                    while ($row = mysqli_fetch_assoc($result)) {
                        $student_name = $row['student_name'];
                        $major_name = $row['major_name'];
                        $block_name = $row['block_name'];
                        $profile_picture = "uploads/application_images/" . $row['profile_picture'];
                        

                        $subjects_scores[] = [
                            'subject' => $row['subject_names'],
                            'score' => $row['scores']
                        ];

                        switch($row["status"]){
                            case "pending":
                                $status = "Đang chờ duyệt";
                                break;
                            case "accepted":
                                $status = "Đã duyệt";
                                break;
                            case "denied":
                                $status = "Từ chối";
                                break;
                        }
                    }
                    renderApplicationDetail($application_id, $student_name, $status, $major_name, $block_name, $profile_picture, $subjects_scores);
                } else {
                    echo "Error: " . mysqli_error($conn);
                }
            ?>
        </div>
        
    </div>
    
</main>


