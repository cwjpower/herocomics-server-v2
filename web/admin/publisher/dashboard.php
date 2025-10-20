<?php
// 세션 시작 및 권한 체크 (나중에 로그인 시스템 만들면 활성화)
// session_start();
// if (!isset($_SESSION['publisher_id'])) {
//     header('Location: login.php');
//     exit;
// }

// 페이지 정보
$page_title = "대시보드";
$current_page = "dashboard";
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - HeroComics 출판사 CMS</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js - 그래프 라이브러리 -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <style>
        /* 사이드바와 메인 레이아웃 스타일 */
        body {
            font-family: 'Noto Sans KR', sans-serif;
            background-color: #f8f9fa;
        }
        
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
        
        .main-content {
            margin-left: 250px;
            padding: 30px;
        }
        
        /* 통계 카드 스타일 */
        .stats-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }
        
        .stats-card .icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: white;
            margin-bottom: 15px;
        }
        
        .stats-card .label {
            color: #6c757d;
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .stats-card .value {
            font-size: 32px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .stats-card .change {
            font-size: 13px;
            display: flex;
            align-items: center;
        }
        
        .stats-card .change.positive {
            color: #28a745;
        }
        
        .stats-card .change.negative {
            color: #dc3545;
        }
        
        /* 차트 컨테이너 */
        .chart-container {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-top: 30px;
        }
        
        .chart-container h3 {
            font-size: 18px;
            margin-bottom: 20px;
            color: #2c3e50;
        }
        
        /* 최근 주문 목록 */
        .recent-orders {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-top: 30px;
        }
        
        .recent-orders h3 {
            font-size: 18px;
            margin-bottom: 20px;
            color: #2c3e50;
        }
        
        .order-item {
            padding: 15px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .order-item:last-child {
            border-bottom: none;
        }
        
        .order-info {
            flex: 1;
        }
        
        .order-info .book-title {
            font-weight: 500;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .order-info .order-date {
            font-size: 13px;
            color: #6c757d;
        }
        
        .order-status {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-paid {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .status-shipping {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-completed {
            background: #d4edda;
            color: #155724;
        }
        
        /* 베스트셀러 카드 */
        .bestseller-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            transition: transform 0.3s;
        }
        
        .bestseller-card:hover {
            transform: translateX(5px);
        }
        
        .bestseller-card .rank {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 18px;
            margin-right: 15px;
        }
        
        .bestseller-card .book-cover {
            width: 60px;
            height: 80px;
            object-fit: cover;
            border-radius: 5px;
            margin-right: 15px;
        }
        
        .bestseller-card .book-info {
            flex: 1;
        }
        
        .bestseller-card .book-title {
            font-weight: 500;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .bestseller-card .sales-count {
            font-size: 13px;
            color: #6c757d;
        }
        
        /* 빠른 액션 버튼 */
        .quick-actions {
            margin-top: 30px;
        }
        
        .action-btn {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: block;
            color: #2c3e50;
        }
        
        .action-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            color: #667eea;
        }
        
        .action-btn i {
            font-size: 32px;
            margin-bottom: 10px;
            color: #667eea;
        }
        
        .action-btn .label {
            font-weight: 500;
        }
        
        /* 로딩 스피너 */
        .loading {
            text-align: center;
            padding: 50px;
            color: #6c757d;
        }
        
        .spinner-border {
            width: 3rem;
            height: 3rem;
        }
/* 시리즈 그룹 스타일 */
/* 가로 스크롤 스타일 */
.books-preview::-webkit-scrollbar {
    height: 8px;
}
.books-preview::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}
.books-preview::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}
.books-preview::-webkit-scrollbar-thumb:hover {
    background: #555;
}
.series-group-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    transition: all 0.3s;
}
.series-groups-container {
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.series-groups {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 20px;
}
.series-group-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 25px;
    border-radius: 12px;
    color: white;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}
.series-group-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5);
}
.publisher-name {
    font-size: 20px;
    font-weight: bold;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.series-stats {
    display: flex;
    justify-content: space-around;
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid rgba(255,255,255,0.3);
}
.stat-item {
    text-align: center;
}
.stat-value {
    font-size: 24px;
    font-weight: bold;
    display: block;
}
.stat-label {
    font-size: 12px;
    opacity: 0.9;
    display: block;
    margin-top: 5px;
}
    </style>
</head>
<body>
<!-- 사이드바 -->
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
        
        <a href="#" class="menu-item">
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
            </div>
            
            <div class="col-md-3 mb-4">
                <div class="stats-card">
                    <div class="icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <div class="label">판매 중인 책</div>
                    <div class="value" id="activeBooks">-</div>
                    <div class="change positive" id="activeBooksChange">
                        <i class="fas fa-arrow-up"></i> 로딩 중...
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-4">
                <div class="stats-card">
                    <div class="icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <div class="label">오늘 주문</div>
                    <div class="value" id="todayOrders">-</div>
                    <div class="change positive" id="ordersChange">
                        <i class="fas fa-arrow-up"></i> 로딩 중...
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-4">
                <div class="stats-card">
                    <div class="icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="label">이번 달 매출</div>
                    <div class="value" id="monthSales">-</div>
                    <div class="change positive" id="salesChange">
                        <i class="fas fa-arrow-up"></i> 로딩 중...
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 매출 추이 그래프와 최근 주문 -->
        <div class="row">
            <div class="col-md-8">
                <div class="chart-container">
                    <h3>📈 최근 7일 매출 추이</h3>
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="recent-orders">
                    <h3>🛒 최근 주문</h3>
                    <div id="recentOrdersList">
                        <div class="loading">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">로딩 중...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 베스트셀러와 빠른 액션 -->

        <!-- 시리즈 그룹 현황 -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="series-groups-container">
                    <h3>📚 출판사별 시리즈 현황</h3>
                    <div id="seriesGroupsList" class="series-groups">
                        <div class="loading">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">로딩 중...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-8">
                <div class="chart-container">
                    <h3>🏆 이번 주 베스트셀러</h3>
                    <div id="bestsellersList">
                        <div class="loading">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">로딩 중...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <h3 class="mb-3">⚡ 빠른 액션</h3>
                <div class="quick-actions">
                    <a href="books/book_upload.php" class="action-btn mb-3">
                        <i class="fas fa-plus-circle"></i>
                        <div class="label">새 책 등록</div>
                    </a>
                    
                    <a href="books/list.php" class="action-btn mb-3">
                        <i class="fas fa-list"></i>
                        <div class="label">책 목록 관리</div>
                    </a>
                    
                    <a href="#" class="action-btn mb-3">
                        <i class="fas fa-chart-bar"></i>
                        <div class="label">상세 통계 보기</div>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- 대시보드 로직 -->
    <script>
        // 페이지 로드 시 데이터 가져오기
        document.addEventListener('DOMContentLoaded', function() {
            loadDashboardData();
        });
        
        // 대시보드 데이터 로드
        async function loadDashboardData() {
            try {
    // 시리즈 그룹 데이터 로드

    // 시리즈 그룹 데이터 로드
    fetch("dashboard_api.php")
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById("seriesGroupsList");
            if (data.success && data.seriesGroups && data.seriesGroups.length > 0) {
                container.innerHTML = data.seriesGroups.map(group => `
                    <div class="series-group-card" onclick="location.href='books/list.php?series=${encodeURIComponent(group.series_name)}'" style="cursor: pointer;">
                        <div class="series-header">
                            <h4>📚 ${group.series_name}</h4>
                            <span class="badge bg-primary">${group.book_count}권</span>
                        </div>
                        <div class="books-preview" style="overflow-x: auto; white-space: nowrap; padding: 10px 0; max-width: 400px;">
                            ${group.books.map(book => `
                                <img src="${book.cover_image}" 
                                     alt="${book.book_title}" 
                                     title="${book.title}"
                                     onerror="this.src='/admin/img/no-image.jpg'"
                                     style="width: 60px; height: 90px; object-fit: cover; border-radius: 8px; margin-right: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); display: inline-block;">
                            `).join('')}
                        </div>
                    </div>
                `).join("");
            } else {
                container.innerHTML = "<p class='text-center text-muted'>등록된 시리즈가 없습니다.</p>";
            }
        })
        .catch(error => {
            console.error("시리즈 그룹 로드 실패:", error);
            document.getElementById("seriesGroupsList").innerHTML = "<p class='text-center text-danger'>데이터를 불러올 수 없습니다.</p>";
        });

                const response = await fetch('dashboard_api.php');
                const data = await response.json();
                
                if (data.success) {
                    updateStatsCards(data.stats);
                    drawSalesChart(data.salesTrend);
                    displayRecentOrders(data.recentOrders);
                    displayBestsellers(data.bestsellers);
                } else {
                    console.error('데이터 로드 실패:', data.message);
                }
            } catch (error) {
                console.error('데이터 로드 에러:', error);
            }
        }
        
        // 통계 카드 업데이트
        function updateStatsCards(stats) {
            // 총 책 권수
            document.getElementById('totalBooks').textContent = stats.totalBooks;
            updateChange('booksChange', stats.booksChange);
            
            // 판매 중인 책
            document.getElementById('activeBooks').textContent = stats.activeBooks;
            updateChange('activeBooksChange', stats.activeBooksChange);
            
            // 오늘 주문
            document.getElementById('todayOrders').textContent = stats.todayOrders + '건';
            updateChange('ordersChange', stats.ordersChange);
            
            // 이번 달 매출
            document.getElementById('monthSales').textContent = formatCurrency(stats.monthSales);
            updateChange('salesChange', stats.salesChange);
        }
        
        // 변화율 업데이트
        function updateChange(elementId, value) {
            const element = document.getElementById(elementId);
            const isPositive = value >= 0;
            
            element.className = 'change ' + (isPositive ? 'positive' : 'negative');
            element.innerHTML = `
                <i class="fas fa-arrow-${isPositive ? 'up' : 'down'}"></i>
                ${isPositive ? '+' : ''}${value}% 어제 대비
            `;
        }
        
        // 매출 추이 그래프 그리기
        function drawSalesChart(salesData) {
            const ctx = document.getElementById('salesChart').getContext('2d');
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: salesData.labels,
                    datasets: [{
                        label: '일별 매출 (원)',
                        data: salesData.values,
                        borderColor: '#667eea',
                        backgroundColor: 'rgba(102, 126, 234, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        pointBackgroundColor: '#667eea'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return '매출: ' + formatCurrency(context.parsed.y);
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return formatCurrency(value);
                                }
                            }
                        }
                    }
                }
            });
        }
        
        // 최근 주문 목록 표시
        function displayRecentOrders(orders) {
            const container = document.getElementById('recentOrdersList');
            
            if (orders.length === 0) {
                container.innerHTML = '<div class="text-center text-muted py-4">최근 주문이 없습니다</div>';
                return;
            }
            
            let html = '';
            orders.forEach(order => {
                html += `
                    <div class="order-item">
                        <div class="order-info">
                            <div class="book-title">${order.book_title}</div>
                            <div class="order-date">${order.order_date}</div>
                        </div>
                        <span class="order-status ${getStatusClass(order.status)}">
                            ${getStatusText(order.status)}
                        </span>
                    </div>
                `;
            });
            
            container.innerHTML = html;
        }
        
        // 베스트셀러 목록 표시
        function displayBestsellers(books) {
            const container = document.getElementById('bestsellersList');
            
            if (books.length === 0) {
                container.innerHTML = '<div class="text-center text-muted py-4">판매 데이터가 없습니다</div>';
                return;
            }
            
            let html = '';
            books.forEach((book, index) => {
                html += `
                    <div class="bestseller-card">
                        <div class="rank">${index + 1}</div>
                        <img src="${book.cover_image || 'https://via.placeholder.com/150x200?text=No+Image'}" 
                             alt="${book.book_title}" 
                             class="book-cover">
                        <div class="book-info">
                            <div class="book-title">${book.book_title}</div>
                            <div class="sales-count">판매량: ${book.sales_count}권</div>
                        </div>
                    </div>
                `;
            });
            
            container.innerHTML = html;
        }
        
        // 주문 상태에 따른 CSS 클래스
        function getStatusClass(status) {
            const statusMap = {
                'pending': 'status-pending',
                'paid': 'status-paid',
                'shipping': 'status-shipping',
                'completed': 'status-completed'
            };
            return statusMap[status] || 'status-pending';
        }
        
        // 주문 상태 텍스트
        function getStatusText(status) {
            const statusMap = {
                'pending': '결제 대기',
                'paid': '결제 완료',
                'shipping': '배송 중',
                'completed': '배송 완료'
            };
            return statusMap[status] || '대기 중';
        }
        
        // 금액 포맷팅
        function formatCurrency(amount) {
            return new Intl.NumberFormat('ko-KR', {
                style: 'currency',
                currency: 'KRW'
            }).format(amount);
        }
    </script>
</body>
</html>
