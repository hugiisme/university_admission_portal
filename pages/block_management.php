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
            'search_value' => $_GET['search_value'] ?? '',
            'sort_by_id' => $_GET['sort_by_id'] ?? '',
            'sort_by_code' => $_GET['sort_by_code'] ?? '',
            'filter_by_subject' => $_GET['filter_by_subject'] ?? '',
        ];
    }

    function fetchSubjects($conn){
        $query = "SELECT * FROM subjects";
        $result = mysqli_query($conn, $query);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    function buildBlockQuery($search_value, $sort_by_id, $sort_by_code, $filter_by_subject){
        $query = "  SELECT b.block_id AS block_id, 
                            b.code AS block_code, 
                    CASE 
                        WHEN COUNT(bs.subject_id) = 0 THEN 'Chưa có' 
                        ELSE GROUP_CONCAT(s.name ORDER BY s.name SEPARATOR ', ')
                    END AS subjects
                    FROM blocks b
                    LEFT JOIN block_subjects bs ON b.block_id = bs.block_id
                    LEFT JOIN subjects s ON s.subject_id = bs.subject_id
                    WHERE 1=1";

        if ($search_value) {
            $query .= " AND b.code LIKE '%$search_value%'";
        }

        if($filter_by_subject){
            $query .= " AND b.block_id IN (
                SELECT DISTINCT bs.block_id
                FROM block_subjects bs
                JOIN subjects s ON bs.subject_id = s.subject_id
                WHERE s.name = '$filter_by_subject'
            )";
        }

        $query .= " GROUP BY b.block_id";
        $orderByClauses = [];

        if ($sort_by_id === 'block_id_asc') {
            $orderByClauses[] = 'b.block_id ASC';
        } elseif ($sort_by_id === 'block_id_desc') {
            $orderByClauses[] = 'b.block_id DESC';
        }

        if ($sort_by_code === 'code_asc') {
            $orderByClauses[] = 'b.code ASC'; 
        } elseif ($sort_by_code === 'code_desc') {
            $orderByClauses[] = 'b.code DESC'; 
        }

        if (!empty($orderByClauses)) {
            $query .= " ORDER BY " . implode(", ", $orderByClauses);
        }
        return $query;
    }

    function fetchBlock($conn, $query, $itemPerPage, $offset){
        $query .= " LIMIT $itemPerPage OFFSET $offset";
        $result = mysqli_query($conn, $query);
        return $result;
    }

    function totalResult($conn, $query){
        $totalResult = mysqli_num_rows(mysqli_query($conn, $query)); 
        return $totalResult;
    }

    function updateBS($conn, $block_code, $subject_name, $action) {
        $block_code = mysqli_real_escape_string($conn, $block_code);
        $subject_name = mysqli_real_escape_string($conn, $subject_name);
    
        $blockCheckQuery = "SELECT block_id FROM blocks WHERE code = '$block_code'";
        $blockCheckResult = mysqli_query($conn, $blockCheckQuery);
    
        if (mysqli_num_rows($blockCheckResult) == 0) {
            add_notification("Block với mã $block_code không tồn tại!", 5000, "error");
            header("Location: " . pageURL());
            exit();
        }
    
        $blockRow = mysqli_fetch_assoc($blockCheckResult);
        $block_id = $blockRow['block_id'];
    
        $subjectCheckQuery = "SELECT subject_id FROM subjects WHERE name = '$subject_name'";
        $subjectCheckResult = mysqli_query($conn, $subjectCheckQuery);
    
        if (mysqli_num_rows($subjectCheckResult) == 0) {
            add_notification("Môn học $subject_name không tồn tại!", 5000, "error");
            header("Location: " . pageURL());
            exit();
        }
    
        $subjectRow = mysqli_fetch_assoc($subjectCheckResult);
        $subject_id = $subjectRow['subject_id'];
    
        if ($action == 'add') {
            $checkQuery = "SELECT * FROM block_subjects WHERE block_id = $block_id AND subject_id = $subject_id";
            $checkResult = mysqli_query($conn, $checkQuery);
    
            if (mysqli_num_rows($checkResult) > 0) {
                add_notification("Môn học $subject_name đã được gán cho block $block_code từ trước", 5000, "error");
                header("Location: " . pageURL());
                exit();
            }
    
            $insertQuery = "INSERT INTO block_subjects (block_id, subject_id) VALUES ($block_id, $subject_id)";
            if (mysqli_query($conn, $insertQuery)) {
                add_notification("Môn học $subject_name được gán vào block $block_code thành công", 5000, "success");
                header("Location: " . pageURL());
            } else {
                add_notification("Không thể gán môn học $subject_name vào block $block_code", 5000, "error");
                header("Location: " . pageURL() . "&status=error");
            }
    
        } elseif ($action == 'remove') {
            $checkAssociationQuery = "SELECT * FROM block_subjects WHERE block_id = $block_id AND subject_id = $subject_id";
            $checkAssociationResult = mysqli_query($conn, $checkAssociationQuery);
    
            if (mysqli_num_rows($checkAssociationResult) == 0) {
                add_notification("Môn học $subject_name chưa được gán vào block $block_code, không thể xóa", 5000, "error");
                header("Location: " . pageURL());
                exit();
            }
    
            $removeQuery = "DELETE FROM block_subjects WHERE block_id = $block_id AND subject_id = $subject_id";
            if (mysqli_query($conn, $removeQuery)) {
                add_notification("Đã xóa môn học $subject_name khỏi block $block_code thành công", 5000, "success");
                header("Location: " . pageURL());
            } else {
                add_notification("Không thể xóa môn học $subject_name khỏi block $block_code", 5000, "error");
                header("Location: " . pageURL());
            }
        }
        exit();
    }
    
    function pageURL(){
        $queryOption = initializeQueryOptions();
        $search_value = $queryOption["search_value"];
        $sort_by_id = $queryOption["sort_by_id"];
        $sort_by_code = $queryOption["sort_by_code"];
        $filter_by_subject = $queryOption["filter_by_subject"];
        $page_index = $_GET['page_index'];
        return "index.php?page=block_management&search_value=$search_value&sort_by_id=$sort_by_id&sort_by_code=$sort_by_code&filter_by_subject=$filter_by_subject&page_index=$page_index";
    }

    function createNewBlock($conn, $block_code){
        $block_code = mysqli_real_escape_string($conn, $block_code);

        $checkQuery = "SELECT block_id FROM blocks WHERE code = '$block_code'";
        $checkResult = mysqli_query($conn, $checkQuery);
    
        if (mysqli_num_rows($checkResult) > 0) {
            add_notification("Khối $block_code đã tồn tại!", 5000, "error");
            header("Location: " . pageURL());
            exit();
        }
    
        $insertQuery = "INSERT INTO blocks (code) VALUES ('$block_code')";
        if (mysqli_query($conn, $insertQuery)) {
            add_notification("Tạo khối $block_code thành công!", 5000, "success");
            header("Location: " . pageURL());
        } else {
            add_notification("Không thể tạo khối $block_code.", 5000, "error");
            header("Location: " . pageURL());
        }
        exit();
    }

    function renderBlockRow($row){
        echo "<tr>";
            echo "<td>{$row['block_id']}</td>";
            echo "<td>{$row['block_code']}</td>";
            echo "<td>{$row['subjects']}</td>";
            echo "<td>
                <form method='post' onchange='this.submit()' style='width: 100%'>
                    <input type='hidden' name='block_code' value='{$row['block_code']}'>
                    <input type='text' name='subject_name_to_add' style='width:100%'>
                </form>
                </td>";
            echo "<td>
                <form method='post' onchange='this.submit()' style='width: 100%'>
                    <input type='hidden' name='block_code' value='{$row['block_code']}'>
                    <input type='text' name='subject_name_to_remove' style='width:100%'>
                </form>
                </td>";
        echo "</tr>";
    }

    if ($_SERVER["REQUEST_METHOD"] === "GET"){
        $queryOption = initializeQueryOptions();
        $search_value = $queryOption["search_value"];
        $sort_by_id = $queryOption["sort_by_id"];
        $sort_by_code = $queryOption["sort_by_code"];
        $filter_by_subject = $queryOption["filter_by_subject"];
    } elseif ($_SERVER["REQUEST_METHOD"] === "POST"){
        if(isset($_POST["subject_name_to_add"]) || isset($_POST["subject_name_to_remove"])){
            $block_code = $_POST["block_code"];
            if(isset($_POST["subject_name_to_add"])){
                $action = "add";
                $subject_name = $_POST["subject_name_to_add"];
            } elseif (isset($_POST["subject_name_to_remove"])){
                $action = "remove";
                $subject_name = $_POST["subject_name_to_remove"];
            }
    
            updateBS($conn, $block_code, $subject_name, $action);
        } elseif(isset($_POST["new_block"])){
            createNewBlock($conn, $_POST["new_block"]);
        }
        
    }

    $currentPage = isset($_GET['page_index']) ? (int)$_GET['page_index'] : 1;;
    $itemPerPage = 8;
    $offset = ($currentPage - 1) * $itemPerPage;

    $blockQuery = buildBlockQuery($search_value, $sort_by_id, $sort_by_code, $filter_by_subject);
    $blockResult = fetchBlock($conn, $blockQuery, $itemPerPage, $offset);
    $totalResult = totalResult($conn, $blockQuery);
    $totalPages = ceil($totalResult / $itemPerPage);
    $subjectRows = fetchSubjects($conn);

    mysqli_close($conn);

?>
<main>
    <h1 class="page-title">Quản lý khối</h1>

    <form action="" method="get" class="query-option-form" onchange="this.submit()">
        <input type="hidden" name="page" value="block_management">
        <div class="query-option-input">
            <div class="search-container">
                <label for="search_by">Tìm kiếm theo mã khối: </label>
                <input type="text" name="search_value" value="<?php echo $search_value ?>">
            </div>
            <div class="filter-sort-option">
                <div class="sort-by">
                    <label for="sort_by">Sắp xếp theo: </label>
                    <select name="sort_by_id" id="">
                        <option value="" <?php echo $sort_by_id == "" ? "selected" : '' ?>>STT</option>
                        <option value="block_id_asc" <?php echo $sort_by_id == "block_id_asc" ? "selected" : '' ?>>STT ⬆️</option>
                        <option value="block_id_desc" <?php echo $sort_by_id == "block_id_desc" ? "selected" : '' ?>>STT ⬇️</option>
                    </select>
                    <select name="sort_by_code" id="">
                        <option value="" <?php echo $sort_by_code == "" ? "selected" : '' ?>>Tên</option>
                        <option value="code_asc" <?php echo $sort_by_code == "code_asc" ? "selected" : '' ?>>Tên khối ⬆️</option>
                        <option value="code_desc" <?php echo $sort_by_code == "code_desc" ? "selected" : '' ?>>Tên khối ⬇️</option>
                    </select>
                </div>
                <div class="filter-by">
                    <label for="filter-by">Lọc theo</label>
                    <select name="filter_by_subject">
                        <option value="">Môn học</option>
                        <?php 
                            foreach ($subjectRows as $subject) {
                                $subject_name = $subject["name"];
                                echo "<option value='$subject_name' " . ((isset($_GET['filter_by_subject']) && $_GET['filter_by_subject'] == $subject_name) ? "selected" : '') . ">$subject_name</option>";
                            }
                        ?>
                    </select>
                </div>
            </div>
        </div>
    </form>

    <div class="content">
        <div class="total-result">Tổng số kết quả: <span class="info"><?php echo $totalResult ?></span> </div>
        <table>
            <thead>
                <tr>
                    <th style="width: 50px">STT</th>
                    <th style="width: 150px">Mã khối</th>
                    <th>Môn học</th>
                    <th>Thêm môn học</th>
                    <th>Xóa môn học</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    if(mysqli_num_rows($blockResult) > 0){
                        while($row = mysqli_fetch_assoc($blockResult)){
                            renderBlockRow($row);
                        }
                    } else {
                        echo "<tr><td colspan='5' class='warning' style='text-align: center; font-size:larger; font-weight: bold;'>Không có kết quả phù hợp</td></tr>";
                    }
                ?>
            </tbody>
        </table>
    </div>
    <div class="create-new-block" style="margin:20px 0px">
        <form action="" method="post">
            <label for="">Tạo khối mới</label>
            <input type="text" name="new_block">
            <input type="submit" value="Tạo khối mới">
        </form>
    </div>
    <div class='pagination'>
        <?php createPagination($currentPage, $totalPages) ?>
    </div>   
</main>

