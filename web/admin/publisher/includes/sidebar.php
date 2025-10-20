<?php
// 공용 사이드바
$current_file = basename($_SERVER['PHP_SELF']);
$current_dir = basename(dirname($_SERVER['PHP_SELF']));

// 경로 설정
if (in_array($current_dir, ['settings', 'books', 'genres', 'orders', 'sales'])) {
    $base_path = '../';
} else {
    $base_path = './';
}
?>

<style>
.sidebar {
    width: 250px;
    background: #FF8C42; /* 더 진한 주황색 */
    color: white;
    min-height: 100vh;
    padding: 20px 0;
    position: fixed;
    left: 0;
    top: 0;
    z-index: 1000;
    box-shadow: 2px 0 5px rgba(0,0,0,0.1);
}

.sidebar-menu {
    list-style: none;
    padding: 0;
    margin: 0;
}

.sidebar-menu li {
    margin: 0;
}

.sidebar-menu a {
    display: flex;
    align-items: center;
    padding: 15px 20px;
    color: white;
    text-decoration: none;
    transition: all 0.3s ease;
    font-size: 16px;
    font-weight: 500;
}

.sidebar-menu a:hover {
    background: rgba(0,0,0,0.1);
    padding-left: 25px;
}

.sidebar-menu a.active {
    background: rgba(0,0,0,0.2);
    border-left: 4px solid white;
    font-weight: 600;
}

.sidebar-menu a i {
    width: 25px;
    margin-right: 12px;
    font-size: 18px;
}

.main-content {
    margin-left: 270px;
    padding: 20px;
}
</style>

<!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<div class="sidebar">
<ul class="sidebar-menu">
        <li>
            <a href="<?php echo $base_path; ?>dashboard.php" class="<?php echo $current_file == 'dashboard.php' ? 'active' : ''; ?>">
                <i class="fas fa-chart-line"></i>
                <span>대시보드</span>
            </a>
        </li>
        <li>
            <a href="<?php echo $base_path; ?>books/list.php" class="<?php echo $current_dir == 'books' && strpos($current_file, 'list') !== false ? 'active' : ''; ?>">
                <i class="fas fa-book"></i>
                <span>책 관리</span>
            </a>
        </li>
        <li>
            <a href="<?php echo $base_path; ?>books/book_upload.php" class="<?php echo strpos($current_file, 'upload') !== false ? 'active' : ''; ?>">
                <i class="fas fa-plus-circle"></i>
                <span>책 추가</span>
            </a>
        </li>
        <li>
            <a href="<?php echo $base_path; ?>genres/list.php" class="<?php echo $current_dir == 'genres' ? 'active' : ''; ?>">
                <i class="fas fa-tags"></i>
                <span>장르 관리</span>
            </a>
        </li>
        <li>
            <a href="<?php echo $base_path; ?>orders/list.php" class="<?php echo $current_dir == 'orders' ? 'active' : ''; ?>">
                <i class="fas fa-shopping-cart"></i>
                <span>주문 관리</span>
            </a>
        </li>
        <li>
            <a href="<?php echo $base_path; ?>sales/dashboard.php" class="<?php echo $current_dir == 'sales' ? 'active' : ''; ?>">
                <i class="fas fa-dollar-sign"></i>
                <span>매출/정산</span>
            </a>
        </li>
        <li>
            <a href="<?php echo ($current_dir == 'settings') ? 'profile.php' : $base_path . 'settings/profile.php'; ?>" class="<?php echo $current_dir == 'settings' ? 'active' : ''; ?>">
                <i class="fas fa-cog"></i>
                <span>설정</span>
            </a>
        </li>
    </ul>
</div>
