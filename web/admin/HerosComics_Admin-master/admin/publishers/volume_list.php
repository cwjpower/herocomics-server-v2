<?php
require_once '../../wps-config.php';
require_once '../admin-header.php';

$mysqli = new mysqli('herocomics-mariadb', 'root', 'rootpass', 'herocomics');

$series_id = $_GET['series_id'] ?? 0;

// 시리즈 정보 조회
$series_query = "SELECT s.*, p.publisher_name 
                 FROM bt_series s 
                 JOIN bt_publishers p ON s.publisher_id = p.publisher_id 
                 WHERE s.series_id = $series_id";
$series_result = $mysqli->query($series_query);
$series = $series_result->fetch_assoc();

// 해당 시리즈의 권 목록 조회
$query = "SELECT * FROM bt_volumes WHERE series_id = $series_id ORDER BY volume_number";
$result = $mysqli->query($query);
?>

<style>
.content-wrapper { background: #f4f4f4; padding: 20px; }
.box { background: white; border-top: 3px solid #3c8dbc; }
</style>

<div class="content-wrapper">
    <section class="content-header">
        <h1><?php echo $series['series_name']; ?> - 권 목록</h1>
        <p>출판사: <?php echo $series['publisher_name']; ?></p>
        <a href="publisher_contents.php?id=<?php echo $series['publisher_id']; ?>" class="btn btn-default btn-sm">← 시리즈 목록으로</a>
    </section>
    
    <section class="content">
        <div class="box">
            <div class="box-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>권 번호</th>
                            <th>제목</th>
                            <th>가격</th>
                            <th>페이지 수</th>
                            <th>등록일</th>
                            <th>관리</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($result && $result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['volume_number']; ?>권</td>
                                <td><strong><?php echo $row['volume_title']; ?></strong></td>
                                <td>₩<?php echo number_format($row['price']); ?></td>
                                <td><?php echo $row['total_pages']; ?>페이지</td>
                                <td><?php echo $row['created_at']; ?></td>
                                <td>
                                    <button class="btn btn-xs btn-info">상세</button>
                                    <button class="btn btn-xs btn-warning">수정</button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">등록된 권이 없습니다.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>

<?php require_once '../admin-footer.php'; ?>
