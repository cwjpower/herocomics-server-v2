<?php
require_once '../../wps-config.php';
session_start();

if($_SESSION['user_level'] != '7') {
    die('<script>alert("출판사 관리자만 접근 가능합니다.");location.href="../login.php";</script>');
}

$mysqli = new mysqli('herocomics-mariadb', 'root', 'rootpass', 'herocomics');
$publisher_id = $_SESSION['publisher_id'];

// 시리즈 목록 조회
$query = "SELECT s.*, COUNT(v.volume_id) as volume_count
          FROM bt_series s
          LEFT JOIN bt_volumes v ON s.series_id = v.series_id
          WHERE s.publisher_id = $publisher_id
          GROUP BY s.series_id
          ORDER BY s.created_at DESC";
$result = $mysqli->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>시리즈 관리</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1>시리즈 관리</h1>
        <a href="publisher_dashboard.php" class="btn btn-default">← 대시보드</a>
        <a href="publisher_upload.php" class="btn btn-primary">새 시리즈 등록</a>
        <hr>
        
        <table class="table table-bordered">
            <thead>
                <tr>
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
                        <td><?php echo $row['series_name']; ?></td>
                        <td><?php echo $row['author']; ?></td>
                        <td><?php echo $row['volume_count']; ?>권</td>
                        <td><?php echo $row['status']; ?></td>
                        <td><?php echo $row['created_at']; ?></td>
                        <td>
                            <button class="btn btn-xs btn-info">권 관리</button>
                            <button class="btn btn-xs btn-warning">수정</button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">등록된 시리즈가 없습니다.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
