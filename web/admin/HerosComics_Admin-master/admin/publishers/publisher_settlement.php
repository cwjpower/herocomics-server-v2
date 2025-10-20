<?php
require_once '../../wps-config.php';
require_once '../admin-header.php';

$mysqli = new mysqli('herocomics-mariadb', 'root', 'rootpass', 'herocomics');

// 월별 정산 데이터 조회
$query = "SELECT 
    p.publisher_name,
    p.commission_rate,
    COUNT(DISTINCT s.series_id) as total_series,
    COUNT(DISTINCT v.volume_id) as total_volumes,
    COALESCE(SUM(v.price * 100), 0) as total_sales,
    COALESCE(SUM(v.price * 100 * (1 - p.commission_rate/100)), 0) as publisher_revenue,
    COALESCE(SUM(v.price * 100 * (p.commission_rate/100)), 0) as platform_fee
FROM bt_publishers p
LEFT JOIN bt_series s ON p.publisher_id = s.publisher_id
LEFT JOIN bt_volumes v ON s.series_id = v.series_id
GROUP BY p.publisher_id
ORDER BY total_sales DESC";

$result = $mysqli->query($query);
?>

<style>
.content-wrapper { background: #f4f4f4; padding: 20px; }
.box { background: white; border-top: 3px solid #3c8dbc; }
.info-box { background: white; border-radius: 2px; padding: 10px; margin-bottom: 20px; }
.info-box-number { font-size: 30px; font-weight: bold; }
</style>

<div class="content-wrapper">
    <section class="content-header">
        <h1>출판사 정산 관리</h1>
    </section>
    
    <section class="content">
        <!-- 요약 정보 -->
        <div class="row">
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-text">이번달 총 매출</span>
                    <span class="info-box-number">₩0</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-text">플랫폼 수수료</span>
                    <span class="info-box-number">₩0</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-text">정산 예정액</span>
                    <span class="info-box-number">₩0</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-text">정산 완료</span>
                    <span class="info-box-number">₩0</span>
                </div>
            </div>
        </div>
        
        <!-- 출판사별 정산 내역 -->
        <div class="box">
            <div class="box-header">
                <h3>출판사별 정산 현황</h3>
            </div>
            <div class="box-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>출판사명</th>
                            <th>시리즈</th>
                            <th>총 권수</th>
                            <th>수수료율</th>
                            <th>총 매출</th>
                            <th>플랫폼 수수료</th>
                            <th>정산액</th>
                            <th>관리</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['publisher_name']; ?></td>
                            <td><?php echo $row['total_series']; ?>개</td>
                            <td><?php echo $row['total_volumes']; ?>권</td>
                            <td><?php echo $row['commission_rate']; ?>%</td>
                            <td>₩<?php echo number_format($row['total_sales']); ?></td>
                            <td>₩<?php echo number_format($row['platform_fee']); ?></td>
                            <td>₩<?php echo number_format($row['publisher_revenue']); ?></td>
                            <td>
                                <button class="btn btn-xs btn-success">정산처리</button>
                                <button class="btn btn-xs btn-info">상세보기</button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>

<?php require_once '../admin-footer.php'; ?>
