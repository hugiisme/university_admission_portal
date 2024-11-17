<?php
    include_once("auth/session.php");
    include_once("config/database.php");
    include_once("includes/add_notification.php");
    include_once("includes/display_notifications.php");
    include_once("includes/pagination.php");

    if($_SESSION["role"] != "admin"){
        header("Location: index.php");
        exit();
    }

    function initializeQueryOptions(){
        return [
            'search_by' => $_GET['search_by'] ?? '',
            'search_value' => $_GET['search_value'] ?? '',
            'sort_by_id' => $_GET['sort_by_id'] ?? '',
            'sort_by_name' => $_GET['sort_by_name'] ?? '',
        ];
    }

    function buildTeacherQuery($search_by, $search_value, $sort_by_id, $sort_by_name){
        $query = "SELECT 
                    u.user_id AS teacher_id,
                    u.username AS teacher_username,
                    u.name AS teacher_name,
                    u.email AS teacher_email,
                    CASE 
                        WHEN COUNT(mt.major_id) = 0 THEN 'Chưa có'
                        ELSE GROUP_CONCAT(m.name ORDER BY m.name SEPARATOR ', ')
                    END AS assigned_majors
                FROM users u
                LEFT JOIN major_teachers mt ON u.user_id = mt.user_id
                LEFT JOIN majors m ON mt.major_id = m.major_id
                WHERE u.role = 'teacher'";


        if($search_by && $search_value){
            $query .= " AND u.$search_by LIKE '%$search_value%'";
        }

        $query .= " GROUP BY u.user_id, u.name";

        $orderByClauses = [];
        if($sort_by_id == "user_id_asc"){
            $orderByClauses[] = "u.user_id ASC";
        } elseif ($sort_by_id == "user_id_desc"){
            $orderByClauses[] = "u.user_id DESC";
        }

        if($sort_by_name == "name_asc"){
            $orderByClauses[] = "u.name ASC";
        } elseif($sort_by_name == "name_desc"){
            $orderByClauses[] = "u.name DESC";
        } 

        if (!empty($orderByClauses)) {
            $query .= " ORDER BY " . implode(", ", $orderByClauses);
        }
        return $query;
    }

    function fetchTeacher($conn, $query, $itemPerPage, $offset){
        $query .= " LIMIT $itemPerPage OFFSET $offset";
        error_log($query);
        $result = mysqli_query($conn, $query);
        return $result;
    }

    function totalResult($conn, $query){
        $totalResult = mysqli_num_rows(mysqli_query($conn, $query)); 
        return $totalResult;
    }

    function updateMT($conn, $major_name, $teacher_id, $action){
        $major_name = mysqli_real_escape_string($conn, $major_name);
        $teacher_id = (int)$teacher_id;
    
        $majorCheckQuery = "SELECT major_id FROM majors WHERE name = '$major_name'";
        $majorCheckResult = mysqli_query($conn, $majorCheckQuery);
    
        if (mysqli_num_rows($majorCheckResult) == 0) {
            add_notification("Ngành học $major_name không tồn tại!", 5000, "error");
            header("Location: " . pageURL());
            exit();
        }
    
        $majorRow = mysqli_fetch_assoc($majorCheckResult);
        $major_id = $majorRow['major_id'];
    
        $teacherNameQuery = "SELECT name FROM users WHERE user_id = $teacher_id";
        $teacherNameResult = mysqli_query($conn, $teacherNameQuery);
        if (mysqli_num_rows($teacherNameResult) == 0) {
            add_notification("Không tìm thấy giáo viên với ID $teacher_id", 5000, "error");
            header("Location: " . pageURL());
            exit();
        }
    
        $teacherRow = mysqli_fetch_assoc($teacherNameResult);
        $teacher_name = $teacherRow['name'];
    
        if ($action == 'add') {
            $checkQuery = "SELECT * 
                           FROM major_teachers 
                           WHERE user_id = $teacher_id 
                               AND major_id = $major_id";
            $checkResult = mysqli_query($conn, $checkQuery);
    
            if (mysqli_num_rows($checkResult) > 0) {
                add_notification("Giáo viên $teacher_name đã được gán ngành $major_name từ trước", 5000, "error");
                header("Location: " . pageURL());
                exit();
            }
    
            $insertQuery = "INSERT INTO major_teachers (user_id, major_id) 
                            VALUES ($teacher_id, $major_id)";
            if (mysqli_query($conn, $insertQuery)) {
                add_notification("Giáo viên $teacher_name được phân công ngành $major_name thành công", 5000, "success");
                header("Location: " . pageURL());
            } else {
                add_notification("Không thể phân công ngành $major_name cho giáo viên $teacher_name", 5000, "error");
                header("Location: " . pageURL() . "&status=error");
            }
    
        } elseif ($action == 'remove') {
            $checkAssignmentQuery = "SELECT * 
                                     FROM major_teachers 
                                     WHERE user_id = $teacher_id 
                                         AND major_id = $major_id";
            $checkAssignmentResult = mysqli_query($conn, $checkAssignmentQuery);
    
            if (mysqli_num_rows($checkAssignmentResult) == 0) {
                add_notification("Giáo viên $teacher_name chưa được phân công ngành $major_name, không thể xóa", 5000, "error");
                header("Location: " . pageURL());
                exit();
            }
    
            $removeQuery = "DELETE FROM major_teachers 
                            WHERE user_id = $teacher_id AND major_id = $major_id";
            if (mysqli_query($conn, $removeQuery)) {
                add_notification("Đã xóa ngành $major_name khỏi giáo viên $teacher_name thành công", 5000, "success");
                header("Location: " . pageURL());
            } else {
                add_notification("Không thể xóa ngành $major_name khỏi giáo viên $teacher_name", 5000, "error");
                header("Location: " . pageURL());
            }
        }
        exit();
    }
    
    function pageURL(){
        $queryOption = initializeQueryOptions();
        $search_by = $queryOption["search_by"];
        $search_value = $queryOption["search_value"];
        $sort_by_id = $queryOption["sort_by_id"];
        $sort_by_name = $queryOption["sort_by_name"];
        $page_index = $_GET["page_index"];
        return "index.php?page=assign_teacher&search_by=$search_by&search_value=$search_value&sort_by_id=$sort_by_id&sort_by_name=$sort_by_name&page_index=$page_index";
    }

    function renderTeacherRow($row){
        echo '<tr>';
            echo "<td>{$row['teacher_id']}</td>";
            echo "<td>{$row['teacher_username']}</td>";
            echo "<td>{$row['teacher_name']}</td>";
            echo "<td>{$row['teacher_email']}</td>";
            echo "<td>{$row['assigned_majors']}</td>";
            echo "<td>
                    <form method='post' onchange='this.submit()' style = 'width: 100%'>
                        <input type='hidden' name='teacher_id' value='{$row['teacher_id']}'>
                        <input type='text' name='major_to_add' style = 'width: 100%'>
                    </form>
                </td>"; 
            echo "<td>
                <form method='post' onchange='this.submit()' style = 'width: 100%'>
                    <input type='hidden' name='teacher_id' value='{$row['teacher_id']}'>
                    <input type='text' name='major_to_remove' style = 'width: 100%'>
                </form>
            </td>"; 
        echo '</tr>';
    }

    if ($_SERVER["REQUEST_METHOD"] === "GET"){
        $queryOption = initializeQueryOptions();
        $search_by = $queryOption["search_by"];
        $search_value = $queryOption["search_value"];
        $sort_by_id = $queryOption["sort_by_id"];
        $sort_by_name = $queryOption["sort_by_name"];
    } elseif($_SERVER["REQUEST_METHOD"] === "POST" && (isset($_POST["major_to_add"]) || isset($_POST["major_to_remove"]))){
        $teacher_id = $_POST["teacher_id"];
        if(isset($_POST["major_to_add"])){
            $action = "add";
            $major_name = $_POST["major_to_add"];
        } elseif(isset($_POST["major_to_remove"])){
            $action = "remove";
            $major_name = $_POST["major_to_remove"];
        }

        updateMT($conn, $major_name, $teacher_id, $action);

    }

    $currentPage = isset($_GET['page_index']) && $_GET['page_index'] > 0 ? (int)$_GET['page_index'] : 1;;;
    $itemPerPage = 10;
    $offset = ($currentPage - 1) * $itemPerPage;

    $teacherQuery = buildTeacherQuery($search_by, $search_value, $sort_by_id, $sort_by_name);
    $teacherResult = fetchTeacher($conn, $teacherQuery, $itemPerPage, $offset);
    $totalResult = totalResult($conn, $teacherQuery);
    $totalPages = ceil($totalResult / $itemPerPage);

    mysqli_close($conn);
?>

<main>
    <h1 class="page-title">Phân ngành giáo viên</h1>

    <form action="" method="get" class="query-option-form" onchange="this.submit()">
        <input type="hidden" name="page" value="assign_teacher">
        <div class="query-option-input">
            <div class="search-container">
                <div class="search-by">
                    <label for="search_by">Tìm kiếm theo: </label>
                    <select name="search_by">
                        <option value="name" <?php echo ($search_by == "" || $search_by == "name") ? "selected" : '' ?>>Tên</option>
                        <option value="email" <?php echo $search_by == "email" ? "selected" : '' ?>>Email</option>
                    </select>
                </div>
                <input type="text" name="search_value" value="<?php echo $search_value ?>">
            </div>
            <div class="filter-sort-option">
                <div class="sort-by">
                    <label for="sort_by">Sắp xếp theo: </label>
                    <select name="sort_by_id" id="">
                        <option value="" <?php echo $sort_by_id == "" ? "selected" : '' ?>>STT</option>
                        <option value="user_id_asc" <?php echo $sort_by_id == "user_id_asc" ? "selected" : '' ?>>STT ⬆️</option>
                        <option value="user_id_desc" <?php echo $sort_by_id == "user_id_desc" ? "selected" : '' ?>>STT ⬇️</option>
                    </select>
                    <select name="sort_by_name" id="">
                        <option value="" <?php echo $sort_by_name == "" ? "selected" : '' ?>>Tên</option>
                        <option value="name_asc" <?php echo $sort_by_name == "name_asc" ? "selected" : '' ?>>Tên ⬆️</option>
                        <option value="name_desc" <?php echo $sort_by_name == "name_desc" ? "selected" : '' ?>>Tên ⬇️</option>
                    </select>
                </div>
            </div>
        </div>
        
    </form>

    <div class="content">
        <div class="total-result">Tổng số kết quả: <span class="info"><?php echo $totalResult ?></span> </div>
        <table class="teacher-table">
            <thead>
                <tr>
                    <th style="width: 50px">STT</th>
                    <th style="width: 150px">Tên người dùng</th>
                    <th style="width: 150px">Tên</th>
                    <th style="width: 150px">Email</th>
                    <th>Ngành được phân công</th>
                    <th style="width: 200px">Ngành cần thêm</th>
                    <th style="width: 200px">Ngành cần xóa</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    if(mysqli_num_rows($teacherResult) > 0){
                        while($row = mysqli_fetch_assoc($teacherResult)){
                            renderTeacherRow($row);
                        }
                    } else {
                        echo "<tr><td colspan='5' class='warning' style='text-align: center; font-size:larger; font-weight: bold;'>Không có kết quả phù hợp</td></tr>";
                    }
                ?>
            </tbody>
        </table>
    </div>

    <div class='pagination'>
        <?php createPagination($currentPage, $totalPages) ?>
    </div>    
        
</main>


