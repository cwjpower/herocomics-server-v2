<?php
// 대시보드 기준 공통 사이드바
$current_file = basename($_SERVER['PHP_SELF']);
$current_dir = basename(dirname($_SERVER['PHP_SELF']));

if (in_array($current_dir, ['settings', 'books', 'genres', 'orders', 'sales'])) {
    $base_path = '../';
} else {
    $base_path = './';
}
?>

<style>
.sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            color: white;
            overflow-y: auto;
        }
        
        .sidebar .logo {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .sidebar .menu-item {
            padding: 12px 15px;
            margin-bottom: 5px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            text-decoration: none;
            color: rgba(255, 255, 255, 0.8);
        }
        
        .sidebar .menu-item:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }
        
        .sidebar .menu-item.active {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }
        
        .sidebar .menu-item i {
            width: 20px;
            margin-right: 10px;
        }
</style>

<div class="sidebar">
        <div class="logo">
            <i class="fas fa-book-open"></i> HeroComics
        </div>
