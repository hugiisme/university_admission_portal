<?php 
    session_start();
    include_once("../config/database.php"); 
    include_once("../includes/add_notification.php");
    include_once("../includes/display_notifications.php");

    $isValid = true;
    $username_error = "";
    $password_error = "";

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = isset($_POST['username']) ? trim($_POST['username']) : '';
        $password = isset($_POST['password']) ? trim($_POST['password']) : '';
       
        if (empty($username)){
            $username_error = "Chưa nhập tên đăng nhập";
            $isValid = false;
        }

        if (empty($password)){
            $password_error = "Chưa nhập mật khẩu";
            $isValid = false;
        }

        if($isValid){
           $findUserQuery = "SELECT * FROM users WHERE username = '$username'";
           $findUserResult = mysqli_query($conn, $findUserQuery);
           if (mysqli_num_rows($findUserResult) == 0){
                $username_error = "Sai tên đăng nhập";
           } else {
                $rows = mysqli_fetch_assoc($findUserResult);
                // if (password_verify($password, $rows["password"])){

                // }

                if(md5($password) == $rows["password"]){
                    add_notification("Đăng nhập thành công", 3000, "success");
                    $_SESSION["user_id"] = $rows["user_id"];
                    $_SESSION["username"] = $username;
                    $_SESSION["user"] = $rows["name"];
                    $_SESSION["role"] = $rows["role"];
                    
                    header("Location: ../index.php");
                } else {
                    $password_error = "Sai mật khẩu";
                }
           }
        } else {
            add_notification("Đăng nhập thất bại", 3000, "error");
        }
    }    
    mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../assets/css/auth.css">
    <link rel="stylesheet" href="../assets/css/notification.css">
    <script src="../assets/js/notification.js" defer></script>
</head>
<body>
    <form action="" method="POST">
        <h1>Đăng nhập</h1>
        <div class="input-container">
            <input type="text" name="username" id="username" value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>" placeholder="">
            <label for="username" >Tên đăng nhập</label>
            <i class='bx bx-user icon'></i>
            <div class="error-message" id = "username-error"><?php echo $username_error; ?></div>
        </div>
        <div class="input-container">
            <input type="password" name="password" id="password" value="<?php echo isset($password) ? htmlspecialchars($password) : ''; ?>" placeholder="">
            <label for="password">Mật khẩu</label>
            <i class='bx bx-lock-alt icon' onclick="togglePassword('password')"></i>
            <div class="error-message" id = "password-error"><?php echo $password_error; ?></div>
        </div>

        <input type="submit" name="login-btn" class="submit-btn" value="Đăng nhập">
        <div class="suggestion">
            Chưa có tài khoản? <a href="register.php">Đăng ký.</a>
        </div>
    </form>
    <div class="notifications">
        <?php display_notifications(); ?>
    </div>
    <script src="../assets/js/toggle-password.js"></script>
</body>
</html>

<!-- TODO: thông báo đăng nhập chưa hiển thị -->