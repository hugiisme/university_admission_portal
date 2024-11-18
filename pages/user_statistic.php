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
            'filter_by_role' => $_GET['filter_by_role'] ?? ''
        ];
    }

    function buildUsersQuery($search_by, $search_value, $sort_by_id, $sort_by_name, $filter_by_role){
        $query = "SELECT user_id, username, name, email, role
                  FROM users 
                  WHERE 1=1";
        if($search_by && $search_value){
            $query .= " AND $search_by LIKE '%$search_value%'";
        }

        if($filter_by_role){
            $query .= " AND role = '$filter_by_role'";
        }

        $orderByClauses = [];
        if($sort_by_id == "user_id_asc"){
            $orderByClauses[] = "user_id ASC";
        } elseif ($sort_by_id == "user_id_desc"){
            $orderByClauses[] = "user_id DESC";
        }

        if($sort_by_name == "name_asc"){
            $orderByClauses[] = "name ASC";
        } elseif($sort_by_name == "name_desc"){
            $orderByClauses[] = "name DESC";
        } 

        if (!empty($orderByClauses)) {
            $query .= " ORDER BY " . implode(", ", $orderByClauses);
        }

        return $query;
    }

    function fetchUser($conn, $query, $itemPerPage, $offset){
        $query .= " LIMIT $itemPerPage OFFSET $offset";
        $result = mysqli_query($conn, $query);
        return $result;
    }

    function totalResult($conn, $query){
        $totalResult = mysqli_num_rows(mysqli_query($conn, $query)); 
        return $totalResult;
    }

    function updateUserRole($conn, $user_id, $new_role){
        $checkQuery = "SELECT role FROM users WHERE user_id = '$user_id'";
        $checkResult = mysqli_query($conn, $checkQuery);
        $row = mysqli_fetch_assoc($checkResult);
        if($row["role"] == "admin"){
            add_notification("Không thể thay đổi role cho admin khác", 5000, "error");
            header("Location: " . pageURL());
            exit;
        }

        $query = "UPDATE users SET role = '$new_role' WHERE user_id = $user_id";
        if (mysqli_query($conn, $query)) {
            add_notification("Đã cập nhật role thành công cho người dùng có id: $user_id.", 5000, "success");
        } else {
            add_notification("Không thể cập nhật role cho người dùng có id: $user_id.", 5000, "error");
        }

        header("Location: " . pageURL());
    }

    function pageURL(){
        $queryOption = initializeQueryOptions();
        $search_by = $queryOption["search_by"];
        $search_value = $queryOption["search_value"];
        $sort_by_id = $queryOption["sort_by_id"];
        $sort_by_name = $queryOption["sort_by_name"];
        $filter_by_role = $queryOption["filter_by_role"];
        $page_index = $_GET["page_index"];
        return "index.php?page=user_statistic&search_by=$search_by&search_value=$search_value&sort_by_id=$sort_by_id&sort_by_name=$sort_by_name&filter_by_role=$filter_by_role&page_index=$page_index";
    }

    function renderUserRow($row){
        echo "<tr>";
        echo "<td>{$row['user_id']}</td>";
        echo "<td>{$row['username']}</td>";
        echo "<td>{$row['name']}</td>";
        echo "<td>{$row['email']}</td>";
        echo "<td>
                <form method='post' action='' style = 'display: inline-block; width: 100%;'>
                    <input type='hidden' name='user_id' value='{$row['user_id']}'>
                    <select name='role' onchange='this.form.submit()' style='width: 100%;text-align:center;'>
                        <option value='admin' " . ($row['role'] === 'admin' ? 'selected' : '') . ">Admin</option>
                        <option value='teacher' " . ($row['role'] === 'teacher' ? 'selected' : '') . ">Giáo viên</option>
                        <option value='student' " . ($row['role'] === 'student' ? 'selected' : '') . ">Học sinh</option>
                    </select>
                </form>
            </td>";
        echo "<td>
                <form method='post'>
                    <input type='hidden' name='user_id_to_delete' value='{$row['user_id']}'>
                    <input type='submit' value='Xóa người dùng này' class='negative-button' style='width:100%; padding: 5px;'>
                </form>
            </td>";

        echo "</tr>";
    }

    function deleteUser($conn, $user_id) {
        $user_id = mysqli_real_escape_string($conn, $user_id);
    
        $checkQuery = "SELECT user_id, role FROM users WHERE user_id = '$user_id'";
        $checkResult = mysqli_query($conn, $checkQuery);
    
        if (mysqli_num_rows($checkResult) == 0) {
            add_notification("Người dùng với ID $user_id không tồn tại!", 5000, "error");
            header("Location: " . pageURL());
            exit();
        }

        if(mysqli_fetch_assoc($checkResult)["role"] == "admin"){
            add_notification("Không thể xóa admin khác", 5000, "error");
            header("Location: " . pageURL());
            exit();
        }
    
        $deleteQuery = "DELETE FROM users WHERE user_id = '$user_id'";
        if (mysqli_query($conn, $deleteQuery)) {
            add_notification("Xóa người dùng với ID $user_id thành công!", 5000, "success");
            if($user_id == $_SESSION["user_id"]){
                add_notification("Bạn vừa xóa bản thân", 5000, "success");
                header("Location: auth/logout.php");
                exit;
            }
        } else {
            add_notification("Không thể xóa người dùng với ID $user_id.", 5000, "error");
        }
        
    
        header("Location: " . pageURL());
        exit();
    }

    if ($_SERVER["REQUEST_METHOD"] === "GET"){
        $queryOption = initializeQueryOptions();
        $search_by = $queryOption["search_by"];
        $search_value = $queryOption["search_value"];
        $sort_by_id = $queryOption["sort_by_id"];
        $sort_by_name = $queryOption["sort_by_name"];
        $filter_by_role = $queryOption["filter_by_role"];
    } elseif ($_SERVER["REQUEST_METHOD"] === "POST"){
        if(isset($_POST['user_id'], $_POST['role'])){
            updateUserRole($conn, $_POST['user_id'], $_POST['role']);
        }elseif(isset($_POST["user_id_to_delete"])){
            deleteUser($conn, $_POST["user_id_to_delete"]);
        }
        
    }

    $currentPage = isset($_GET['page_index']) && $_GET['page_index'] > 0 ? (int)$_GET['page_index'] : 1;;
    $itemPerPage = 8;
    $offset = ($currentPage - 1) * $itemPerPage;

    $userQuery = buildUsersQuery($search_by, $search_value, $sort_by_id, $sort_by_name, $filter_by_role);
    $userResult = fetchUser($conn, $userQuery, $itemPerPage, $offset);
    $totalResult = totalResult($conn, $userQuery);
    $totalPages = ceil($totalResult / $itemPerPage);

    mysqli_close($conn);
?>
<main>
    <h1 class="page-title">Thống kê người dùng</h1>
    <form action="" method="get" class="query-option-form" onchange="this.submit()">
        <input type="hidden" name="page" value="user_statistic">
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
                <div class="filter-by">
                    <label for="filter_by_role">Lọc theo: </label>
                    <select name="filter_by_role" id="">
                        <option value="" <?php echo $filter_by_role == "" ? "selected" : '' ?>>Vai trò</option>
                        <option value="admin" <?php echo $filter_by_role == "admin" ? "selected" : '' ?>>Admin</option>
                        <option value="teacher" <?php echo $filter_by_role == "teacher" ? "selected" : '' ?>>Giáo viên</option>
                        <option value="student" <?php echo $filter_by_role == "student" ? "selected" : '' ?>>Học sinh</option>
                    </select>
                </div>
            </div>
        </div>
        
    </form>
    <div class="content">
        <div class="total-result">Tổng số kết quả: <span class="info"><?php echo $totalResult ?></span> </div>
            <table class="user-table">
            <thead>
                <tr>
                    <th style="width: 50px">STT</th>
                    <th>Tên đăng nhập</th>
                    <th>Tên</th>
                    <th>Email</th>
                    <th>Vai trò</th>
                    <th>Xóa người dùng</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($userResult) > 0) {
                    while ($row = mysqli_fetch_assoc($userResult)) {
                        renderUserRow($row);
                    }
                } else {
                    echo "<tr><td colspan='6' class='warning' style='text-align: center; font-size:larger; font-weight: bold;'>Không có kết quả phù hợp</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <div class='pagination'>
            <?php createPagination($currentPage, $totalPages) ?>
        </div>
    </div>
</main>

