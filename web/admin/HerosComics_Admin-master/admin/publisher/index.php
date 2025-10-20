<?php
require_once '../publisher-header.php';

// 통계 조회
$stats_query = "SELECT 
    COUNT(DISTINCT s.series_id) as total_series,
    COUNT(DISTINCT v.volume_id) as total_volumes,
    SUM(v.price) as total_sales
FROM bt_series s
LEFT JOIN bt_volumes v ON s.series_id = v.series_id
WHERE s.publisher_id = $publisher_id";
$stats = $mysqli->query($stats_query)->fetch_assoc();
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1><?php echo $publisher['publisher_name']; ?> 대시보드</h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-aqua">
                    <div class="inner">
                        <h3><?php echo $stats['total_series'] ?: 0; ?></h3>
                        <p>시리즈</p>
                    </div>
                    <div class="icon"><i class="fa fa-book"></i></div>
                </div>
            </div>
            
            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-green">
                    <div class="inner">
                        <h3><?php echo $stats['total_volumes'] ?: 0; ?></h3>
                        <p>총 권수</p>
                    </div>
                    <div class="icon"><i class="fa fa-files-o"></i></div>
                </div>
            </div>
            
            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-yellow">
                    <div class="inner">
                        <h3>₩<?php echo number_format($stats['total_sales'] ?: 0); ?></h3>
                        <p>총 매출</p>
                    </div>
                    <div class="icon"><i class="fa fa-krw"></i></div>
                </div>
            </div>
            
            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-red">
                    <div class="inner">
                        <h3>0</h3>
                        <p>승인 대기</p>
                    </div>
                    <div class="icon"><i class="fa fa-clock-o"></i></div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once '../admin-footer.php'; ?>
