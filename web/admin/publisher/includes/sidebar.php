/* Updated: 1760969516.8231413 */
<?php
// 현재 페이지의 위치를 파악해서 base_path 자동 설정
$current_file = basename($_SERVER['PHP_SELF']);
$current_dir = basename(dirname($_SERVER['PHP_SELF']));

// settings 폴더에 있으면 ../, 아니면 ./
if ($current_dir == 'settings') {
    $base_path = '../';
} else {
    $base_path = './';
}
?>
<style>
/* 사이드바 스타일 - 보라색 그라디언트 */
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
            color: white;
    background: rgba(255,255,255,0.15);
    color: white;
    border-left-color: rgba(255,255,255,0.5);
}

.sidebar-menu a.active {
            color: white;
    background: rgba(255,255,255,0.2);
    color: white;
    border-left-color: #fff;
    font-weight: 600;
}

body {
    margin: 0;
    padding: 0;
}

.main-content {
    margin-left: 240px;
}
</style>

