<?php
require_once '../../wps-config.php';
require_once '../admin-header.php';

$mysqli = new mysqli('herocomics-mariadb', 'root', 'rootpass', 'herocomics');

$publisher_id = $_GET['id'] ?? 0;

// 출판사 정보 조회
$pub_query = "SELECT * FROM bt_publishers WHERE publisher_id = $publisher_id";
$pub_result = $mysqli->query($pub_query);
$publisher = $pub_result->fetch_assoc();

// 해당 출판사의 시리즈와 권 조회
$query = "SELECT 
    s.series_id,
    s.series_name,
    s.author,
    s.status,
    COUNT(v.volume_id) as volume_count,
    s.created_at
FROM bt_series s
LEFT JOIN bt_volumes v ON s.series_id = v.series_id
WHERE s.publisher_id = $publisher_id
GROUP BY s.series_id
ORDER BY s.series_name";

$result = $mysqli->query($query);
?>

<style>
.content-wrapper { background: #f4f4f4; padding: 20px; }
.box { background: white; border-top: 3px solid #3c8dbc; }
</style>

<div class="content-wrapper">
    <section class="content-header">
        <h1><?php echo $publisher['publisher_name']; ?> - 콘텐츠 목록</h1>
        <a href="publisher_list.php" class="btn btn-default btn-sm">← 목록으로</a>
    </section>
    
    <section class="content">
        <div class="box">
            <div class="box-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>시리즈 ID</th>
                            <th>시리즈명</th>
                            <th>작가</th>
                            <th>권수</th>
                            <th>상태</th>
                            <th>등록일</th>
                            <th>관리</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($result && $result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['series_id']; ?></td>
                                <td><strong><?php echo $row['series_name']; ?></strong></td>
                                <td><?php echo $row['author']; ?></td>
                                <td><?php echo $row['volume_count']; ?>권</td>
                                <td><?php echo $row['status']; ?></td>
                                <td><?php echo $row['created_at']; ?></td>
                                <td>
                                    <button onclick="location.href='volume_list.php?series_id=<?php echo $row['series_id']; ?>'" class="btn btn-xs btn-info">권 목록</button>
                                    <button class="btn btn-xs btn-warning">수정</button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">등록된 시리즈가 없습니다.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>

<?php require_once '../admin-footer.php'; ?>
