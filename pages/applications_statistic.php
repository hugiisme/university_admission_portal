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
            'major_id' => $_GET["major_id"] ?? '',
            'search_by' => $_GET['search_by'] ?? '',
            'search_value' => $_GET['search_value'] ?? '',
            'sort_by_major_name' => $_GET['sort_by_major_name'] ?? '',
            'sort_by_student_name' => $_GET['sort_by_student_name'] ?? '',
            'sort_by_verifier_name' => $_GET['sort_by_verifier_name'] ?? '',
            'sort_by_application_id' => $_GET['sort_by_application_id'] ?? '',
            'filter_by_status' => $_GET['filter_by_status'] ?? '',
            'filter_by_blocks' => $_GET['filter_by_blocks'] ?? ''
        ];
    }

    function buildApplicationQuery($major_id, $search_by, $search_value, $sort_by_major_name, $sort_by_student_name, $sort_by_verifier_name, $sort_by_application_id, $filter_by_status, $filter_by_blocks) {
        $query = "SELECT 
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
                            LEFT JOIN major_teachers mt ON mt.major_id = m.major_id
                            WHERE 1=1";
        if ($major_id) {
            $query .= " AND a.major_id = $major_id";
        }
        
        if($search_by && $search_value){
            $query .= " AND $search_by LIKE '%$search_value%'";
        }

        if($filter_by_status){
            $query .= " AND status = '$filter_by_status'";
        }

        if ($filter_by_blocks) {
            $query .= " AND b.code = '$filter_by_blocks'";
        }

        if ($_SESSION["role"] == "teacher") {
            $query .= " AND mt.user_id = {$_SESSION['user_id']}";
        }

        $orderByClauses = [];

        if ($sort_by_major_name === 'major_name_asc') {
            $orderByClauses[] = "m.name ASC";
        } elseif ($sort_by_major_name === 'major_name_desc') {
            $orderByClauses[] = "m.name DESC";
        }

        if ($sort_by_student_name === 'student_name_asc') {
            $orderByClauses[] = "s.name ASC";
        } elseif ($sort_by_student_name === 'student_name_desc') {
            $orderByClauses[] = "s.name DESC";
        }

        if ($sort_by_verifier_name === 'verifier_name_asc') {
            $orderByClauses[] = "v.name ASC";
        } elseif ($sort_by_verifier_name === 'verifier_name_desc') {
            $orderByClauses[] = "v.name DESC";
        }

        if ($sort_by_application_id === 'application_id_asc') {
            $orderByClauses[] = "a.application_id ASC";
        } elseif ($sort_by_application_id === 'application_id_desc') {
            $orderByClauses[] = "a.application_id DESC";
        }

        if (!empty($orderByClauses)) {
            $query .= " ORDER BY " . implode(", ", $orderByClauses);
        }

       return $query;
    }

    function fetchApplication($conn, $query, $itemPerPage, $offset){
        $query .= " LIMIT $itemPerPage OFFSET $offset";
        $applicationResult = mysqli_query($conn, $query);
        return $applicationResult;
    }

    function totalResult($conn, $query){
        $totalResult = mysqli_num_rows(mysqli_query($conn, $query)); 
        return $totalResult;
    }

    function renderApplicationCard($applicationRows){
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

            echo "<div class='bottom-cards-container'>";
                $acceptButtonDisabled = $applicationRows["status"] == "accepted" ; // || $applicationRows["verifier_id"] != $_SESSION["user_id"]
                $denyButtonDisabled = $applicationRows["status"] == "denied" ; // || $applicationRows["verifier_id"] != $_SESSION["user_id"]

                $disableAcceptButton = $acceptButtonDisabled ? "disabled" : '';
                $disableDenyButton = $denyButtonDisabled ? "disabled" : '';

                $disableAcceptClass = $acceptButtonDisabled ? "disabled-button" : '';
                $disableDenyClass = $denyButtonDisabled ? "disabled-button" : '';
                echo "<button class='accept-application-btn positive-button bottom-card-button $disableAcceptClass' data-application-id='{$applicationRows["application_id"]}' " . $disableAcceptButton . ">Duyệt</button>";
                echo "<button class='deny-application-btn negative-button bottom-card-button $disableDenyClass' data-application-id='{$applicationRows["application_id"]}' " . $disableDenyButton . ">Không duyệt</button>";
                echo "<button class='profile-detail-button neutral-button' data-application-id='{$applicationRows["application_id"]}'>Xem hồ sơ chi tiết</button>";
                if($_SESSION["role"] == "admin"){
                    echo "<button class='delete-application-button negative-button' data-application-id='{$applicationRows["application_id"]}'>&times;</button>";
                }
            echo "</div>";
        echo "</div>";
    }
       
    function fetchBlocks($conn) {
        $blocksQuery = "SELECT * FROM blocks";
        $blocksResult = mysqli_query($conn, $blocksQuery);
        return mysqli_fetch_all($blocksResult, MYSQLI_ASSOC);
    }

    if ($_SERVER["REQUEST_METHOD"] === "GET"){
        $queryOption = initializeQueryOptions();
        $major_id = $queryOption["major_id"];
        $search_by = $queryOption["search_by"];
        $search_value = $queryOption["search_value"];
        $sort_by_major_name = $queryOption["sort_by_major_name"];
        $sort_by_student_name = $queryOption["sort_by_student_name"];
        $sort_by_verifier_name = $queryOption["sort_by_verifier_name"];
        $sort_by_application_id = $queryOption["sort_by_application_id"];
        $filter_by_status = $queryOption["filter_by_status"];
        $filter_by_blocks = $queryOption["filter_by_blocks"];
    }

    $currentPage = isset($_GET['page_index']) && $_GET['page_index'] > 0 ? (int)$_GET['page_index'] : 1;;
    $itemPerPage = 6;
    $offset = ($currentPage - 1) * $itemPerPage;

    $blocksRows = fetchBlocks($conn);
    $applicationQuery = buildApplicationQuery($major_id, $search_by, $search_value, $sort_by_major_name, $sort_by_student_name, $sort_by_verifier_name, $sort_by_application_id, $filter_by_status, $filter_by_blocks);
    $applicationResult = fetchApplication($conn, $applicationQuery, $itemPerPage, $offset);
    $totalResult = totalResult($conn, $applicationQuery);
    $totalPages = ceil($totalResult / $itemPerPage);

?>

<h1 class="page-title">Danh sách hồ sơ</h1>
<form method="get" class="query-option-form" onchange="this.submit()">
    <div class="query-option-input"> 
        <input type="hidden" name="page" value="submit_profile">
            <div class="search-container">
                <label for="search_by">Tìm kiếm theo: </label>
                <select name="search_by">
                    <option value="m.name" <?php echo isset($_GET['search_by']) && $_GET['search_by'] == 'm.name' ? 'selected' : ''; ?>>Tên ngành</option>
                    <option value="s.name" <?php echo isset($_GET['search_by']) && $_GET['search_by'] == 's.name' ? 'selected' : ''; ?>>Tên người nộp</option>
                    <option value="v.name" <?php echo isset($_GET['search_by']) && $_GET['search_by'] == 'v.name' ? 'selected' : ''; ?>>Tên người duyệt</option>
                </select>
                <input type="text" name="search_value" value="<?php echo isset($_GET["search_value"]) ? $_GET["search_value"] : '' ?>">
            </div>
        <div class="filter-sort-option">
            <div class="sort-by">
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
            <div class="filter-by">
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
    <?php if($totalResult > 0): ?>
        <div class='total-result'>Tổng số kết quả: <?php echo $totalResult?> </div>
        <div class='application-cards cards-container'>
            <?php 
                while($applicationRows = mysqli_fetch_assoc($applicationResult)){
                    renderApplicationCard($applicationRows);
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


<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.accept-application-btn').forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                const applicationId = this.dataset.applicationId;
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
                        location.reload();
                    } else {
                        alert(result.message || 'Có lỗi xảy ra. Vui lòng thử lại.');
                    }
                });
            });
        });

        document.querySelectorAll('.deny-application-btn').forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                const applicationId = this.dataset.applicationId;
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
                        location.reload();
                    } else {
                        alert(result.message || 'Có lỗi xảy ra. Vui lòng thử lại.');
                    }
                });
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
                            alert('Xoá hồ sơ thành công');
                            location.reload();
                        } else {
                            alert(result.message || 'Có lỗi xảy ra. Vui lòng thử lại');
                        }
                    });
                }
            });
        });

        document.querySelectorAll('.profile-detail-button').forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                const applicationId = this.dataset.applicationId;

                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'index.php?page=profile_detail'; 

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'application_id';
                input.value = applicationId;

                form.appendChild(input);

                document.body.appendChild(form);

                form.submit();
            });
        });
    });


</script>