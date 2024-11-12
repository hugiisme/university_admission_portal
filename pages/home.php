<?php
    include_once("config/database.php");
    date_default_timezone_set('Asia/Ho_Chi_Minh');
    $homeSearchValue = '';
    $homeSortByName = '';
    $homeSortByTimeLeft = '';
    $homeFilterByStatus = '';
    $homeFilterByBlocks = '';
    $homeFilterByVisibility = '';
    $homeFilterByApplied = '';

    $homeCurrentPage = $homeCurrentPage = isset($_GET['page_index']) ? (int)$_GET['page_index'] : 1;;
    $homeItemPerPage = 6;
    $homeOffset = ($homeCurrentPage - 1) * $homeItemPerPage;
?>
<main>
    <h1 class="page-title">Trang chủ</h1>
    <form method="GET" id="filter-form" onchange="this.submit()">
        <div class="content">
            <div class="content-header">
                <h2 class="content-title">Các khóa học</h2>
                <div class="content-input">
                    <div class="search">
                        <label for="search_value">Tìm kiếm tên: </label>
                        <input type="text" name="search_value" value="<?php echo isset($_GET['search_value']) ? $_GET['search_value'] : ''; ?>">
                    </div>
                    <div class="content-buttons">
                        <div class="sort-by">
                            <label for="">Sắp xếp theo: </label>
                            <select name="sort_by_name" id="sort-by-name" class="sort-by">
                                <option value="" <?php echo (!isset($_GET['sort_by_name']) || empty($_GET['sort_by_name'])) ? "selected" : ''; ?>>Tên</option>
                                <option value="name_asc" <?php echo (isset($_GET['sort_by_name']) && $_GET['sort_by_name'] == "name_asc") ? "selected" : ''; ?>>Tên ⬆️</option>
                                <option value="name_desc" <?php echo (isset($_GET['sort_by_name']) && $_GET['sort_by_name'] == "name_desc") ? "selected" : ''; ?>>Tên ⬇️</option>
                            </select>
                            <select name="sort_by_time_left" id="sort-by-time-left" class="sort-by">
                                <option value="" <?php echo (!isset($_GET['sort_by_time_left']) || empty($_GET['sort_by_time_left'])) ? "selected" : ''; ?>>Thời gian còn lại</option>
                                <option value="time_left_asc" <?php echo (isset($_GET['sort_by_time_left']) && $_GET['sort_by_time_left'] == "time_left_asc") ? "selected" : ''; ?>>Thời gian còn lại ⬆️</option>
                                <option value="time_left_desc" <?php echo (isset($_GET['sort_by_time_left']) && $_GET['sort_by_time_left'] == "time_left_desc") ? "selected" : ''; ?>>Thời gian còn lại ⬇️</option>
                            </select>
                        </div>
                        <div class="filter-by">
                            <label for="filter_by">Lọc theo</label>
                            <select name="filter_by_status" id="filter-by-status" class="filter-by"> 
                                <option value="" <?php echo (!isset($_GET['filter_by_status']) || empty($_GET['filter_by_status'])) ? "selected" : ''; ?>>Thời gian</option>
                                <option value="early" <?php echo (isset($_GET['filter_by_status']) && $_GET['filter_by_status'] == 'early') ? "selected" : ''; ?>>Chưa mở</option>
                                <option value="on time" <?php echo (isset($_GET['filter_by_status']) && $_GET['filter_by_status'] == 'on time') ? "selected" : ''; ?>>Đang mở</option>
                                <option value="late" <?php echo (isset($_GET['filter_by_status']) && $_GET['filter_by_status'] == 'late') ? "selected" : ''; ?>>Quá hạn</option>
                            </select>
                            <select name="filter_by_blocks" id="filter-by-blocks" class="filter-by">
                                <option value="" <?php echo (!isset($_GET['filter_by_blocks']) || empty( $_GET['filter_by_blocks'])) ? "selected" : ''; ?>>Khối</option>
                                <?php 
                                    $blocksQuery = "SELECT * FROM blocks";
                                    $blocksResult = mysqli_query($conn, $blocksQuery);
                                    $blocksRows = mysqli_fetch_all($blocksResult, MYSQLI_ASSOC);

                                    foreach ($blocksRows as $block) {
                                        $blockCode = $block["code"];
                                        echo "<option value='$blockCode' " . ((isset($_GET['filter_by_blocks']) && $_GET['filter_by_blocks'] == $blockCode) ? "selected" : '') . ">$blockCode</option>";
                                    }
                                ?>
                            </select>
                            <?php if ($_SESSION['role'] === 'admin'): ?>
                                <select name="filter_by_visibility" id="filter-by-visibility" class="filter-by">
                                    <option value="" <?php echo empty($_GET['filter_by_visibility']) ? "selected" : ''; ?>>Hiển thị</option>
                                    <option value="1" <?php echo (isset($_GET['filter_by_visibility']) &&  $_GET['filter_by_visibility'] == "1") ? "selected" : ''; ?>>Hiện</option>
                                    <option value="0" <?php echo (isset($_GET['filter_by_visibility']) &&  $_GET['filter_by_visibility'] == "0") ? "selected" : ''; ?>>Ẩn</option>
                                </select>
                            <?php elseif ($_SESSION['role'] === 'student'): ?>
                                <select name="filter_by_applied" class="filter-by">
                                    <option value="" <?php echo empty($_GET['filter_by_applied']) ? "selected" : ''; ?>>Tình trạng nộp</option>
                                    <option value="applied" <?php echo (isset($_GET['filter_by_applied']) &&  $_GET['filter_by_applied'] == "applied") ? "selected" : ''; ?>>Đã nộp</option>
                                    <option value="not_applied" <?php echo (isset($_GET['filter_by_applied']) &&  $_GET['filter_by_applied'] == "not_applied") ? "selected" : ''; ?>>Chưa nộp</option>
                                </select>
                            <?php endif; ?>
                            
                        </div>
                    </div>
                </div>
            </div>
            
            <?php 
                if ($_SERVER["REQUEST_METHOD"] === "GET") {
                    $homeSearchValue = isset($_GET['search_value']) ? trim($_GET['search_value']) : '';
                    $homeSortByName = isset($_GET['sort_by_name']) ? trim($_GET['sort_by_name']) : '';
                    $homeSortByTimeLeft = isset($_GET['sort_by_time_left']) ? trim($_GET['sort_by_time_left']) : '';
                    $homeFilterByStatus = isset($_GET['filter_by_status']) ? trim($_GET['filter_by_status']) : '';
                    $homeFilterByBlocks = isset($_GET['filter_by_blocks']) ? trim($_GET['filter_by_blocks']) : '';
                    $homeFilterByVisibility = isset($_GET['filter_by_visibility']) ? trim($_GET['filter_by_visibility']) : '';
                    $homeFilterByApplied = isset($_GET['filter_by_applied']) ? trim($_GET['filter_by_applied']) : '';
                }

                $majorQuery = " SELECT m.*, GROUP_CONCAT(b.code ORDER BY b.code ASC SEPARATOR ', ') AS block_names, 
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
                                WHERE 1=1";

                if ($homeSearchValue) {
                    $majorQuery .= " AND m.name LIKE '%$homeSearchValue%'";
                }

                if ($homeFilterByStatus) {
                    if ($homeFilterByStatus == 'early') {
                        $majorQuery .= " AND m.start_date > NOW()";
                    } elseif ($homeFilterByStatus == 'on time') {
                        $majorQuery .= " AND m.start_date <= NOW() AND m.end_date >= NOW()";
                    } elseif ($homeFilterByStatus == 'late') {
                        $majorQuery .= " AND m.end_date < NOW()";
                    }
                }

                if ($homeFilterByBlocks) {
                    $majorQuery .= " AND m.major_id IN (
                        SELECT DISTINCT mb.major_id
                        FROM major_blocks mb
                        JOIN blocks b ON mb.block_id = b.block_id
                        WHERE b.code = '$homeFilterByBlocks'
                    )";
                }

                if ($_SESSION["role"] == "student") {
                    $majorQuery .= " AND m.is_shown = 1";
                } elseif ($homeFilterByVisibility !== ''){
                    $majorQuery .= " AND m.is_shown = '$homeFilterByVisibility'";
                }

                if ($homeFilterByApplied) {
                    if ($homeFilterByApplied === 'applied') {
                        $majorQuery .= " AND a.application_id IS NOT NULL";
                    } elseif ($homeFilterByApplied === 'not_applied') {
                        $majorQuery .= " AND (a.application_id IS NULL OR a.application_id = '')"; 
                    }
                }

                $majorQuery .= " GROUP BY m.major_id";

                $orderByClauses = [];

                if ($homeSortByName === 'name_asc') {
                    $orderByClauses[] = 'm.name ASC';
                } elseif ($homeSortByName === 'name_desc') {
                    $orderByClauses[] = 'm.name DESC';
                }

                if ($homeSortByTimeLeft === 'time_left_asc') {
                    $orderByClauses[] = 'time_left ASC'; 
                } elseif ($homeSortByTimeLeft === 'time_left_desc') {
                    $orderByClauses[] = 'time_left DESC'; 
                }

                if (!empty($orderByClauses)) {
                    $majorQuery .= " ORDER BY " . implode(", ", $orderByClauses);
                }

                $majorQuery .= " LIMIT $homeItemPerPage OFFSET $homeOffset";
                $majorResult = mysqli_query($conn, $majorQuery);
                if (!$majorResult) {
                    die('Lỗi query: ' . mysqli_error($conn));
                }

                $totalResult = mysqli_num_rows(mysqli_query($conn, str_replace("LIMIT $homeItemPerPage OFFSET $homeOffset", "", $majorQuery))); 
                $totalPages = ceil($totalResult / $homeItemPerPage);

                if ($totalResult > 0) {
                    echo "<div class='total-result'>Tổng số kết quả: $totalResult </div>";
                    echo "<div class='cards-container'>";
                        while ($rows = mysqli_fetch_assoc($majorResult)) {
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

                                echo '<div class="major-buttons">';
                                    $submitProfileAbility = $_SESSION['role'] == 'student' && ($rows["has_application"] == 1 || $currentState != "Đang mở");
                                    $location = "index.php?page=submit_profile" . ($_SESSION['role'] == 'student' ? '&major_id=' . $rows["major_id"] : '&search_value=' . urlencode($rows["name"]));
                                    $submitButtonText = (($_SESSION['role'] == 'student' && $rows["has_application"] == 0) || $_SESSION['role'] != 'student') ? "Nộp hồ sơ" : "Đã nộp";
                                    
                                    echo "<button type='button'  class='major-button positive-button' onclick=\"window.location.href='$location'\" " . ($submitProfileAbility ? "disabled" : "") . ">$submitButtonText</button>";
                                    if ($_SESSION["role"] != "student") {
                                        $majorVisibility = $rows["is_shown"] == 0 ? "Hiện" : "Ẩn";
                                        $visibilityState = $rows["is_shown"] == 0 ? "is-show-true" : "is-show-false";
                                        echo "<button class='major-button toggle-visibility-button $visibilityState' data-major-id='{$rows["major_id"]}'> $majorVisibility </button>";
                                        echo "<button class='major-button negative-button' data-major-id='{$rows["major_id"]}'>Xóa</button>";
                                    }
                                echo '</div>';
                            echo '</div>';
                        }
                    echo "</div>";
                } else {
                    echo "<h1 class='no-result'>Không tìm thấy kết quả nào</h1>";
                }
            ?>
        </div>
    </form>
    
    <?php 
        function buildPaginationUrl($page) {
            $url = $_SERVER['PHP_SELF'] . '?';
            foreach ($_GET as $key => $value) {
                if ($key != 'page_index') {
                    $url .= $key . '=' . urlencode($value) . '&';
                }
            }
            $url .= 'page_index=' . $page;
            return $url;
        }
        echo "<div class='pagination'>";

        // Previous Button
        if ($homeCurrentPage > 1) {
            $previousPage = $homeCurrentPage - 1;
            echo "<form action='" . $_SERVER['PHP_SELF'] . "' method='get'>";
            foreach ($_GET as $key => $value) {
                if ($key != 'page_index') {
                    echo "<input type='hidden' name='" . htmlspecialchars($key) . "' value='" . htmlspecialchars($value) . "'>";
                }
            }
            echo "<button type='submit' name='page_index' value='$previousPage' class='neutral-button'>&laquo; Previous</button>";
            echo "</form>";
        } else {
            echo "<button class='neutral-button disabled-button' disabled>&laquo; Previous</button>";
        }

        // Dropdown for page selection
        echo "<form action='" . $_SERVER['PHP_SELF'] . "' method='get' class='pagination-form'>";
        foreach ($_GET as $key => $value) {
            if ($key != 'page_index') {
                echo "<input type='hidden' name='" . htmlspecialchars($key) . "' value='" . htmlspecialchars($value) . "'>";
            }
        }
        echo "<select class='pagination-select' onchange='this.form.submit()' name='page_index'>";
        for ($i = 1; $i <= $totalPages; $i++) {
            $selected = ($i == $homeCurrentPage) ? 'selected' : '';
            echo "<option value='$i' $selected>Page $i</option>";
        }
        echo "</select>";
        echo "</form>";

        // Next Button
        if ($homeCurrentPage < $totalPages) {
            $nextPage = $homeCurrentPage + 1;
            echo "<form action='" . $_SERVER['PHP_SELF'] . "' method='get'>";
            foreach ($_GET as $key => $value) {
                if ($key != 'page_index') {
                    echo "<input type='hidden' name='" . htmlspecialchars($key) . "' value='" . htmlspecialchars($value) . "'>";
                }
            }
            echo "<button type='submit' name='page_index' value='$nextPage' class='neutral-button'>Next &raquo;</button>";
            echo "</form>";
        } else {
            echo "<button class='neutral-button disabled-button' disabled>Next &raquo;</button>";
        }

        echo "</div>";
    ?>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.toggle-visibility-button').forEach(button => {
                button.addEventListener('click', function(event) {
                    event.preventDefault(); 

                    const majorId = this.dataset.majorId;

                    fetch('includes/toggle_major_visibility.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `major_id=${majorId}`
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
        


            document.querySelectorAll('.delete-major-button').forEach(button => {
                button.addEventListener('click', function() {
                    event.preventDefault();

                    const majorId = this.dataset.majorId;
                    if (confirm("Bạn có chắc muốn xóa ngành này?")) {
                        fetch('includes/delete_major.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: `major_id=${majorId}`
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                window.location.reload();
                            } else {
                                alert("Không thể xóa ngành này");
                            }
                        });
                    }
                });
            });
        });

    </script>
</main>

<!-- TODO: Tạo form data -->
