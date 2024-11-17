<link rel="stylesheet" href="assets/css/home.css">
<?php
    include_once("auth/session.php");
    include_once("config/database.php");
    include_once("includes/pagination.php");

    function initializeQueryOptions(){
        return [
            'search_value' => $_GET['search_value'] ?? '',
            'sort_by_name' => $_GET['sort_by_name'] ?? '',
            'sort_by_time_left' => $_GET['sort_by_time_left'] ?? '',
            'filter_by_status' => $_GET['filter_by_status'] ?? '',
            'filter_by_blocks' => $_GET['filter_by_blocks'] ?? '',
            'filter_by_visibility' => $_GET['filter_by_visibility'] ?? '',
            'filter_by_applied' => $_GET['filter_by_applied'] ?? '',
        ];
    }

    function buildMajorQuery($search_value, $sort_by_name, $sort_by_time_left, $filter_by_status, $filter_by_blocks, $filter_by_visibility, $filter_by_applied){
        $majorQuery = " SELECT m.*, GROUP_CONCAT(DISTINCT b.code ORDER BY b.code ASC SEPARATOR ', ') AS block_names, 
                        CASE 
                            WHEN m.end_date < NOW() THEN 0 
                            ELSE TIMESTAMPDIFF(SECOND, NOW(), m.end_date) 
                        END AS time_left,
                        CASE 
                            WHEN a.application_id IS NOT NULL THEN 1 
                            ELSE 0 
                        END AS has_application
                        FROM majors m
                        JOIN major_blocks mb ON m.major_id = mb.major_id
                        JOIN blocks b ON mb.block_id = b.block_id
                        LEFT JOIN applications a ON a.major_id = m.major_id AND a.student_id = {$_SESSION['user_id']}
                        LEFT JOIN major_teachers mt ON mt.major_id = m.major_id
                        WHERE 1=1";

        if ($search_value) {
            $majorQuery .= " AND m.name LIKE '%$search_value%'";
        }

        if ($filter_by_status) {
            if ($filter_by_status == 'early') {
                $majorQuery .= " AND m.start_date > NOW()";
            } elseif ($filter_by_status == 'on time') {
                $majorQuery .= " AND m.start_date <= NOW() AND m.end_date >= NOW()";
            } elseif ($filter_by_status == 'late') {
                $majorQuery .= " AND m.end_date < NOW()";
            }
        }

        if ($filter_by_blocks) {
            $majorQuery .= " AND m.major_id IN (
                SELECT DISTINCT mb.major_id
                FROM major_blocks mb
                JOIN blocks b ON mb.block_id = b.block_id
                WHERE b.code = '$filter_by_blocks'
            )";
        }

        if ($_SESSION["role"] == "student") {
            $majorQuery .= " AND m.is_shown = 1";
        } elseif($_SESSION["role"] == "teacher"){
            $majorQuery .= " AND mt.user_id = {$_SESSION['user_id']}";
        }
        
        if ($filter_by_visibility !== ''){
            $majorQuery .= " AND m.is_shown = '$filter_by_visibility'";
        }

        if ($filter_by_applied) {
            if ($filter_by_applied === 'applied') {
                $majorQuery .= " AND a.application_id IS NOT NULL";
                
            } elseif ($filter_by_applied === 'not_applied') {
                $majorQuery .= " AND (a.application_id IS NULL OR a.application_id = '')"; 
            }
        }

        $majorQuery .= " GROUP BY m.major_id";

        $orderByClauses = [];

        if ($sort_by_name === 'name_asc') {
            $orderByClauses[] = 'm.name ASC';
        } elseif ($sort_by_name === 'name_desc') {
            $orderByClauses[] = 'm.name DESC';
        }

        if ($sort_by_time_left === 'time_left_asc') {
            $orderByClauses[] = 'time_left ASC'; 
        } elseif ($sort_by_time_left === 'time_left_desc') {
            $orderByClauses[] = 'time_left DESC'; 
        }

        if (!empty($orderByClauses)) {
            $majorQuery .= " ORDER BY " . implode(", ", $orderByClauses);
        }
        return $majorQuery;
    }

    function fetchMajor($conn, $majorQuery, $itemPerPage, $offset){
        $majorQuery .= " LIMIT $itemPerPage OFFSET $offset";
        $majorResult = mysqli_query($conn, $majorQuery);
        return $majorResult;
    }

    function totalResult($conn, $query){
        $totalResult = mysqli_num_rows(mysqli_query($conn, $query)); 
        return $totalResult;
    }

    function renderMajorCard($rows) {
        $startDate = new DateTime($rows["start_date"]);
        $endDate = new DateTime($rows["end_date"]);
        $currentDate = new DateTime();
        
        echo '<div class="card">';
            echo '<div class="major-name">Tên ngành: <span class="info">' . $rows["name"] . '</span></div>';
            echo '<div class="major-blocks">Khối xét tuyển: <span class="info">' . $rows['block_names'] . '</span></div>';

            if ($currentDate < $startDate) {
                $currentState = "Chưa mở";
            } elseif ($currentDate >= $startDate && $currentDate <= $endDate) {
                $currentState = "Đang mở";
            } else {
                $currentState = "Đã quá hạn";
            }

            echo "<div class='major-status'>Trạng thái: <span class='info'>$currentState</span></div>";

            echo '<div class="major-dates">';
                echo '<div class="major-start-date">Ngày bắt đầu: <span class="info">' . $startDate->format("d-m-Y H:i:s") . '</span></div>';
                echo '<div class="major-end-date">Ngày kết thúc: <span class="info">' . $endDate->format("d-m-Y H:i:s") . '</span></div>';
            echo '</div>';

            echo '<div class="bottom-cards-container">';
                $submitProfileAbility = $_SESSION['role'] == 'student' && ($rows["has_application"] == 1 || $currentState != "Đang mở");
                // $location = "index.php?page=submit_profile" . ($_SESSION['role'] == 'student' ? '&major_id=' . $rows["major_id"] : '&search_value=' . urlencode($rows["name"])); 
                $location = "index.php?page=submit_profile" . '&major_id=' . $rows["major_id"] . '&search_value=' . urlencode($rows["name"]); 
                $submitButtonText = (($_SESSION['role'] == 'student' && $rows["has_application"] == 0) || $_SESSION['role'] != 'student') ? "Nộp hồ sơ" : "Đã nộp";
                $submitButtonClass = $submitProfileAbility ? "disabled-button" : "";
                echo "<button type='button'  class='bottom-card-button positive-button $submitButtonClass' onclick=\"window.location.href='$location'\" " . ($submitProfileAbility ? "disabled" : "") . ">$submitButtonText</button>";
                
                
                if ($_SESSION["role"] != "student") {
                    $majorVisibility = $rows["is_shown"] == 0 ? "Hiện" : "Ẩn";
                    $visibilityState = $rows["is_shown"] == 0 ? "is-show-true" : "is-show-false";
                    echo "<button class='bottom-card-button toggle-visibility-button $visibilityState' data-major-id='{$rows["major_id"]}'> $majorVisibility </button>";
                    if ($_SESSION["role"] == "admin"){
                        echo "<button class='bottom-card-button delete-bottom-card-button negative-button' data-major-id='{$rows["major_id"]}'>Xóa</button>";
                    }
                } elseif($rows["has_application"] == 1) {
                    echo "<button type='button' class='bottom-card-button neutral-button' onclick=\"window.location.href='$location'\">Xem hồ sơ</button>";
                }
            echo '</div>';
        echo '</div>';
    }

    function fetchBlocks($conn) {
        $query = "SELECT * FROM blocks";
        $result = mysqli_query($conn, $query);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    if ($_SERVER["REQUEST_METHOD"] === "GET"){
        $queryOption = initializeQueryOptions();
        $search_value = $queryOption["search_value"];
        $sort_by_name = $queryOption["sort_by_name"];
        $sort_by_time_left = $queryOption["sort_by_time_left"];
        $filter_by_status = $queryOption["filter_by_status"];
        $filter_by_blocks = $queryOption["filter_by_blocks"];
        $filter_by_visibility = $queryOption["filter_by_visibility"];
        $filter_by_applied = $queryOption["filter_by_applied"];
    }
    
    $currentPage = isset($_GET['page_index']) && $_GET['page_index'] > 0 ? (int)$_GET['page_index'] : 1;;
    $itemPerPage = 6;
    $offset = ($currentPage - 1) * $itemPerPage;

    $blocksRows = fetchBlocks($conn);
    $majorQuery = buildMajorQuery($search_value, $sort_by_name, $sort_by_time_left, $filter_by_status, $filter_by_blocks, $filter_by_visibility, $filter_by_applied);
    $majorResult = fetchMajor($conn, $majorQuery, $itemPerPage, $offset);
    $totalResult = totalResult($conn, $majorQuery);
    $totalPages = ceil($totalResult / $itemPerPage);

    mysqli_close($conn);
?>
<main>
    <h1 class="page-title">Trang chủ</h1>
    <form method="GET" class="query-option-form" onchange="this.submit()">
        <h2 class="query-option-title">Các khóa học</h2>
        <div class="query-option-input">
            <div class="search-by">
                <label for="search_value">Tìm kiếm tên ngành: </label>
                <input type="text" name="search_value" value="<?php echo isset($_GET['search_value']) ? $_GET['search_value'] : ''; ?>">
            </div>
            <div class="filter-sort-option">
                <div class="sort-by">
                    <label for="">Sắp xếp theo: </label>
                    <select name="sort_by_name" id="sort-by-name" class="sort-by">
                        <option value="">Tên ngành</option>
                        <option value="name_asc" <?php echo (isset($_GET['sort_by_name']) && $_GET['sort_by_name'] == "name_asc") ? "selected" : ''; ?>>Tên ngành ⬆️</option>
                        <option value="name_desc" <?php echo (isset($_GET['sort_by_name']) && $_GET['sort_by_name'] == "name_desc") ? "selected" : ''; ?>>Tên ngành ⬇️</option>
                    </select>
                    <select name="sort_by_time_left" id="sort-by-time-left" class="sort-by">
                        <option value="">Thời gian còn lại</option>
                        <option value="time_left_asc" <?php echo (isset($_GET['sort_by_time_left']) && $_GET['sort_by_time_left'] == "time_left_asc") ? "selected" : ''; ?>>Thời gian còn lại ⬆️</option>
                        <option value="time_left_desc" <?php echo (isset($_GET['sort_by_time_left']) && $_GET['sort_by_time_left'] == "time_left_desc") ? "selected" : ''; ?>>Thời gian còn lại ⬇️</option>
                    </select>
                </div>
                <div class="filter-by">
                    <label for="filter_by">Lọc theo</label>
                    <select name="filter_by_status" id="filter-by-status" class="filter-by"> 
                        <option value="">Trạng thái</option>
                        <option value="early" <?php echo (isset($_GET['filter_by_status']) && $_GET['filter_by_status'] == 'early') ? "selected" : ''; ?>>Chưa mở</option>
                        <option value="on time" <?php echo (isset($_GET['filter_by_status']) && $_GET['filter_by_status'] == 'on time') ? "selected" : ''; ?>>Đang mở</option>
                        <option value="late" <?php echo (isset($_GET['filter_by_status']) && $_GET['filter_by_status'] == 'late') ? "selected" : ''; ?>>Quá hạn</option>
                    </select>
                    <select name="filter_by_blocks" id="filter-by-blocks" class="filter-by">
                        <option value="">Khối</option>
                        <?php 
                            foreach ($blocksRows as $block) {
                                $blockCode = $block["code"];
                                echo "<option value='$blockCode' " . ((isset($_GET['filter_by_blocks']) && $_GET['filter_by_blocks'] == $blockCode) ? "selected" : '') . ">$blockCode</option>";
                            }
                        ?>
                    </select>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <select name="filter_by_visibility" id="filter-by-visibility" class="filter-by">
                            <option value="">Hiển thị</option>
                            <option value="1" <?php echo (isset($_GET['filter_by_visibility']) &&  $_GET['filter_by_visibility'] == "1") ? "selected" : ''; ?>>Hiện</option>
                            <option value="0" <?php echo (isset($_GET['filter_by_visibility']) &&  $_GET['filter_by_visibility'] == "0") ? "selected" : ''; ?>>Ẩn</option>
                        </select>
                    <?php elseif ($_SESSION['role'] === 'student'): ?>
                        <select name="filter_by_applied" class="filter-by">
                            <option value="">Tình trạng nộp</option>
                            <option value="applied" <?php echo (isset($_GET['filter_by_applied']) &&  $_GET['filter_by_applied'] == "applied") ? "selected" : ''; ?>>Đã nộp</option>
                            <option value="not_applied" <?php echo (isset($_GET['filter_by_applied']) &&  $_GET['filter_by_applied'] == "not_applied") ? "selected" : ''; ?>>Chưa nộp</option>
                        </select>
                    <?php endif; ?>
                    
                </div>
            </div>
        </div>
        
    </form>
    
    <div class="content">
        <?php if($totalResult > 0): ?>
            <div class='total-result'>Tổng số kết quả: <span class='info'><?php echo $totalResult ?></span></div>
            <div class="cards-container">
                <?php 
                    while ($rows = mysqli_fetch_assoc($majorResult)) {
                        renderMajorCard($rows);
                    }
                ?>
            </div>
            <div class='pagination'>
                <?php createPagination($currentPage, $totalPages) ?>
            </div>
        <?php else: ?>
            <h1 class='no-result'>Không tìm thấy kết quả nào</h1>
        <?php endif; ?>
    </div>

    <div class="notifications">
        <?php display_notifications(); ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.toggle-visibility-button').forEach(button => {
                button.addEventListener('click', function(event) {
                    event.preventDefault(); 

                    const majorId = this.dataset.majorId;
                    const formData = new FormData();
                    formData.append('major_id', majorId); 

                    fetch('includes/toggle_major_visibility.php', {
                        method: 'POST',
                        body: formData 
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.textContent = data.newVisibility ? 'Hiện' : 'Ẩn';
                            if (this.textContent == "Hiện") {
                                this.classList.remove('is-show-false');
                                this.classList.add('is-show-true');
                            } else {
                                this.classList.remove('is-show-true');
                                this.classList.add('is-show-false');
                            }
                        } else {
                            alert("Không thể đặt lại hiển thị");
                        }
                    })
                    .catch(error => console.log('Lỗi ', error));
                });
            });
        

            document.querySelectorAll('.delete-bottom-card').forEach(button => {
                button.addEventListener('click', function(event) {
                    event.preventDefault(); 

                    const majorId = this.dataset.majorId;
                    if (confirm("Bạn có chắc muốn xóa ngành này?")) {
                        const formData = new FormData();
                        formData.append('major_id', majorId); 

                        fetch('includes/delete_major.php', {
                            method: 'POST',
                            body: formData 
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert("Xóa ngành thành công");
                                window.location.reload();
                            } else {
                                alert("Không thể xóa ngành này");
                            }
                        })
                        .catch(error => console.log('Lỗi ', error));
                    }
                });
            });
        });
    </script>
</main>
