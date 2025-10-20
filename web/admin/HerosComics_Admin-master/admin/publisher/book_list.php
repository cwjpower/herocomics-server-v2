<?php
require_once '../../wps-config.php';

if (!isset($_SESSION['login']['userid']) || $_SESSION['login']['user_level'] != 7) {
    header('Location: ../login.php');
    exit;
}

$publisher_id = $_SESSION['login']['publisher_id'];

// DB 연결
global $wdb;

// 내 책 목록 가져오기
$query = "SELECT * FROM bt_books WHERE publisher_id = ? ORDER BY created_dt DESC";
$stmt = $wdb->prepare($query);
$stmt->bind_param("i", $publisher_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>내 책 목록 - Hero Comics</title>
    <link rel="stylesheet" href="../../css/bootstrap.min.css">
    <link rel="stylesheet" href="../../css/font-awesome.min.css">
    <style>
        body { 
            font-family: 'Noto Sans KR', sans-serif; 
            background: #f4f6f9;
            padding: 20px;
        }
        .container { 
            max-width: 1400px; 
            margin: 0 auto; 
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .top-bar { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #007bff;
        }
        .top-bar h1 {
            color: #333;
            margin: 0;
            font-size: 28px;
        }
        .top-bar .user-info {
            color: #666;
            font-size: 14px;
            margin-top: 5px;
        }
        .btn { 
            padding: 10px 20px; 
            text-decoration: none; 
            border-radius: 4px; 
            display: inline-block;
            margin-left: 10px;
            font-weight: bold;
            transition: all 0.3s;
        }
        .btn-primary { 
            background: #007bff; 
            color: white; 
        }
        .btn-primary:hover { 
            background: #0056b3; 
            color: white;
        }
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        .btn-danger:hover {
            background: #c82333;
            color: white;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
        }
        th, td { 
            padding: 15px; 
            text-align: left; 
            border-bottom: 1px solid #ddd; 
        }
        th { 
            background: #f8f9fa; 
            font-weight: bold;
            color: #333;
        }
        tbody tr:hover {
            background: #f8f9fa;
        }
        img { 
            max-width: 80px; 
            height: auto;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .no-image {
            width: 80px;
            height: 120px;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            font-size: 36px;
        }
        .status-active { 
            color: #28a745; 
            font-weight: bold; 
        }
        .status-pending { 
            color: #ffc107; 
            font-weight: bold;
        }
        .badge-free {
            background: #28a745;
            color: white;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 11px;
        }
        .empty { 
            text-align: center; 
            padding: 80px 20px; 
            color: #999; 
        }
        .empty h2 {
            font-size: 24px;
            margin-bottom: 15px;
        }
        .action-links a {
            color: #007bff;
            text-decoration: none;
            margin: 0 5px;
        }
        .action-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    
    <div class="container">
        <div class="top-bar">
            <div>
                <h1>📚 내 책 목록</h1>
                <div class="user-info">
                    출판사: <strong><?= htmlspecialchars($_SESSION['login']['display_name']) ?></strong>
                    (Publisher ID: <?= $publisher_id ?>)
                </div>
            </div>
            <div>
                <a href="book_upload.php" class="btn btn-primary">
                    <i class="fa fa-plus"></i> 새 책 업로드
                </a>
                <a href="../logout.php" class="btn btn-danger">
                    <i class="fa fa-sign-out"></i> 로그아웃
                </a>
            </div>
        </div>

        <?php if($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>표지</th>
                    <th>제목</th>
                    <th>저자</th>
                    <th>ISBN</th>
                    <th>정가</th>
                    <th>할인율</th>
                    <th>판매가</th>
                    <th>무료</th>
                    <th>상태</th>
                    <th>등록일</th>
                    <th>관리</th>
                </tr>
            </thead>
            <tbody>
                <?php while($book = $result->fetch_assoc()): ?>
                <tr>
                    <td>
                        <?php if($book['cover_img']): ?>
                            <img src="<?= htmlspecialchars($book['cover_img']) ?>" alt="표지">
                        <?php else: ?>
                            <div class="no-image">📖</div>
                        <?php endif; ?>
                    </td>
                    <td><strong><?= htmlspecialchars($book['book_title']) ?></strong></td>
                    <td><?= htmlspecialchars($book['author']) ?></td>
                    <td><?= htmlspecialchars($book['isbn']) ?: '-' ?></td>
                    <td><?= number_format($book['normal_price']) ?>원</td>
                    <td><?= $book['discount_rate'] ?>%</td>
                    <td><strong><?= number_format($book['sale_price']) ?>원</strong></td>
                    <td>
                        <?= $book['is_free'] == 'Y' ? '<span class="badge-free">무료</span>' : '-' ?>
                    </td>
                    <td>
                        <?php if($book['book_status'] == 1): ?>
                            <span class="status-active"><i class="fa fa-check-circle"></i> 판매중</span>
                        <?php else: ?>
                            <span class="status-pending"><i class="fa fa-clock-o"></i> 대기</span>
                        <?php endif; ?>
                    </td>
                    <td><?= date('Y-m-d', strtotime($book['created_dt'])) ?></td>
                    <td class="action-links">
                        <a href="book_edit.php?id=<?= $book['ID'] ?>">
                            <i class="fa fa-edit"></i> 수정
                        </a> | 
                        <a href="book_delete.php?id=<?= $book['ID'] ?>" 
                           onclick="return confirm('정말 삭제하시겠습니까?')"
                           style="color: #dc3545;">
                            <i class="fa fa-trash"></i> 삭제
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <div style="margin-top: 20px; color: #666; text-align: right;">
            총 <strong><?= $result->num_rows ?></strong>개의 책
        </div>
        <?php else: ?>
        <div class="empty">
            <h2>📭 아직 등록된 책이 없습니다</h2>
            <p style="font-size: 16px; margin-bottom: 30px;">
                첫 번째 책을 업로드하고 독자들에게 선보이세요!
            </p>
            <a href="book_upload.php" class="btn btn-primary" style="font-size: 18px; padding: 15px 40px;">
                <i class="fa fa-upload"></i> 첫 책 업로드하기
            </a>
        </div>
        <?php endif; ?>
    </div>

</body>
</html>
<?php
$stmt->close();
?>
