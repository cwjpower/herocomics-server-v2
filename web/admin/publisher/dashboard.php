<?php
// 페이지 정보 설정
$page_title = "대시보드";
$current_page = "dashboard";

// 헤더 포함
require_once 'layout/modern_header.php';
?>

<!-- 대시보드 고유 컨텐츠 시작 -->
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
        <div class="chart-container">
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
        <div class="quick-actions" style="margin-top: 0;">
            <a href="books/book_upload.php" class="action-btn mb-3" style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); text-align: center; cursor: pointer; transition: all 0.3s; text-decoration: none; display: block; color: #2c3e50;">
                <i class="fas fa-plus-circle" style="font-size: 32px; margin-bottom: 10px; color: #667eea; display: block;"></i>
                <div class="label" style="font-weight: 500;">새 책 등록</div>
            </a>
            
            <a href="books/list.php" class="action-btn mb-3" style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); text-align: center; cursor: pointer; transition: all 0.3s; text-decoration: none; display: block; color: #2c3e50;">
                <i class="fas fa-list" style="font-size: 32px; margin-bottom: 10px; color: #667eea; display: block;"></i>
                <div class="label" style="font-weight: 500;">책 목록 관리</div>
            </a>
            
            <a href="#" class="action-btn mb-3" style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); text-align: center; cursor: pointer; transition: all 0.3s; text-decoration: none; display: block; color: #2c3e50;">
                <i class="fas fa-chart-bar" style="font-size: 32px; margin-bottom: 10px; color: #667eea; display: block;"></i>
                <div class="label" style="font-weight: 500;">상세 통계 보기</div>
            </a>
        </div>
    </div>
</div>

<!-- 대시보드 JavaScript -->
<script>
    // 페이지 로드 시 데이터 가져오기
    document.addEventListener('DOMContentLoaded', function() {
        loadDashboardData();
    });
    
    // 대시보드 데이터 로드
    async function loadDashboardData() {
        try {
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
        document.getElementById('totalBooks').textContent = stats.totalBooks;
        updateChange('booksChange', stats.booksChange);
        
        document.getElementById('activeBooks').textContent = stats.activeBooks;
        updateChange('activeBooksChange', stats.activeBooksChange);
        
        document.getElementById('todayOrders').textContent = stats.todayOrders + '건';
        updateChange('ordersChange', stats.ordersChange);
        
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
                    <img src="${book.cover_image || '../images/no-image.png'}" 
                         alt="${book.title}" 
                         class="book-cover">
                    <div class="book-info">
                        <div class="book-title">${book.title}</div>
                        <div class="sales-count">판매량: ${book.sales_count}권</div>
                    </div>
                </div>
            `;
        });
        
        container.innerHTML = html;
    }
    
    // 주문 상태 CSS 클래스
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

<!-- 베스트셀러 카드, 주문 아이템, 빠른 액션 버튼 스타일 -->
<style>
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
    
    .action-btn:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        color: #667eea;
    }
    
    .loading {
        text-align: center;
        padding: 50px;
        color: #6c757d;
    }
</style>

<?php
// 푸터 포함
require_once 'layout/modern_footer.php';
?>
