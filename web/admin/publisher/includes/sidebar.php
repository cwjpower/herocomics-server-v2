<?php
// 공통 사이드바
// 모든 페이지에서 사용
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
</style>

<div class="sidebar">
        <div class="logo">
            <i class="fas fa-book-open"></i> HeroComics
        </div>
        
        <a href="dashboard.php" class="menu-item active">
            <i class="fas fa-chart-line"></i>
            <span>대시보드</span>
        </a>
        
        <a href="books/list.php" class="menu-item">
            <i class="fas fa-book"></i>
            <span>책 관리</span>
        </a>
        
        <a href="books/book_upload.php" class="menu-item">
            <i class="fas fa-plus-circle"></i>
            <span>책 추가</span>
        </a>
        
        <a href="genres/" class="menu-item">
            <i class="fas fa-tags"></i>
            <span>장르 관리</span>
        </a>
        
        <a href="orders/list.php" class="menu-item">
            <i class="fas fa-shopping-cart"></i>
            <span>주문 관리</span>
        </a>
        
        <a href="#" class="menu-item">
            <i class="fas fa-dollar-sign"></i>
            <span>매출/정산</span>
        </a>
        
        <a href="settings/profile.php" class="menu-item">
            <i class="fas fa-cog"></i>
            <span>설정</span>
        </a>
    </div>
    
    <!-- 메인 컨텐츠 -->
    <div class="main-content">
        <h1 class="mb-4">📊 대시보드</h1>
        
        <!-- 통계 카드 -->
        <div class="row" id="statsCards">
            <div class="col-md-3 mb-4">
                <div class="stats-card">
                    <div class="icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="label">총 책 권수</div>
                    <div class="value" id="totalBooks">-</div>
                    <div class="change positive" id="booksChange">
                        <i class="fas fa-arrow-up"></i> 로딩 중...
                    </div>
                </div>
