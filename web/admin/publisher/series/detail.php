<?php
require_once '../../wps-config.php';
require_once '../../wps-settings.php';

if (!isset($_SESSION['publisher_id'])) {
    $_SESSION['publisher_id'] = 1;
}
$publisher_id = $_SESSION['publisher_id'];

// 시리즈명 받기
$series_name = isset($_GET['series_name']) ? trim($_GET['series_name']) : '';

if (empty($series_name)) {
    header('Location: ../dashboard.php');
    exit;
}

// 1. 시리즈에 속한 책 목록 + 매출 정보
$query = "
    SELECT 
        b.ID,
        b.book_title,
        b.series_volume,
        b.cover_img,
        b.sale_price,
        COUNT(DISTINCT i.order_id) as sold_count,
        COALESCE(SUM(i.sale_price), 0) as total_sales
    FROM bt_books b
    LEFT JOIN bt_order_item i ON b.ID = i.book_id
    WHERE b.series_name = ? AND b.publisher_id = ?
    GROUP BY b.ID, b.book_title, b.series_volume, b.cover_img, b.sale_price
    ORDER BY b.series_volume ASC
";

$stmt = $wdb->prepare($query);
$stmt->bind_param('si', $series_name, $publisher_id);
$stmt->execute();
$books = $wdb->get_results($stmt);

// 2. 시리즈 전체 매출 집계
$total_series_sales = 0;
$total_sold_count = 0;
foreach ($books as $book) {
    $total_series_sales += $book['total_sales'];
    $total_sold_count += $book['sold_count'];
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($series_name); ?> - HeroComics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<?php include "../includes/sidebar.php"; ?>

<div class="main-content" style="margin-left: 270px; padding: 20px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>📚 <?php echo htmlspecialchars($series_name); ?></h1>
        <a href="../dashboard.php" class="btn btn-outline-secondary">← 대시보드</a>
    </div>
    
    <!-- 시리즈 매출 통계 -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted">총 권수</h6>
                    <h3 class="text-primary"><?php echo count($books); ?>권</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted">총 판매량</h6>
                    <h3 class="text-success"><?php echo number_format($total_sold_count); ?>권</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted">시리즈 총 매출</h6>
                    <h3 class="text-info">₩<?php echo number_format($total_series_sales); ?></h3>
                </div>
            </div>
        </div>
    </div>
    
    <!-- 권별 매출 그래프 -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">📊 권별 매출 현황</h5>
        </div>
        <div class="card-body">
            <canvas id="volumeSalesChart" height="80"></canvas>
        </div>
    </div>
    
    <!-- 책 목록 -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">📖 시리즈 책 목록</h5>
        </div>
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>권수</th>
                        <th>표지</th>
                        <th>제목</th>
                        <th>판매가</th>
                        <th>판매량</th>
                        <th>매출액</th>
                        <th>비율</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($books)): ?>
                        <?php foreach ($books as $book): ?>
                        <tr>
                            <td><?php echo $book['series_volume'] ? $book['series_volume'] . '권' : '-'; ?></td>
                            <td>
                                <img src="<?php echo !empty($book['cover_img']) ? htmlspecialchars($book['cover_img']) : 'https://via.placeholder.com/50x75?text=No+Image'; ?>" 
                                     alt="표지" 
                                     style="width: 50px; height: 75px; object-fit: cover;"
                                     onerror="this.src='https://via.placeholder.com/50x75?text=No+Image'">
                            </td>
                            <td><?php echo htmlspecialchars($book['book_title']); ?></td>
                            <td>₩<?php echo number_format($book['sale_price']); ?></td>
                            <td><?php echo number_format($book['sold_count']); ?>권</td>
                            <td>₩<?php echo number_format($book['total_sales']); ?></td>
                            <td>
                                <?php 
                                $percentage = $total_series_sales > 0 ? round($book['total_sales'] / $total_series_sales * 100, 1) : 0;
                                ?>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar" role="progressbar" 
                                         style="width: <?php echo $percentage; ?>%" 
                                         aria-valuenow="<?php echo $percentage; ?>" 
                                         aria-valuemin="0" aria-valuemax="100">
                                        <?php echo $percentage; ?>%
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-5">책 정보가 없습니다.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// 권별 매출 그래프
const ctx = document.getElementById('volumeSalesChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: [<?php foreach ($books as $book): ?>'<?php echo $book['series_volume'] ? $book['series_volume'] . '권' : $book['book_title']; ?>',<?php endforeach; ?>],
        datasets: [{
            label: '매출액 (원)',
            data: [<?php foreach ($books as $book): ?><?php echo $book['total_sales']; ?>,<?php endforeach; ?>],
            backgroundColor: 'rgba(54, 162, 235, 0.5)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
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
</script>
</body>
</html>
