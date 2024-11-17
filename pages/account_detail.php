<link rel="stylesheet" href="assets/css/account-info.css">
<?php
    include_once("auth/session.php");
    include_once("config/database.php");
    include_once("includes/add_notification.php");
    include_once("includes/display_notifications.php");

    function getUserInfo($conn){
        $userInfoQuery = "  SELECT *
        FROM users
        WHERE user_id = '{$_SESSION["user_id"]}'";
        $userInfoResult = mysqli_query($conn, $userInfoQuery);
        return mysqli_fetch_assoc($userInfoResult);
    }
    
    function changeInfo($conn, $userInfo) {
        $isValid = true;
        $username = mysqli_real_escape_string($conn, $_POST["username"] ?? "");
        $name = mysqli_real_escape_string($conn, $_POST["name"] ?? "");
        $email = mysqli_real_escape_string($conn, $_POST["email"] ?? "");
        $errors = [];
    
        if (empty($username)) {
            $errors["usernameError"] = "Chưa nhập tên đăng nhập";
            $isValid = false;
        }
    
        if (empty($name)) {
            $errors["nameError"] = "Chưa nhập tên người dùng";
            $isValid = false;
        }
    
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors["emailError"] = "Chưa nhập email hoặc email không hợp lệ";
            $isValid = false;
        }
    
        if (!empty($_FILES["avatar"]["name"])) {
            $allowedExtensions = ['jpg', 'jpeg', 'png'];
            $fileExtension = strtolower(pathinfo($_FILES["avatar"]["name"], PATHINFO_EXTENSION));
            $fileMimeType = mime_content_type($_FILES["avatar"]["tmp_name"]);
            
            if (!in_array($fileMimeType, ['image/jpeg', 'image/png'])) {
                $errors["avatarError"] = "Vui lòng chọn một ảnh hợp lệ (jpg, jpeg, png).";
                $isValid = false;
            }
            if ($_FILES["avatar"]["size"] > 5 * 1024 * 1024) {
                $errors["avatarError"] = "Ảnh phải có dung lượng dưới 5MB.";
                $isValid = false;
            }
        }
    
        if ($isValid) {
            $updateInfoQuery = "UPDATE users SET username = '$username', name = '$name', email = '$email'";
    
            if (isset($_POST["delete_avatar"])) {
                $currentAvatar = $userInfo["avatar"];
                
                if ($currentAvatar && $currentAvatar !== "default_avatar" && file_exists("uploads/avatars/$currentAvatar")) {
                    if (unlink("uploads/avatars/$currentAvatar")) {
                        $updateInfoQuery .= ", avatar = NULL";
                    } else {
                        $errors["avatarError"] = "Không thể xóa ảnh đại diện.";
                        $isValid = false;
                    }
                }  
            } elseif (!empty($_FILES["avatar"]["name"])) {
                $avatarFilename = uniqid('avatar_') . '.' . $fileExtension;
                $avatarPath = "uploads/avatars/" . $avatarFilename;
    
                if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $avatarPath)) {
                    $updateInfoQuery .= ", avatar = '$avatarFilename'";
                } else {
                    $errors["avatarError"] = "Không thể tải lên ảnh đại diện.";
                    $isValid = false;
                }
            }
    
            $updateInfoQuery .= " WHERE user_id = '{$_SESSION["user_id"]}'";
            if ($isValid) {
                if (mysqli_query($conn, $updateInfoQuery)) {
                    add_notification("Thông tin đã được cập nhật thành công!", 5000, "success");
                    header("Location: index.php?page=account_detail&section=info");
                } else {
                    add_notification("Có lỗi xảy ra trong quá trình cập nhật: " . mysqli_error($conn), 5000, "error");
                }
            } else {
                add_notification("Có lỗi xảy ra: " . implode(" ", $errors), 5000, "error");
            }
        }
        return $errors;
    }    

    function changePassword($conn, $userInfo){
        $isValid = true;
        $old_password = $_POST["old_password"] ?? "";
        $new_password = $_POST["new_password"] ?? "";
        $new_password_confirmation = $_POST["new_password_confirmation"] ?? "";
        $errors = [];

        if(empty($old_password)){
            $errors["oldPasswordError"] = "Chưa xác nhận mật khẩu cũ";
            $isValid = false;
        } if (md5($old_password) != $userInfo["password"]){
            $errors["oldPasswordError"] = "Mật khẩu cũ không chính xác";
            $isValid = false;
        }

        if(empty($new_password)){
            $errors["newPasswordError"] = "Chưa nhập mật khẩu mới";
            $isValid = false;
        }
        if(empty($new_password_confirmation)){
            $errors["newPasswordConfirmationError"] = "Chưa xác nhận mật khẩu mới";
            $isValid = false;
        } elseif ($new_password_confirmation != $new_password){
            $errors["newPasswordConfirmationError"] = "Mật khẩu không trùng khớp";
            $isValid = false;
        }

        if($isValid){
            $new_password_hashed = md5($new_password);
            $updatePasswordQuery = "UPDATE users
                                    SET password = '$new_password_hashed'
                                    WHERE user_id = '{$_SESSION["user_id"]}'";
            if(mysqli_query($conn, $updatePasswordQuery)){
                add_notification("Mật khẩu đã được cập nhật thành công!", 5000, "success");
                header("Location: index.php?page=account_detail&section=password");
            } else {
                add_notification("Có lỗi xảy ra trong quá trình cập nhật: " . mysqli_error($conn), 5000, "error");
            }
        }
        return $errors;
    }
    
    function deleteAccount($conn, $userInfo) {
        $isValid = true;
        $password = $_POST["password"] ?? "";
        $errors = [];

        if(empty($password)){
            $errors["passwordError"] = "Chưa xác nhận mật khẩu cũ";
            $isValid = false;
        } elseif (md5($password) != $userInfo["password"]){
            $errors["passwordError"] = "Mật khẩu cũ không chính xác";
            $isValid = false;
        } 
        
        if($isValid){
            $deleteQuery = "DELETE FROM users WHERE user_id = '{$_SESSION["user_id"]}'";
            if(mysqli_query($conn, $deleteQuery)){
                add_notification("Đã xóa tài khoản thành công!", 5000, "success");
                header("Location: auth/logout.php");
            } else {
                add_notification("Có lỗi xảy ra trong quá trình xóa: " . mysqli_error($conn), 5000, "error");
            }
        }
        return $errors;
    }

    $section = isset($_GET["section"]) ? $_GET["section"] : "info";
    $userInfo = getUserInfo($conn);
    $errors = [];
    if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["submit_change_info_button"]) && $section == "info"){
        $errors = changeInfo($conn, $userInfo);
    } elseif($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["submit_change_password_button"]) && $section == "password"){
        $errors = changePassword($conn, $userInfo);
    } elseif($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["submit_delete_account_button"]) && $section == "delete"){
        $errors = deleteAccount($conn, $userInfo);
    }
    mysqli_close($conn);
?>
<main>
    <div class="account-page-wrapper">
        <div class="account-info-container">
            <h2>Hồ sơ người dùng</h2>
            <img src="uploads/avatars/<?php echo $userInfo["avatar"] === null ? "default_avatar.jpg" :  $userInfo["avatar"] ?>" alt="avatar">
            <div class="account-infos">
                <div class="account-info">Tên đăng nhập: <span class="info"> <?php echo $userInfo["username"] ?></span></div>
                <div class="account-info">Tên người dùng: <span class="info"> <?php echo $userInfo["name"] ?></span></div>
                <div class="account-info">Email: <span class="info"><?php echo $userInfo["email"] ?></span></div>
                <div class="account-info">Vai trò: <span class="info"><?php echo $userInfo["role"] ?></span></div>
            </div>
        </div>
        <div class="change-info-section">
            <div class="sections">
                <div class="section"><a href="index.php?page=account_detail&section=info" class="info-header-button <?php echo $section == "info" ? "choosed" : ""  ?>">Đổi thông tin cá nhân</a></div>
                <div class="section"><a href="index.php?page=account_detail&section=password" class="info-header-button <?php echo $section == "password" ? "choosed" : ""  ?>">Đổi mật khẩu</a></div>
                <div class="section"><a href="index.php?page=account_detail&section=delete" class="info-header-button <?php echo $section == "delete" ? "choosed" : ""  ?>">Xóa tài khoản</a></div>
            </div>
            <?php if($section == "info"): ?>
                <form action="" method="post" class="change-info-form" enctype="multipart/form-data">
                    <div class="change-info-container">
                        <div class="change-info-input-container">
                            <label for="">Tên đăng nhập</label>
                            <input type="text" name="username" value="<?php echo $userInfo["username"] ?>">
                        </div>
                        <div class="error"><?php echo $errors["usernameError"] ?? ""; ?></div>
                    </div>
                    <div class="change-info-container">
                        <div class="change-info-input-container">
                            <label for="">Tên người dùng</label>
                            <input type="text" name="name" value="<?php echo $userInfo["name"] ?>">
                        </div>
                        <div class="error"><?php echo $errors["nameError"] ?? ""; ?></div>
                    </div>
                    <div class="change-info-container">
                        <div class="change-info-input-container">
                            <label for="">Email</label>
                            <input type="email" name="email" value="<?php echo $userInfo["email"] ?>">
                        </div>
                        <div class="error"><?php echo $errors["emailError"] ?? ""; ?></div>
                    </div>
                    <div class="change-info-container">
                        <div class="change-info-input-container">
                            <label for="">Chọn ảnh đại diện mới</label>
                            <input type="file" name="avatar">
                        </div>
                        <div class="change-info-input-container">
                            <label for="">Xóa ảnh đại diện? <input type="checkbox" name="delete_avatar"></label>
                        </div>
                        <div class="error"><?php echo $errors["avatarError"] ?? ""; ?></div>
                    </div>

                    <input type="submit" value="Xác nhận thay đổi thông tin" name="submit_change_info_button" class="positive-button change-info-btn" onclick="return confirm('Bạn có chắc muốn thay đổi những thông tin trên?')">
                </form>
            <?php elseif($section == "password"): ?>  
                <form action="" method="post" class="change-info-form">
                    <div class="change-info-container">
                        <div class="change-info-input-container">
                            <label for="">Mật khẩu cũ: </label>
                            <input type="password" name="old_password" id="old-password" value="<?php echo $_POST["old_password"] ?? ""; ?>">
                            <i class='bx bx-lock-alt icon' onclick="togglePassword('old-password')"></i>
                        </div>
                        
                        <div class="error"><?php echo $errors["oldPasswordError"] ?? "";?></div>
                    </div>
                    <div class="change-info-container">
                        <div class="change-info-input-container">
                            <label for="">Mật khẩu mới</label>
                            <input type="password" name="new_password" id="new-password" value="<?php echo $_POST["new_password"] ?? ""; ?>">
                            <i class='bx bx-lock-alt icon' onclick="togglePassword('new-password')"></i>
                        </div>
                        
                        <div class="error"><?php echo $errors["newPasswordError"] ?? "";?></div>
                    </div>
                    <div class="change-info-container">
                        <div class="change-info-input-container">
                            <label for="">Xác nhận mật khẩu mới</label>
                            <input type="password" name="new_password_confirmation" id="new-password-confirmation" value="<?php echo $_POST["new_password_confirmation"] ?? ""; ?>">
                            <i class='bx bx-lock-alt icon' onclick="togglePassword('new-password-confirmation')"></i>
                        </div>
                        
                        <div class="error"><?php echo $errors["newPasswordConfirmationError"] ?? "";?></div>
                    </div>
                    <input type="submit" value="Xác nhận thay đổi mật khẩu" name="submit_change_password_button" class="negative-button change-info-btn" onclick="return confirm('Bạn có chắc muốn thay đổi mật khẩu của mình?')">
                </form>
            <?php elseif($section == "delete"): ?> 
                <form action="" method="post" class="change-info-form">
                    <div class="change-info-container">
                        <div class="change-info-input-container">
                            <label for="">Xác nhận mật khẩu: </label>
                            <input type="password" name="password" id="password" value="<?php echo $_POST["password"] ?? ""; ?>">
                            <i class='bx bx-lock-alt icon' onclick="togglePassword('password')"></i>
                        </div>
                        <div class="error"><?php echo $errors["passwordError"] ?? "";?></div>
                    </div>
                    <input type="submit" value="Xóa tài khoản" name="submit_delete_account_button" class="negative-button change-info-btn" onclick="return confirm('Bạn có chắc muốn xóa tài khoản của mình?')">
                </form>
            <?php endif; ?>
        </div>
        
</main>