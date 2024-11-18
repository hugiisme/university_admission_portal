<?php  ?>
<aside class="sidebar">
    <h1 class="logo">Đăng ký học bạ</h1>
    <nav class="navigation">
        <ul class="menu">
            <li class="menu-item <?php echo $page == "home" ? 'active' : '' ?>"><a class="nav-a" href="index.php?page=home">Trang chủ</a></li>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <li class="menu-item">
                    <a class="nav-a" href="#">Các chức năng admin</a>
                    <ul class="submenu">
                        <li class="menu-item <?php echo $page == "submit_profile" ? 'active' : '' ?>"><a class="nav-a" href="index.php?page=submit_profile">Thống kê hồ sơ</a></li>
                        <li class="menu-item <?php echo $page == "user_statistic" ? 'active' : '' ?>"><a class="nav-a" href="index.php?page=user_statistic">Thống kê người dùng</a></li>
                        <li class="menu-item <?php echo $page == "assign_teacher" ? 'active' : '' ?>"><a class="nav-a" href="index.php?page=assign_teacher" >Phân ngành giáo viên</a></li>
                        <li class="menu-item <?php echo $page == "major_management" ? 'active' : '' ?>"><a class="nav-a" href="index.php?page=major_management" >Quản lý ngành</a></li>
                        <li class="menu-item <?php echo $page == "block_management" ? 'active' : '' ?>"><a class="nav-a" href="index.php?page=block_management" >Quản lý khối</a></li>
                    </ul>
                </li>
            <?php endif; ?>
            <li class="menu-item <?php echo $page == "account_detail" ? 'active' : '' ?>"><a class="nav-a" href="index.php?page=account_detail">Tài khoản</a></li>
        </ul>
        <a href="auth/logout.php" class="nav-a logout-btn">Đăng xuất</a>
    </nav>
</aside>

<script>
    document.querySelectorAll('.menu-item > .nav-a').forEach(item => {
        item.addEventListener('click', function(event) {
            const submenu = this.nextElementSibling;
            if (submenu && submenu.classList.contains('submenu')) {
                submenu.style.display = submenu.style.display === 'block' ? 'none' : 'block';
                event.preventDefault();
            }
        });
    });
</script>
