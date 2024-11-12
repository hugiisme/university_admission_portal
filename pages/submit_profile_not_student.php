<?php
    include_once("includes/session.php");
    include_once("config/database.php");
    include_once("includes/add_notification.php");
    include_once("includes/display_notifications.php");

    $applicationSearchBy = '';
    $applicationSearchValue = '';
    $applicationSortByMajorName = '';
    $applicationSortByStudentName = '';
    $applicationSortByVerifierName = '';
    $applicationSortByAppId = '';
    $applicationFilterByStatus = '';
    $applicationFilterByBlocks = '';

    $applicationCurrentPage = $applicationCurrentPage = isset($_GET['page_index']) ? (int)$_GET['page_index'] : 1;;
    $applicationItemPerPage = 6;
    $applicationOffset = ($applicationCurrentPage - 1) * $applicationItemPerPage;
?>

<h1 class="page-title">Danh sách hồ sơ</h1>
<form method="get" class="search-sort-filter-form" onchange="this.submit()">
    <input type="hidden" name="page" value="submit_profile">
    <div class="search-container">
        <label for="search_by">Tìm kiếm theo: </label>
        <select name="search_by_application_opt">
            <option value="m.name" <?php echo isset($_GET['search_by_application_opt']) && $_GET['search_by_application_opt'] == 'm.name' ? 'selected' : ''; ?>>Tên ngành</option>
            <option value="s.name" <?php echo isset($_GET['search_by_application_opt']) && $_GET['search_by_application_opt'] == 's.name' ? 'selected' : ''; ?>>Tên người nộp</option>
            <option value="v.name" <?php echo isset($_GET['search_by_application_opt']) && $_GET['search_by_application_opt'] == 'v.name' ? 'selected' : ''; ?>>Tên người duyệt</option>
        </select>
        <input type="text" name="search_value" value="<?php echo isset($_GET["search_value"]) ? $_GET["search_value"] : '' ?>">
    </div>
    <div class="sort-filter">
        <div class="sort-container">
            <label for="sort_by">Sắp xếp theo: </label>
            <select name="sort_by_major_name">
                <option value="">Tên ngành</option>
                <option value="major_name_asc" <?php echo isset($_GET['sort_by_major_name']) && $_GET['sort_by_major_name'] == 'major_name_asc' ? 'selected' : ''; ?>>Tên ngành ⬆️</option>
                <option value="major_name_desc" <?php echo isset($_GET['sort_by_major_name']) && $_GET['sort_by_major_name'] == 'major_name_desc' ? 'selected' : ''; ?>>Tên ngành ⬇️</option>
            </select>
            <select name="sort_by_student_name">
                <option value="">Tên học sinh</option>
                <option value="student_name_asc" <?php echo isset($_GET['sort_by_student_name']) && $_GET['sort_by_student_name'] == 'student_name_asc' ? 'selected' : ''; ?>>Tên học sinh ⬆️</option>
                <option value="student_name_desc" <?php echo isset($_GET['sort_by_student_name']) && $_GET['sort_by_student_name'] == 'student_name_desc' ? 'selected' : ''; ?>>Tên học sinh ⬇️</option>
            </select>
            <select name="sort_by_verifier_name">
                <option value="">Tên người duyệt</option>
                <option value="verifier_name_asc" <?php echo isset($_GET['sort_by_verifier_name']) && $_GET['sort_by_verifier_name'] == 'verifier_name_asc' ? 'selected' : ''; ?>>Tên người duyệt ⬆️</option>
                <option value="verifier_name_desc" <?php echo isset($_GET['sort_by_verifier_name']) && $_GET['sort_by_verifier_name'] == 'verifier_name_desc' ? 'selected' : ''; ?>>Tên người duyệt ⬇️</option>
            </select>
            <select name="sort_by_application_id">
                <option value="">STT</option>
                <option value="application_id_asc" <?php echo isset($_GET['sort_by_application_id']) && $_GET['sort_by_application_id'] == 'application_id_asc' ? 'selected' : ''; ?>>STT ⬆️</option>
                <option value="application_id_desc" <?php echo isset($_GET['sort_by_application_id']) && $_GET['sort_by_application_id'] == 'application_id_desc' ? 'selected' : ''; ?>>STT ⬇️</option>
            </select>
        </div>
        <div class="filter-container">
            <label for="filter_by">Lọc theo: </label>
            <select name="filter_by_status">
                <option value="">Trạng thái</option>
                <option value="pending" <?php echo isset($_GET['filter_by_status']) && $_GET['filter_by_status'] == 'pending' ? 'selected' : ''; ?>>Chưa duyệt</option>
                <option value="accepted" <?php echo isset($_GET['filter_by_status']) && $_GET['filter_by_status'] == 'accepted' ? 'selected' : ''; ?>>Đã duyệt</option>
                <option value="denied" <?php echo isset($_GET['filter_by_status']) && $_GET['filter_by_status'] == 'denied' ? 'selected' : ''; ?>>Từ chối</option>
            </select>
            <select name="filter_by_blocks">
                <option value="">Khối</option>
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
        </div>
    </div>

    
    
</form>

<?php
    if ($_SERVER["REQUEST_METHOD"] === "GET") {
        $applicationSearchBy = isset($_GET["search_by_application_opt"]) ? trim($_GET["search_by_application_opt"]) : '';
        $applicationSearchValue = isset($_GET["search_value"]) ? trim($_GET["search_value"]) : '';
        $applicationSortByMajorName = isset($_GET["sort_by_major_name"]) ? trim($_GET["sort_by_major_name"]) : '';
        $applicationSortByStudentName = isset($_GET["sort_by_student_name"]) ? trim($_GET["sort_by_student_name"]) : '';
        $applicationSortByVerifierName = isset($_GET["sort_by_verifier_name"]) ? trim($_GET["sort_by_verifier_name"]) : '';
        $applicationSortByAppId = isset($_GET["sort_by_application_id"]) ? trim($_GET["sort_by_application_id"]) : '';
        $applicationFilterByStatus = isset($_GET["filter_by_status"]) ? trim($_GET["filter_by_status"]) : '';
        $applicationFilterByBlocks = isset($_GET['filter_by_blocks']) ? trim($_GET['filter_by_blocks']) : '';
    }
    $findAllApplicationsQuery = "SELECT 
                                    a.application_id,
                                    m.name AS major_name,
                                    s.name AS student_name,
                                    b.code AS block_code,
                                    IFNULL(v.name, 'Chưa có') AS verifier_name,
                                    IFNULL(v.user_id, '0') AS verifier_id,
                                    a.status
                                FROM applications a
                                JOIN users s ON a.student_id = s.user_id
                                JOIN majors m ON a.major_id = m.major_id
                                JOIN blocks b ON a.block_id = b.block_id
                                LEFT JOIN users v ON a.verifier_id = v.user_id
                                WHERE 1=1";
    if($applicationSearchBy && $applicationSearchValue){
        $findAllApplicationsQuery .= " AND $applicationSearchBy LIKE '%$applicationSearchValue%'";
    }

    if($applicationFilterByStatus){
        $findAllApplicationsQuery .= " AND status = '$applicationFilterByStatus'";
    }

    if ($applicationFilterByBlocks) {
        $findAllApplicationsQuery .= " AND b.code = '$applicationFilterByBlocks'";
    }

    $orderByApplicationClauses = [];

    if ($applicationSortByMajorName) {
        if ($applicationSortByMajorName === 'major_name_asc') {
            $orderByApplicationClauses[] = "m.name ASC";
        } elseif ($applicationSortByMajorName === 'major_name_desc') {
            $orderByApplicationClauses[] = "m.name DESC";
        }
    }

    if ($applicationSortByStudentName) {
        if ($applicationSortByStudentName === 'student_name_asc') {
            $orderByApplicationClauses[] = "s.name ASC";
        } elseif ($applicationSortByStudentName === 'student_name_desc') {
            $orderByApplicationClauses[] = "s.name DESC";
        }
    }

    if ($applicationSortByVerifierName) {
        if ($applicationSortByVerifierName === 'verifier_name_asc') {
            $orderByApplicationClauses[] = "v.name ASC";
        } elseif ($applicationSortByVerifierName === 'verifier_name_desc') {
            $orderByApplicationClauses[] = "v.name DESC";
        }
    }

    if ($applicationSortByAppId) {
        if ($applicationSortByAppId === 'application_id_asc') {
            $orderByApplicationClauses[] = "a.application_id ASC";
        } elseif ($applicationSortByAppId === 'application_id_desc') {
            $orderByApplicationClauses[] = "a.application_id DESC";
        }
    }

    if (!empty($orderByApplicationClauses)) {
        $findAllApplicationsQuery .= " ORDER BY " . implode(", ", $orderByApplicationClauses);
    }

    $findAllApplicationsQuery .= " LIMIT $applicationItemPerPage OFFSET $applicationOffset";
    
    $findAllApplicationsResult = mysqli_query($conn, $findAllApplicationsQuery);
    if (!$findAllApplicationsResult) {
        die('Lỗi query: ' . mysqli_error($conn));
    }

    $totalResult = mysqli_num_rows(mysqli_query($conn, str_replace("LIMIT $applicationItemPerPage OFFSET $applicationOffset", "", $findAllApplicationsQuery)));
    $totalPages = ceil($totalResult / $applicationItemPerPage);
    if ($totalResult > 0) {
        echo "<div class='total-result'>Tổng số kết quả: $totalResult </div>";
        
        echo "<div class='application-cards cards-container'>";
        while($applicationRows = mysqli_fetch_assoc($findAllApplicationsResult)){
            echo "<div class='application-card card'>";
                echo "<div> STT: <span class='info'>" . $applicationRows["application_id"] . "</span></div>";
                echo "<div> Tên người nộp: <span class='info'>" . $applicationRows["student_name"] . "</span></div>";
                echo "<div> Tên ngành: <span class='info'>" . $applicationRows["major_name"] . "</span></div>";
                echo "<div> Tên khối: <span class='info'>" . $applicationRows["block_code"] . "</span></div>";
                $status = $applicationRows["status"];

                echo "<div> Trạng thái: <span class='info'>";
                    switch ($status) {
                        case "pending":
                            echo "Chưa duyệt";
                            break;
                        case "accepted":
                            echo "Đã duyệt";
                            break;
                        case "denied":
                            echo "Không duyệt";
                            break;
                        default:
                            echo "Trạng thái không xác định"; 
                            break;
                    }
                echo "</span></div>";
                                
                echo "<div> Tên người xét duyệt: <span class='info'>" . $applicationRows["verifier_name"] . "</span></div>";          

                echo "<div class='application-buttons'>";
                    $acceptButtonDisabled = $applicationRows["status"] == "accepted" || $applicationRows["verifier_id"] != $_SESSION["user_id"];
                    $denyButtonDisabled = $applicationRows["status"] == "denied" || $applicationRows["verifier_id"] != $_SESSION["user_id"];

                    $disableAcceptButton = $acceptButtonDisabled ? "disabled" : '';
                    $disableDenyButton = $denyButtonDisabled ? "disabled" : '';

                    $disableAcceptClass = $acceptButtonDisabled ? "disabled-button" : '';
                    $disableDenyClass = $denyButtonDisabled ? "disabled-button" : '';
                    echo "<button class='accept-application-btn positive-button application-btn $disableAcceptClass' data-application-id='{$applicationRows["application_id"]}' " . $disableAcceptButton . ">Duyệt</button>";
                    echo "<button class='deny-application-btn negative-button application-btn $disableDenyClass' data-application-id='{$applicationRows["application_id"]}' " . $disableDenyButton . ">Không duyệt</button>";
                    if($_SESSION["role"] == "admin"){
                        echo "<button class='delete-application-button' data-application-id='{$applicationRows["application_id"]}'>&times;</button>";
                    }
                echo "</div>";
            echo "</div>";
        }
        echo "</div>";
    } else {
        echo "<h1 class='no-result'>Không tìm thấy kết quả nào</h1>";
    }
    
    mysqli_close($conn);
?>



<?php
    // Function to build the pagination URL with current query parameters
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

    // Start the pagination HTML output
    echo "<div class='pagination'>";

    // Previous Button
    if ($applicationCurrentPage > 1) {
        $previousPage = $applicationCurrentPage - 1;
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
        $selected = ($i == $applicationCurrentPage) ? 'selected' : '';
        echo "<option value='$i' $selected>Page $i</option>";
    }
    echo "</select>";
    echo "</form>";

    // Next Button
    if ($applicationCurrentPage < $totalPages) {
        $nextPage = $applicationCurrentPage + 1;
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
        document.querySelectorAll('.accept-application-btn').forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                const applicationId = this.dataset.applicationId;
                if (confirm("Bạn chắc chắn muốn duyệt hồ sơ này?")) {
                    const formData = new FormData();
                    formData.append('application_id', applicationId);
                    formData.append('action', 'accept');

                    fetch('includes/update_application.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())  
                    .then(result => {
                        if (result.status === 'success') {
                            alert('Hồ sơ đã được duyệt!');
                            location.reload();
                        } else {
                            alert(result.message || 'Có lỗi xảy ra. Vui lòng thử lại.');
                        }
                    });
                }
            });
        });

        document.querySelectorAll('.deny-application-btn').forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                const applicationId = this.dataset.applicationId;
                if (confirm("Bạn chắc chắn muốn từ chối hồ sơ này?")) {
                    const formData = new FormData();
                    formData.append('application_id', applicationId);
                    formData.append('action', 'deny');

                    fetch('includes/update_application.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())  
                    .then(result => {
                        if (result.status === 'success') {
                            alert('Hồ sơ đã bị từ chối.');
                            location.reload();
                        } else {
                            alert(result.message || 'Có lỗi xảy ra. Vui lòng thử lại.');
                        }
                    });
                }
            });
        });

        document.querySelectorAll('.delete-application-button').forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                const applicationId = this.dataset.applicationId;
                if (confirm("Bạn chắc chắn muốn xóa hồ sơ này?")) {
                    const formData = new FormData();
                    formData.append('application_id', applicationId);

                    fetch('includes/delete_application.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())  
                    .then(result => {
                        if (result.status === 'success') {
                            alert('Hồ sơ đã bị xóa.');
                            location.reload();
                        } else {
                            alert(result.message || 'Có lỗi xảy ra. Vui lòng thử lại.');
                        }
                    });
                }
            });
        });
    });


</script>