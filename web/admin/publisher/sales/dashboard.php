<?php
require_once '../../wps-config.php';
require_once '../../wps-settings.php';

if (!isset($_SESSION['publisher_id'])) {
    $_SESSION['publisher_id'] = 1;
}
$publisher_id = $_SESSION['publisher_id'];

// 1. 총 매출액
$query_total = "
    SELECT COALESCE(SUM(o.total_paid), 0) as total_sales
    FROM bt_order o
    INNER JOIN bt_order_item i ON o.order_id = i.order_id
    INNER JOIN bt_books b ON i.book_id = b.ID
    WHERE b.publisher_id = ?
";
$stmt = $wdb->prepare($query_total);
$stmt->bind_param('i', $publisher_id);
$stmt->execute();
$total_sales = $wdb->get_row($stmt)['total_sales'];

// 2. 이번 달 매출
$query_month = "
    SELECT COALESCE(SUM(o.total_paid), 0) as month_sales
    FROM bt_order o
    INNER JOIN bt_order_item i ON o.order_id = i.order_id
    INNER JOIN bt_books b ON i.book_id = b.ID
    WHERE b.publisher_id = ?
    AND YEAR(o.created_dt) = YEAR(CURDATE())
    AND MONTH(o.created_dt) = MONTH(CURDATE())
";
$stmt = $wdb->prepare($query_month);
$stmt->bind_param('i', $publisher_id);
$stmt->execute();
$month_sales = $wdb->get_row($stmt)['month_sales'];

// 3. 수수료율 (30% 고정, 나중에 DB에서)
$commission_rate = 30;
$settlement_amount = $month_sales * (100 - $commission_rate) / 100;

// 4. 책별 매출 순위
$query_books = "
    SELECT 
        i.book_id,
        i.book_title,
        COUNT(*) as sold_count,
        SUM(i.sale_price) as total_sales
    FROM bt_order_item i
    INNER JOIN bt_books b ON i.book_id = b.ID
    WHERE b.publisher_id = ?
    GROUP BY i.book_id, i.book_title
    ORDER BY sold_count DESC
    LIMIT 10
";
$stmt = $wdb->prepare($query_books);
$stmt->bind_param('i', $publisher_id);
$stmt->execute();
$book_sales = $wdb->get_results($stmt);
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>매출/정산 - HeroComics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<?php include "../includes/sidebar.php"; ?>

<div class="main-content" style="margin-left: 270px; padding: 20px;">
    <h1>💰 매출/정산</h1>
    
    <!-- 통계 카드 -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted">총 매출액</h6>
                    <h3 class="text-primary">₩<?php echo number_format($total_sales); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted">이번 달 매출</h6>
                    <h3 class="text-success">₩<?php echo number_format($month_sales); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted">정산 예정액</h6>
                    <h3 class="text-info">₩<?php echo number_format($settlement_amount); ?></h3>
                    <small class="text-muted">수수료 <?php echo $commission_rate; ?>% 제외</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted">수수료율</h6>
                    <h3 class="text-warning"><?php echo $commission_rate; ?>%</h3>
                </div>
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
    
    <!-- 책별 매출 순위 -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">📚 책별 매출 순위</h5>
        </div>
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>순위</th>
                        <th>책 제목</th>
                        <th>판매 권수</th>
                        <th>매출액</th>
                        <th>비율</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($book_sales)): ?>
                        <?php foreach ($book_sales as $index => $book): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($book['book_title']); ?></td>
                            <td><?php echo number_format($book['sold_count']); ?>권</td>
                            <td>₩<?php echo number_format($book['total_sales']); ?></td>
                            <td><?php echo round($book['total_sales'] / $total_sales * 100, 1); ?>%</td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-5">매출 데이터가 없습니다.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// 매출 추이 그래프 (dashboard_api.php에서 데이터 가져오기)
fetch('../dashboard_api.php')
    .then(res => res.json())
    .then(data => {
        const ctx = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.salesTrend.labels,
                datasets: [{
                    label: '일별 매출 (원)',
                    data: data.salesTrend.values,
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
    });
</script>
</body>
</html>
