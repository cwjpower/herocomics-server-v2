<?php
require_once '../wps-config.php';
require_once '../wps-settings.php';

if (!isset($_SESSION['publisher_id'])) {
    $_SESSION['publisher_id'] = 1;
}
$publisher_id = $_SESSION['publisher_id'];

// 현재 디렉토리 설정 (sidebar용)
$current_dir = 'dashboard';
$base_path = './';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>대시보드 - HeroComics 출판사</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Noto Sans KR', sans-serif;
        }
        
        .main-content {
            margin-left: 270px;
            padding: 30px;
        }
        
        /* 통계 카드 */
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            transition: all 0.3s;
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
        }
        
        /* 베스트셀러 카드 */
        .bestseller-card {
            background: white;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 15px;
            transition: all 0.3s;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .bestseller-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .bestseller-card .rank {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 18px;
        }
        
        .bestseller-card .book-cover {
            width: 60px;
            height: 90px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .bestseller-card .book-info {
            flex: 1;
        }
        
        .bestseller-card .book-title {
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .bestseller-card .sales-count {
            font-size: 13px;
            color: #6c757d;
        }
        
        /* 시리즈 그룹 카드 */
        .series-group-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 15px;
            transition: all 0.3s;
            cursor: pointer;
        }
        
        .series-group-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }
        
        .series-header h5 {
            margin: 0;
            font-size: 18px;
        }
        
        /* 빠른 액션 버튼 */
        .quick-action-btn {
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
            margin-bottom: 15px;
        }
        
        .quick-action-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            color: #667eea;
            text-decoration: none;
        }
        
        .quick-action-btn i {
            font-size: 32px;
            margin-bottom: 10px;
            color: #667eea;
        }
        
        .quick-action-btn .label {
            font-weight: 500;
        }
    </style>
</head>
<body>
<?php include "includes/sidebar.php"; ?>

<div class="main-content">
    <h1 class="mb-4">📊 대시보드</h1>
    
    <!-- 통계 카드 -->
    <div class="row" id="statsCards">
        <div class="col-md-3">
            <div class="stats-card">
                <div class="icon" style="background: #e3f2fd; color: #2196F3;">
                    <i class="fas fa-book"></i>
                </div>
                <div class="label">총 책 권수</div>
                <div class="value" id="totalBooks">-</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="icon" style="background: #e8f5e9; color: #4CAF50;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="label">판매 중인 책</div>
                <div class="value" id="activeBooks">-</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="icon" style="background: #fff3e0; color: #FF9800;">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="label">오늘 주문</div>
                <div class="value" id="todayOrders">-</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card">
                <div class="icon" style="background: #f3e5f5; color: #9C27B0;">
                    <i class="fas fa-won-sign"></i>
                </div>
                <div class="label">이번 달 매출</div>
                <div class="value" id="monthSales">-</div>
            </div>
        </div>
    </div>
    
    <!-- 매출 추이 그래프 -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">📈 최근 7일 매출 추이</h5>
        </div>
        <div class="card-body">
            <canvas id="salesChart" height="80"></canvas>
        </div>
    </div>
    
    <div class="row">
        <!-- 최근 주문 -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">🛒 최근 주문</h5>
                </div>
                <div class="card-body" id="recentOrdersList">
                    <p class="text-center text-muted">로딩 중...</p>
                </div>
            </div>
        </div>
        
        <!-- 베스트셀러 -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">🏆 이번 주 베스트셀러</h5>
                </div>
                <div class="card-body" id="bestsellersList">
                    <p class="text-center text-muted">로딩 중...</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- 시리즈 현황 + 빠른 액션 -->
    <div class="row">
        <!-- 시리즈 현황 -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">📚 출판사별 시리즈 현황</h5>
                </div>
                <div class="card-body" id="seriesGroupsList">
                    <p class="text-center text-muted">로딩 중...</p>
                </div>
            </div>
        </div>
        
        <!-- 빠른 액션 -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">⚡ 빠른 액션</h5>
                </div>
                <div class="card-body">
                    <a href="<?php echo $base_path; ?>books/book_upload.php" class="quick-action-btn">
                        <i class="fas fa-plus-circle"></i>
                        <div class="label">새 책 등록</div>
                    </a>
                    
                    <a href="<?php echo $base_path; ?>books/list.php" class="quick-action-btn">
                        <i class="fas fa-list"></i>
                        <div class="label">책 목록 관리</div>
                    </a>
                    
                    <a href="<?php echo $base_path; ?>orders/list.php" class="quick-action-btn">
                        <i class="fas fa-shopping-cart"></i>
                        <div class="label">주문 관리</div>
                    </a>
                    
                    <a href="<?php echo $base_path; ?>sales/dashboard.php" class="quick-action-btn">
                        <i class="fas fa-won-sign"></i>
                        <div class="label">매출/정산</div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
            // 통계 업데이트
            document.getElementById('totalBooks').textContent = data.stats.totalBooks;
            document.getElementById('activeBooks').textContent = data.stats.activeBooks;
            document.getElementById('todayOrders').textContent = data.stats.todayOrders + '건';
            document.getElementById('monthSales').textContent = '₩' + data.stats.monthSales.toLocaleString();
            
            // 매출 추이 그래프
            drawSalesChart(data.salesTrend);
            
            // 최근 주문
            displayRecentOrders(data.recentOrders);
            
            // 베스트셀러
            displayBestsellers(data.bestsellers);
            
            // 시리즈 그룹
            displaySeriesGroups(data.seriesGroups);
        }
    } catch (error) {
        console.error('데이터 로드 실패:', error);
    }
}

// 매출 추이 그래프
function drawSalesChart(salesTrend) {
    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: salesTrend.labels,
            datasets: [{
                label: '일별 매출 (원)',
                data: salesTrend.values,
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₩' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
}

// 최근 주문 표시
function displayRecentOrders(orders) {
    const container = document.getElementById('recentOrdersList');
    
    if (!orders || orders.length === 0) {
        container.innerHTML = '<p class="text-center text-muted py-4">최근 주문이 없습니다</p>';
        return;
    }
    
    let html = '<div class="list-group">';
    orders.forEach(order => {
        const statusClass = order.status === 'paid' ? 'success' : 'secondary';
        html += `
            <div class="list-group-item">
                <div class="d-flex justify-content-between">
                    <strong>${order.book_title}</strong>
                    <span class="badge bg-${statusClass}">${getStatusText(order.status)}</span>
                </div>
                <small class="text-muted">주문번호: ${order.order_id} | ${order.order_date}</small>
            </div>
        `;
    });
    html += '</div>';
    container.innerHTML = html;
}

// 베스트셀러 표시
function displayBestsellers(books) {
    const container = document.getElementById('bestsellersList');
    
    if (!books || books.length === 0) {
        container.innerHTML = '<p class="text-center text-muted py-4">판매 데이터가 없습니다</p>';
        return;
    }
    
    let html = '';
    books.forEach((book, index) => {
        const coverImage = (book.cover_image && book.cover_image !== 'null') ? book.cover_image : 'https://via.placeholder.com/60x90?text=No+Image';
        html += `
            <div class="bestseller-card">
                <div class="rank">${index + 1}</div>
                <img src="${coverImage}" 
                     alt="${book.book_title || ''}" 
                     class="book-cover"
                     onerror="this.src='https://via.placeholder.com/60x90?text=No+Image'">
                <div class="book-info">
                    <div class="book-title">${book.book_title || '제목 없음'}</div>
                    <div class="sales-count">판매량: ${book.sales_count || 0}권</div>
                </div>
            </div>
        `;
    });
    container.innerHTML = html;
}

// 시리즈 그룹 표시
function displaySeriesGroups(seriesGroups) {
    const container = document.getElementById('seriesGroupsList');
    
    if (!seriesGroups || seriesGroups.length === 0) {
        container.innerHTML = '<p class="text-center text-muted py-4">등록된 시리즈가 없습니다</p>';
        return;
    }
    
    let html = '';
    seriesGroups.forEach(group => {
        html += `
            <div class="series-group-card" onclick="location.href='series/detail.php?series_name=' + encodeURIComponent('${group.series_name}')">
                <div class="series-header">
                    <h5>📚 ${group.series_name || '시리즈'}</h5>
                    <span class="badge bg-light text-dark">${group.volume_count || 0}권</span>
                </div>
            </div>
        `;
    });
    container.innerHTML = html;
}

// 주문 상태 텍스트
function getStatusText(status) {
    const statusMap = {
        'pending': '대기 중',
        'paid': '결제 완료',
        'cancelled': '취소',
        'refunded': '환불'
    };
    return statusMap[status] || '알 수 없음';
}
</script>
</body>
</html>
