<?php
require_once '../../wps-config.php';
require_once '../admin-header.php';

$mysqli = new mysqli('herocomics-mariadb', 'root', 'rootpass', 'herocomics');

// 출판사별 통계
$query = "SELECT 
    p.publisher_name,
    COUNT(DISTINCT s.series_id) as series_count,
    COUNT(DISTINCT v.volume_id) as volume_count,
    MAX(s.created_at) as last_upload
FROM bt_publishers p
LEFT JOIN bt_series s ON p.publisher_id = s.publisher_id
LEFT JOIN bt_volumes v ON s.series_id = v.series_id
GROUP BY p.publisher_id";

$result = $mysqli->query($query);
?>

<style>
.content-wrapper { background: #f4f4f4; padding: 20px; }
.box { background: white; border-top: 3px solid #3c8dbc; }
</style>

<div class="content-wrapper">
    <section class="content-header">
        <h1>출판사 통계</h1>
    </section>
    
    <section class="content">
        <div class="box">
            <div class="box-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>출판사명</th>
                            <th>시리즈 수</th>
                            <th>총 권수</th>
                            <th>최근 업로드</th>
                            <th>상세통계</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['publisher_name']; ?></td>
                            <td><?php echo $row['series_count']; ?>개</td>
                            <td><?php echo $row['volume_count']; ?>권</td>
                            <td><?php echo $row['last_upload'] ?: '없음'; ?></td>
                            <td>
                                <button class="btn btn-xs btn-info">매출 통계</button>
                                <button class="btn btn-xs btn-warning">인기 시리즈</button>
                                <button class="btn btn-xs btn-success">다운로드</button>
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
