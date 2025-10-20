<?php
// 현재 페이지 위치 파악
$current_file = basename($_SERVER['PHP_SELF']);
$current_dir = basename(dirname($_SERVER['PHP_SELF']));

// 경로 설정
if ($current_dir == 'settings' || $current_dir == 'books' || $current_dir == 'genres' || $current_dir == 'orders') {
    $base_path = '../';
} else {
    $base_path = './';
}
?>

<style>
.sidebar {
    width: 240px;
    background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
    color: white;
    min-height: 100vh;
    padding: 0;
    position: fixed;
    left: 0;
    top: 0;
    z-index: 1000;
    box-shadow: 2px 0 10px rgba(0,0,0,0.1);
}
.sidebar-header {
    padding: 30px 20px;
    font-size: 22px;
    font-weight: bold;
    color: white;
    background: rgba(255,255,255,0.1);
    border-bottom: 1px solid rgba(255,255,255,0.2);
    text-align: center;
}
.sidebar-menu {
    list-style: none;
    padding: 20px 0;
    margin: 0;
}
.sidebar-menu li {
    margin-bottom: 2px;
}
.sidebar-menu a {
    display: flex;
    align-items: center;
    padding: 15px 20px;
    color: rgba(255,255,255,0.9);
    text-decoration: none;
    transition: all 0.3s ease;
    font-size: 15px;
    border-left: 3px solid transparent;
}
.sidebar-menu a:hover {
    background: rgba(255,255,255,0.15);
    color: white;
    border-left-color: rgba(255,255,255,0.5);
}
.sidebar-menu a.active {
    background: rgba(255,255,255,0.2);
    color: white;
    border-left-color: #fff;
    font-weight: 600;
}
.main-content {
    margin-left: 260px;
    padding: 20px;
}
</style>

<!-- 사이드바 HTML -->
<div class="sidebar">
    <div class="sidebar-header">
        📖 HeroComics
    </div>
    <ul class="sidebar-menu">
        <li>
            <a href="<?php echo $base_path; ?>dashboard.php" class="<?php echo $current_file == 'dashboard.php' ? 'active' : ''; ?>">
                📊 대시보드
            </a>
        </li>
        <li>
            <a href="<?php echo $base_path; ?>books/list.php" class="<?php echo $current_dir == 'books' ? 'active' : ''; ?>">
                📚 책 관리
            </a>
        </li>
        <li>
            <a href="<?php echo $base_path; ?>books/book_upload.php" class="<?php echo strpos($current_file, 'upload') !== false ? 'active' : ''; ?>">
                📖 책 추가
            </a>
        </li>
        <li>
            <a href="<?php echo $base_path; ?>genres/list.php" class="<?php echo $current_dir == 'genres' ? 'active' : ''; ?>">
                🏷️ 장르 관리
            </a>
        </li>
        <li>
            <a href="<?php echo $base_path; ?>orders/list.php" class="<?php echo $current_dir == 'orders' ? 'active' : ''; ?>">
                🛒 주문 관리
            </a>
        </li>
        <li>
            <a href="<?php echo $base_path; ?>sales/dashboard.php" class="<?php echo $current_dir == 'sales' ? 'active' : ''; ?>">
                💰 매출/정산
            </a>
        </li>
        <li>
            <a href="<?php echo ($current_dir == 'settings') ? 'profile.php' : $base_path . 'settings/profile.php'; ?>" class="<?php echo $current_dir == 'settings' ? 'active' : ''; ?>">
                ⚙️ 설정
            </a>
        </li>
    </ul>
</div>
