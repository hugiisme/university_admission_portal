<aside class="sidebar">
    <h1 class="logo">Đăng ký học bạ</h1>
    <nav class="navigation">
        <ul class="menu">
            <li class="menu-item <?php echo $page == "home" ? 'active' : '' ?>"><a class="nav-a" href="index.php?page=home">Trang chủ</a></li>
            <!-- <li class="menu-item <?php echo $page == "submit_profile" ? 'active' : '' ?>"><a class="nav-a" href="index.php?page=submit_profile">Nộp hồ sơ</a></li> -->
            <li class="menu-item <?php echo $page == "profile_detail" ? 'active' : '' ?>"><a class="nav-a" href="index.php?page=profile_detail">Xem hồ sơ</a></li>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <li class="menu-item">
                    <a class="nav-a" href="#">Các chức năng admin</a>
                    <ul class="submenu">
                        <li class="menu-item <?php echo $page == "profile_statistic" ? 'active' : '' ?>"><a class="nav-a" href="index.php?page=profile_statistic">Thống kê hồ sơ</a></li>
                        <li class="menu-item <?php echo $page == "user_statistic" ? 'active' : '' ?>"><a class="nav-a" href="index.php?page=user_statistic">Thống kê người dùng</a></li>
                    </ul>
                </li>
            <?php endif; ?>
            <li class="menu-item <?php echo $page == "account_detail" ? 'active' : '' ?>"><a class="nav-a" href="index.php?page=account_detail">Tài khoản</a></li>
        </ul>
        <a href="auth/logout.php" class="nav-a logout-btn">Đăng xuất</a>
    </nav>
</aside>
