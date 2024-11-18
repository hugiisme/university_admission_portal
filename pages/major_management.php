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
            'sort_by_name' => $_GET['sort_by_name'] ?? '',
            'sort_by_time_left' => $_GET['sort_by_time_left'] ?? '',
            'filter_by_status' => $_GET['filter_by_status'] ?? '',
            'filter_by_blocks' => $_GET['filter_by_blocks'] ?? '',
        ];
    }

    function fetchBlocks($conn){
        $query = "SELECT * FROM blocks";
        $result = mysqli_query($conn, $query);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    function buildMajorQuery($search_value, $sort_by_id, $sort_by_name, $sort_by_time_left, $filter_by_status, $filter_by_blocks){
        $query = "SELECT
                    m.major_id AS major_id,
                    m.name AS major_name,
                    m.start_date AS start_date,
                    m.end_date AS end_date,
                    CASE 
                        WHEN m.end_date < NOW() THEN 0 
                        ELSE TIMESTAMPDIFF(SECOND, NOW(), m.end_date) 
                    END AS time_left,
                    CASE 
                        WHEN COUNT(mb.block_id) = 0 THEN 'Chưa có' 
                        ELSE GROUP_CONCAT(b.code ORDER BY b.code SEPARATOR ', ')
                    END AS blocks
                  FROM majors m
                  LEFT JOIN major_blocks mb ON m.major_id = mb.major_id
                  LEFT JOIN blocks b ON b.block_id = mb.block_id
                  WHERE 1=1";
        if ($search_value) {
            $query .= " AND m.name LIKE '%$search_value%'";
        }

        if($filter_by_blocks){
            $query .= " AND m.major_id IN (
                SELECT DISTINCT mb.major_id
                FROM major_blocks mb
                JOIN blocks b ON mb.block_id = b.block_id
                WHERE b.code = '$filter_by_blocks'
            )";
        }         

        if ($filter_by_status == 'early') {
            $query .= " AND m.start_date > NOW()";
        } elseif ($filter_by_status == 'on time') {
            $query .= " AND m.start_date <= NOW() AND m.end_date >= NOW()";
        } elseif ($filter_by_status == 'late') {
            $query .= " AND m.end_date < NOW()";
        }

        $query .= " GROUP BY m.major_id";

        $orderByClauses = [];

        if ($sort_by_id === 'major_id_asc') {
            $orderByClauses[] = 'm.major_id ASC';
        } elseif ($sort_by_id === 'major_id_desc') {
            $orderByClauses[] = 'm.major_id DESC';
        }

        if ($sort_by_time_left === 'time_left_asc') {
            $orderByClauses[] = 'time_left ASC'; 
        } elseif ($sort_by_time_left === 'time_left_desc') {
            $orderByClauses[] = 'time_left DESC'; 
        }

        if ($sort_by_name === 'name_asc') {
            $orderByClauses[] = 'm.name ASC'; 
        } elseif ($sort_by_name === 'name_desc') {
            $orderByClauses[] = 'm.name DESC'; 
        }

        if (!empty($orderByClauses)) {
            $query .= " ORDER BY " . implode(", ", $orderByClauses);
        }

        
        return $query;
    }

    function fetchMajor($conn, $query, $itemPerPage, $offset){
        $query .= " LIMIT $itemPerPage OFFSET $offset";
        $result = mysqli_query($conn, $query);
        return $result;
    }

    function totalResult($conn, $query){
        error_log($query);
        $totalResult = mysqli_num_rows(mysqli_query($conn, $query)); 
        return $totalResult;
    }
    
    function updateMB($conn, $major_name, $block_name, $action) {
        $major_name = mysqli_real_escape_string($conn, $major_name);
        $block_name = mysqli_real_escape_string($conn, $block_name);
    
        $majorCheckQuery = "SELECT major_id FROM majors WHERE name = '$major_name'";
        $majorCheckResult = mysqli_query($conn, $majorCheckQuery);
    
        if (mysqli_num_rows($majorCheckResult) == 0) {
            add_notification("Chuyên ngành $major_name không tồn tại!", 5000, "error");
            header("Location: " . pageURL());
            exit();
        }
    
        $majorRow = mysqli_fetch_assoc($majorCheckResult);
        $major_id = $majorRow['major_id'];
    
        $blockCheckQuery = "SELECT block_id FROM blocks WHERE code = '$block_name'";
        $blockCheckResult = mysqli_query($conn, $blockCheckQuery);
    
        if (mysqli_num_rows($blockCheckResult) == 0) {
            add_notification("Block với mã $block_name không tồn tại!", 5000, "error");
            header("Location: " . pageURL());
            exit();
        }
    
        $blockRow = mysqli_fetch_assoc($blockCheckResult);
        $block_id = $blockRow['block_id'];
    
        if ($action == 'add') {
            $checkAssociationQuery = "SELECT * FROM major_blocks WHERE major_id = $major_id AND block_id = $block_id";
            $checkAssociationResult = mysqli_query($conn, $checkAssociationQuery);
    
            if (mysqli_num_rows($checkAssociationResult) > 0) {
                add_notification("Block $block_name đã được gán cho chuyên ngành $major_name từ trước", 5000, "error");
                header("Location: " . pageURL());
                exit();
            }
    
            $insertQuery = "INSERT INTO major_blocks (major_id, block_id) VALUES ($major_id, $block_id)";
            if (mysqli_query($conn, $insertQuery)) {
                add_notification("Block $block_name được gán vào chuyên ngành $major_name thành công", 5000, "success");
                header("Location: " . pageURL());
            } else {
                add_notification("Không thể gán block $block_name vào chuyên ngành $major_name", 5000, "error");
                header("Location: " . pageURL() . "&status=error");
            }
    
        } elseif ($action == 'remove') {
            $checkAssociationQuery = "SELECT * FROM major_blocks WHERE major_id = $major_id AND block_id = $block_id";
            $checkAssociationResult = mysqli_query($conn, $checkAssociationQuery);
    
            if (mysqli_num_rows($checkAssociationResult) == 0) {
                add_notification("Block $block_name chưa được gán cho chuyên ngành $major_name, không thể xóa", 5000, "error");
                header("Location: " . pageURL());
                exit();
            }
    
            $removeQuery = "DELETE FROM major_blocks WHERE major_id = $major_id AND block_id = $block_id";
            if (mysqli_query($conn, $removeQuery)) {
                add_notification("Đã xóa block $block_name khỏi chuyên ngành $major_name thành công", 5000, "success");
                header("Location: " . pageURL());
            } else {
                add_notification("Không thể xóa block $block_name khỏi chuyên ngành $major_name", 5000, "error");
                header("Location: " . pageURL());
            }
        }
        exit();
    }

    function pageURL(){
        $queryOption = initializeQueryOptions();
        $search_value = $queryOption["search_value"];
        $sort_by_id = $queryOption["sort_by_id"];
        $sort_by_name = $queryOption["sort_by_name"];
        $sort_by_time_left = $queryOption["sort_by_time_left"];
        $filter_by_status = $queryOption["filter_by_status"];
        $filter_by_blocks = $queryOption["filter_by_blocks"];
        return "index.php?page=major_management&search_value=$search_value&sort_by_id=$sort_by_id&sort_by_name=$sort_by_name&sort_by_time_left=$sort_by_time_left&filter_by_status=$filter_by_status&filter_by_blocks=$filter_by_blocks";
    }

    function createNewMajor($conn, $major_name) {
        $major_name = mysqli_real_escape_string($conn, $major_name);
        
        $current_date = date('Y-m-d'); 
        
        $checkQuery = "SELECT major_id FROM majors WHERE name = '$major_name'";
        $checkResult = mysqli_query($conn, $checkQuery);
    
        if (mysqli_num_rows($checkResult) > 0) {
            add_notification("Chuyên ngành $major_name đã tồn tại!", 5000, "error");
            header("Location: " . pageURL());
            exit();
        }
    
        $insertQuery = "INSERT INTO majors (name, is_shown, start_date, end_date) 
                        VALUES ('$major_name', 1, '$current_date', '$current_date')";
        if (mysqli_query($conn, $insertQuery)) {
            add_notification("Tạo chuyên ngành $major_name thành công!", 5000, "success");
            header("Location: " . pageURL());
        } else {
            add_notification("Không thể tạo chuyên ngành $major_name.", 5000, "error");
            header("Location: " . pageURL());
        }
        exit();
    }

    function updateMajorTime($conn, $major_name, $setDate, $date) {
        $major_name = mysqli_real_escape_string($conn, $major_name);
        $date = mysqli_real_escape_string($conn, $date); 
    
        if (empty($date)) {
            add_notification("Ngày không hợp lệ!", 5000, "error");
            header("Location: " . pageURL());
            exit();
        }
    
        $query = "SELECT start_date, end_date FROM majors WHERE name = '$major_name'";
        $result = mysqli_query($conn, $query);
        if (!$result || mysqli_num_rows($result) == 0) {
            add_notification("Chuyên ngành không tồn tại!", 5000, "error");
            header("Location: " . pageURL());
            exit();
        }
    
        $row = mysqli_fetch_assoc($result);
        $startDate = $row['start_date'];
        $endDate = $row['end_date'];
    
        if ($setDate == 'start_date' && strtotime($date) > strtotime($endDate)) {
            add_notification("Ngày bắt đầu không thể sau ngày kết thúc!", 5000, "error");
            header("Location: " . pageURL());
            exit();
        } elseif ($setDate == 'end_date' && strtotime($date) < strtotime($startDate)) {
            add_notification("Ngày kết thúc không thể trước ngày bắt đầu!", 5000, "error");
            header("Location: " . pageURL());
            exit();
        }
    
        $updateQuery = "UPDATE majors SET $setDate = '$date' WHERE name = '$major_name'";
    
        if (mysqli_query($conn, $updateQuery)) {
            add_notification("Cập nhật thời gian cho chuyên ngành $major_name thành công!", 5000, "success");
            header("Location: " . pageURL());
        } else {
            add_notification("Không thể cập nhật thời gian cho chuyên ngành $major_name.", 5000, "error");
            header("Location: " . pageURL());
        }
        exit();
    }

    function updateMajorName($conn, $major_id, $major_new_name) {
        $major_id = mysqli_real_escape_string($conn, $major_id);
        $major_new_name = mysqli_real_escape_string($conn, $major_new_name);
    
        if (empty($major_new_name)) {
            add_notification("Tên chuyên ngành mới không hợp lệ!", 5000, "error");
            header("Location: " . pageURL());
            exit();
        }
    
        $query = "SELECT * FROM majors WHERE name = '$major_new_name'";
        $result = mysqli_query($conn, $query);
        if ($result && mysqli_num_rows($result) > 0) {
            add_notification("Tên chuyên ngành đã tồn tại!", 5000, "error");
            header("Location: " . pageURL());
            exit();
        }
    
        $updateQuery = "UPDATE majors SET name = '$major_new_name' WHERE major_id = $major_id";
    
        if (mysqli_query($conn, $updateQuery)) {
            add_notification("Cập nhật tên chuyên ngành thành công!", 5000, "success");
            header("Location: " . pageURL());
        } else {
            add_notification("Không thể cập nhật tên chuyên ngành. " . mysqli_error($conn), 5000, "error");
            header("Location: " . pageURL());
        }
        exit();
    }
    
    function renderMajorRow($row){
        echo "<tr>";
            echo "<td>{$row['major_id']}</td>";
            echo "<td>
                    <form method='post' onchange='this.submit()' style='width: 100%'>
                        <input type='hidden' name='major_id' value='{$row['major_id']}'>
                        <input type='text' name='major_new_name' value='{$row['major_name']}' style='width: 100%'>
                    </form>
                </td>";
            echo "<td>{$row['blocks']}</td>";
            
            echo "<td>
                    <form method='post' onchange='this.submit()'  style='width: 100%'>
                        <input type='hidden' name='major_name' value='{$row['major_name']}'>
                        <input type='datetime-local' name='new_start_date' value='" . date('Y-m-d\TH:i', strtotime($row['start_date'])) . "'  style='width: 100%'>
                    </form>
                </td>";

                echo "<td>
                    <form method='post' onchange='this.submit()'  style='width: 100%'>
                        <input type='hidden' name='major_name' value='{$row['major_name']}'>
                        <input type='datetime-local' name='new_end_date' value='" . date('Y-m-d\TH:i', strtotime($row['end_date'])) . "'  style='width: 100%'>
                    </form>
                </td>";

            echo "<td>
                    <form method='post' onchange='this.submit()' style='width: 100%'>
                        <input type='hidden' name='major_name' value='{$row['major_name']}'>
                        <input type='text' name='block_to_add' style='width: 100%'>
                    </form>
                </td>";
            echo "<td>
                <form method='post' onchange='this.submit()' style='width: 100%'>
                    <input type='hidden' name='major_name' value='{$row['major_name']}'>
                    <input type='text' name='block_to_remove' style='width: 100%'>
                </form>
            </td>";
            echo "<td>
                    <form method='post'>
                        <input type='hidden' name='major_to_delete' value='{$row['major_name']}'>
                        <input type='submit' value='Xóa ngành này' class='negative-button' style='width:100%; padding: 5px;'>
                    </form>
                </td>";
        echo "</tr>";
    }

    function deleteMajor($conn, $major_name) {
        $major_name = mysqli_real_escape_string($conn, $major_name);
    
        $checkQuery = "SELECT major_id FROM majors WHERE name = '$major_name'";
        $checkResult = mysqli_query($conn, $checkQuery);
    
        if (mysqli_num_rows($checkResult) == 0) {
            add_notification("Ngành $major_name không tồn tại!", 5000, "error");
            header("Location: " . pageURL());
            exit();
        }
    
        $deleteQuery = "DELETE FROM majors WHERE name = '$major_name'";
        if (mysqli_query($conn, $deleteQuery)) {
            add_notification("Xóa ngành $major_name thành công!", 5000, "success");
        } else {
            add_notification("Không thể xóa ngành $major_name.", 5000, "error");
        }
    
        header("Location: " . pageURL());
        exit();
    }
    

    if ($_SERVER["REQUEST_METHOD"] === "GET"){
        $queryOption = initializeQueryOptions();
        $search_value = $queryOption["search_value"];
        $sort_by_id = $queryOption["sort_by_id"];
        $sort_by_name = $queryOption["sort_by_name"];
        $sort_by_time_left = $queryOption["sort_by_time_left"];
        $filter_by_status = $queryOption["filter_by_status"];
        $filter_by_blocks = $queryOption["filter_by_blocks"];
    } elseif ($_SERVER["REQUEST_METHOD"] === "POST"){
        $major_name = $_POST["major_name"];
        if(isset($_POST["block_to_add"]) || isset($_POST["block_to_remove"])){
            if(isset($_POST["block_to_add"])){
                $action = "add";
                $block_name = $_POST["block_to_add"];
            } elseif(isset($_POST["block_to_remove"])){
                $action = "remove";
                $block_name = $_POST["block_to_remove"];
            }

            updateMB($conn, $major_name, $block_name, $action);
        } elseif(isset($_POST["new_major"])){
            createNewMajor($conn, $_POST["new_major"]);
        } elseif(isset($_POST["new_start_date"]) || isset($_POST["new_end_date"])){
            if(isset($_POST["new_start_date"])){
                $setDate = "start_date";
                $date = $_POST["new_start_date"];
            } elseif(isset($_POST["new_end_date"])){
                $setDate = "end_date";
                $date = $_POST["new_end_date"];
            }
            updateMajorTime($conn, $major_name, $setDate, $date);
        } elseif(isset($_POST["major_to_delete"])){
            deleteMajor($conn, $_POST["major_to_delete"]);
        } elseif(isset($_POST["major_new_name"])){
            updateMajorName($conn, $_POST["major_id"], $_POST["major_new_name"]);
        }
    }

    $currentPage = isset($_GET['page_index']) && $_GET['page_index'] > 0 ? (int)$_GET['page_index'] : 1;;
    $itemPerPage = 8;
    $offset = ($currentPage - 1) * $itemPerPage;

    $majorQuery = buildMajorQuery($search_value, $sort_by_id, $sort_by_name, $sort_by_time_left, $filter_by_status, $filter_by_blocks);
    $majorResult = fetchMajor($conn, $majorQuery, $itemPerPage, $offset);
    $totalResult = totalResult($conn, $majorQuery);
    $totalPages = ceil($totalResult / $itemPerPage);
    $blocksRows = fetchBlocks($conn);

    mysqli_close($conn);
?>

<main>
    <h1 class="page-title">Quản lý ngành</h1>
    <form action="" method="get" class="query-option-form" onchange="this.submit()">
        <input type="hidden" name="page" value="major_management">
        <div class="query-option-input">
            <div class="search-container">
                <label for="search_by">Tìm kiếm theo tên ngành: </label>
                <input type="text" name="search_value" value="<?php echo $search_value ?>">
            </div>
            <div class="filter-sort-option">
                <div class="sort-by">
                    <label for="sort_by">Sắp xếp theo: </label>
                    <select name="sort_by_id" id="">
                        <option value="" <?php echo $sort_by_id == "" ? "selected" : '' ?>>STT</option>
                        <option value="major_id_asc" <?php echo $sort_by_id == "major_id_asc" ? "selected" : '' ?>>STT ⬆️</option>
                        <option value="major_id_desc" <?php echo $sort_by_id == "major_id_desc" ? "selected" : '' ?>>STT ⬇️</option>
                    </select>
                    <select name="sort_by_name" id="">
                        <option value="" <?php echo $sort_by_name == "" ? "selected" : '' ?>>Tên</option>
                        <option value="name_asc" <?php echo $sort_by_name == "name_asc" ? "selected" : '' ?>>Tên ngành ⬆️</option>
                        <option value="name_desc" <?php echo $sort_by_name == "name_desc" ? "selected" : '' ?>>Tên ngành ⬇️</option>
                    </select>
                    <select name="sort_by_time_left" id="sort-by-time-left" class="sort-by">
                        <option value="">Thời gian còn lại</option>
                        <option value="time_left_asc" <?php echo (isset($_GET['sort_by_time_left']) && $_GET['sort_by_time_left'] == "time_left_asc") ? "selected" : ''; ?>>Thời gian còn lại ⬆️</option>
                        <option value="time_left_desc" <?php echo (isset($_GET['sort_by_time_left']) && $_GET['sort_by_time_left'] == "time_left_desc") ? "selected" : ''; ?>>Thời gian còn lại ⬇️</option>
                    </select>
                </div>
                <div class="filter-by">
                    <label for="filter-by">Lọc theo</label>
                    <select name="filter_by_status" id="filter-by-status" class="filter-by"> 
                        <option value="">Trạng thái</option>
                        <option value="early" <?php echo (isset($_GET['filter_by_status']) && $_GET['filter_by_status'] == 'early') ? "selected" : ''; ?>>Chưa mở</option>
                        <option value="on time" <?php echo (isset($_GET['filter_by_status']) && $_GET['filter_by_status'] == 'on time') ? "selected" : ''; ?>>Đang mở</option>
                        <option value="late" <?php echo (isset($_GET['filter_by_status']) && $_GET['filter_by_status'] == 'late') ? "selected" : ''; ?>>Quá hạn</option>
                    </select>
                    <select name="filter_by_blocks">
                        <option value="">Khối</option>
                        <?php 
                            foreach ($blocksRows as $block) {
                                $blockCode = $block["code"];
                                echo "<option value='$blockCode' " . ((isset($_GET['filter_by_blocks']) && $_GET['filter_by_blocks'] == $blockCode) ? "selected" : '') . ">$blockCode</option>";
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
                    <th>Tên ngành</th>
                    <th>Khối</th>
                    <th>Ngày bắt đầu mở đơn</th>
                    <th>Ngày kết thúc nộp đơn</th>
                    <th style="width: 120px">Thêm khối</th>
                    <th style="width: 120px">Xóa khối</th>
                    <th style="width: 120px">Xóa ngành</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if(mysqli_num_rows($majorResult) > 0){
                    while($row = mysqli_fetch_assoc($majorResult)){
                        renderMajorRow($row);
                    }
                } else {
                    echo "<tr><td colspan='8' class='warning' style='text-align: center; font-size:larger; font-weight: bold;'>Không có kết quả phù hợp</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <div class="create-new-major"  style="margin:20px 0px">
        <form action="" method="post">
            <label for="">Tạo ngành mới</label>
            <input type="text" name="new_major">
            <input type="submit" value="Tạo ngành mới">
        </form>
    </div>
    <div class='pagination'>
        <?php createPagination($currentPage, $totalPages) ?>
    </div>  
</main>



