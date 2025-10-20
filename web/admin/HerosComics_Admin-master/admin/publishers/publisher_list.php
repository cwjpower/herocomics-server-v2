<?php
require_once '../../wps-config.php';
require_once '../admin-header.php';

$mysqli = new mysqli('herocomics-mariadb', 'root', 'rootpass', 'herocomics');
$query = "SELECT p.*, 
          (SELECT COUNT(*) FROM bt_series WHERE publisher_id = p.publisher_id) as series_count,
          (SELECT COUNT(*) FROM bt_volumes v JOIN bt_series s ON v.series_id = s.series_id WHERE s.publisher_id = p.publisher_id) as volume_count
          FROM bt_publishers p 
          ORDER BY p.publisher_id ASC";
$result = $mysqli->query($query);
?>

<style>
.content-wrapper {
    background: #f4f4f4;
    min-height: 600px;
    padding: 20px;
}
.box {
    background: white;
    border-top: 3px solid #3c8dbc;
    margin-bottom: 20px;
    box-shadow: 0 1px 1px rgba(0,0,0,0.1);
}
</style>

<div class="content-wrapper">
    <section class="content-header">
        <h1>출판사 관리</h1>
    </section>
    
    <section class="content">
        <div class="box">
            <div class="box-header">
                <button onclick="location.href='publisher_add.php'" class="btn btn-primary">새 출판사 추가</button>
                <button onclick="location.href='publisher_accounts.php'" class="btn btn-info">계정 관리</button>
                <button onclick="location.href='publisher_settlement.php'" class="btn btn-warning">정산 관리</button>
                <button onclick="location.href='publisher_stats.php'" class="btn btn-success">통계</button>
            </div>
            
            <div class="box-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>출판사명</th>
                            <th>코드</th>
                            <th>시리즈/권수</th>
                            <th>이메일</th>
                            <th>수수료율</th>
                            <th>상태</th>
                            <th>관리</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['publisher_id']; ?></td>
                            <td>
                                <a href="publisher_contents.php?id=<?php echo $row['publisher_id']; ?>" style="color: #337ab7; font-weight: bold;">
                                    <?php echo $row['publisher_name']; ?>
                                </a>
                            </td>
                            <td><?php echo $row['publisher_code']; ?></td>
                            <td><?php echo $row['series_count']; ?>개 / <?php echo $row['volume_count']; ?>권</td>
                            <td><?php echo $row['contact_email']; ?></td>
                            <td><?php echo $row['commission_rate']; ?>%</td>
                            <td><?php echo $row['status']; ?></td>
                            <td>
                                <button onclick="location.href='publisher_edit.php?id=<?php echo $row['publisher_id']; ?>'" class="btn btn-xs btn-info">수정</button>
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
