<?php
    include_once("../config/database.php");
    include_once("../includes/add_notification.php");
    include_once("../includes/display_notifications.php");

    function validate_input($data, &$error, $field_name, $empty_message) {
        if (empty($data)) {
            $error = $empty_message;
            add_notification($error, 3000, "error");
            return false;
        }
        return true;
    }

    function validate_email($email, &$error) {
        if (empty($email)) {
            $error = "Chưa nhập email";
            add_notification($error, 3000, "error");
            return false;
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Email không hợp lệ";
            add_notification($error, 3000, "error");
            return false;
        }
        return true;
    }

    function validate_password($password, $password_confirmation, &$error) {
        if (empty($password)) {
            $error = "Chưa nhập mật khẩu";
            add_notification($error, 3000, "error");
            return false;
        }
        if (empty($password_confirmation)) {
            $error = "Chưa xác nhận mật khẩu";
            add_notification($error, 3000, "error");
            return false;
        } elseif ($password !== $password_confirmation) {
            $error = "Mật khẩu không trùng khớp";
            add_notification($error, 3000, "error");
            return false;
        }
        return true;
    }

    function check_existing_user($username, $conn, &$error) {
        $sameNameQuery = "SELECT * FROM users WHERE username = '$username'";
        $sameNameResult = mysqli_query($conn, $sameNameQuery);
        if (mysqli_num_rows($sameNameResult) > 0) {
            $error = "Tên đăng nhập đã tồn tại";
            add_notification($error, 3000, "error");
            return true;
        }
        return false;
    }

    function create_user($username, $hashedPassword, $role, $name, $email, $conn) {
        $createUserQuery = "INSERT INTO users (username, password, role, name, email) VALUES ('$username', '$hashedPassword', '$role', '$name', '$email')";
        if (mysqli_query($conn, $createUserQuery)) {
            add_notification("Đăng ký thành công", 3000, "success");
            header("Location: login.php");
            return true;
        } else {
            add_notification("Đăng ký thất bại", 3000, "error");
            return false;
        }
    }

    $isValid = true;
    $username_error = "";
    $name_error = "";
    $email_error = "";
    $password_error = "";
    $password_confirmation_error = "";
    $role_error = "";

    $role = ''; // ko có là lỗi

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = isset($_POST['username']) ? trim($_POST['username']) : '';
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $password = isset($_POST['password']) ? trim($_POST['password']) : '';
        $password_confirmation = isset($_POST['password_confirmation']) ? trim($_POST['password_confirmation']) : '';
        $role = isset($_POST['role']) ? trim($_POST['role']) : '';

        $isValid &= validate_input($username, $username_error, "Tên đăng nhập", "Chưa nhập tên đăng nhập");
        $isValid &= validate_input($name, $name_error, "Tên người dùng", "Chưa nhập tên người dùng");
        $isValid &= validate_email($email, $email_error);
        $isValid &= validate_password($password, $password_confirmation, $password_confirmation_error);
        // &= <=> validate1 && validate2 && validate3...

        if (empty($role)) {
            $role_error = "Chưa chọn vai trò";
            add_notification($role_error, 3000, "error");
            $isValid = false;
        }

        if ($isValid) {
            if (!check_existing_user($username, $conn, $username_error)) {
                $hashedPassword = md5($password);
                create_user($username, $hashedPassword, $role, $name, $email, $conn);
            }
        } else {
            add_notification("Đăng ký thất bại", 3000, "error");
        }
    }

    mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../assets/css/auth.css">
    <link rel="stylesheet" href="../assets/css/notification.css">
    <script src="../assets/js/notification.js" defer></script>
</head>
<body>
    <form action="" method="POST">
        <h1>Đăng Ký</h1>

        <div class="input-container">
            <input type="text" name="username" id="username" value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>" placeholder="">
            <label for="username">Tên đăng nhập</label>
            <i class='bx bx-user-circle icon'></i>
            <div class="error-message" id="username-error"><?php echo $username_error; ?></div>
        </div>

        <div class="input-container">
            <input type="text" name="name" id="name" value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>" placeholder="">
            <label for="name">Tên người dùng</label>
            <i class='bx bx-user icon'></i>
            <div class="error-message" id="name-error"><?php echo $name_error; ?></div>
        </div>

        <div class="input-container">
            <input type="email" name="email" id="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" placeholder="">
            <label for="email">Email</label>
            <i class='bx bx-envelope icon'></i>
            <div class="error-message" id="email-error"><?php echo $email_error; ?></div>
        </div>

        <div class="input-container">
            <input type="password" name="password" id="password" value="<?php echo isset($password) ? htmlspecialchars($password) : ''; ?>" placeholder="">
            <label for="password">Mật khẩu</label>
            <i class='bx bx-lock-alt icon' onclick="togglePassword('password')"></i>
            <div class="error-message" id="password-error"><?php echo $password_error; ?></div>
        </div>

        <div class="input-container">
            <input type="password" name="password_confirmation" id="password-confirmation" value="<?php echo isset($password_confirmation) ? htmlspecialchars($password_confirmation) : ''; ?>" placeholder="">
            <label for="password_confirmation">Xác nhận mật khẩu</label>
            <i class='bx bx-lock-alt icon' onclick="togglePassword('password-confirmation')"></i>
            <div class="error-message" id="password-confirmation-error"><?php echo $password_confirmation_error; ?></div>
        </div>

        <div class="input-container">
            <select name="role" id="role">
                <option value="" <?php echo empty($role) ? 'selected' : ''; ?>>-- Chọn vai trò của bạn --</option>
                <option value="admin" <?php echo ($role == 'admin') ? 'selected' : ''; ?>>Admin</option>
                <option value="teacher" <?php echo ($role == 'teacher') ? 'selected' : ''; ?>>Giáo viên</option>
                <option value="student" <?php echo ($role == 'student') ? 'selected' : ''; ?>>Học sinh</option>
            </select>
            <div class="error-message" id="role-error"><?php echo $role_error; ?></div>
        </div>

        <input type="submit" name="register_btn" class="submit-btn" value="Đăng ký">
        <div class="suggestion">
            Đã có tài khoản? <a href="login.php">Đăng nhập.</a>
        </div>
    </form>
    <div class="notifications">
        <?php display_notifications(); ?>
    </div>

    <script src="../assets/js/toggle-password.js"></script>
</body>
</html>
