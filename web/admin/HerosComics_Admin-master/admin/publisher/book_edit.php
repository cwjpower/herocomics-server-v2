<?php
require_once '../../wps-config.php';

if (!isset($_SESSION['login']['userid']) || $_SESSION['login']['user_level'] != 7) {
    header('Location: /admin/login.php');
    exit;
}

$publisher_id = $_SESSION['login']['publisher_id'];
$display_name = $_SESSION['login']['display_name'];
$book_id = $_GET['id'] ?? 0;

if ($book_id <= 0) {
    die('잘못된 접근입니다.');
}

global $wdb;

// 본인 책인지 확인
$sql = "SELECT * FROM bt_books WHERE ID = ? AND publisher_id = ?";
$stmt = $wdb->prepare($sql);
$stmt->bind_param("ii", $book_id, $publisher_id);
$stmt->execute();
$result = $stmt->get_result();
$book = $result->fetch_assoc();
$stmt->close();

if (!$book) {
    die('❌ 수정 권한이 없거나 존재하지 않는 책입니다.');
}

// 수정 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_title = $_POST['book_title'];
    $author = $_POST['author'];
    $isbn = $_POST['isbn'] ?? '';
    $normal_price = (int)$_POST['normal_price'];
    $discount_rate = (int)$_POST['discount_rate'];
    $is_free = $_POST['is_free'];
    $sale_price = $normal_price * (100 - $discount_rate) / 100;
    
    $update_sql = "UPDATE bt_books SET 
                   book_title = ?, 
                   author = ?, 
                   isbn = ?, 
                   normal_price = ?, 
                   discount_rate = ?, 
                   sale_price = ?,
                   is_free = ?
                   WHERE ID = ? AND publisher_id = ?";
    
    $stmt = $wdb->prepare($update_sql);
    $stmt->bind_param("ssiiiisii", 
        $book_title, $author, $isbn, $normal_price, 
        $discount_rate, $sale_price, $is_free, $book_id, $publisher_id);
    
    if ($stmt->execute()) {
        $stmt->close();
        header('Location: book_list.php?msg=updated');
        exit;
    } else {
        $error = $wdb->error;
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>책 수정 - Hero Comics</title>
    <style>
        body { font-family: 'Noto Sans KR', sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        h1 { color: #333; }
        .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .btn { background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block; }
        .btn:hover { background: #5a6268; }
        .form-group { margin-bottom: 20px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; color: #333; }
        input[type="text"], input[type="number"], select { 
            width: 100%; 
            padding: 10px; 
            border: 1px solid #ddd; 
            border-radius: 4px; 
            box-sizing: border-box;
        }
        button { 
            background: #28a745; 
            color: white; 
            padding: 12px 30px; 
            border: none; 
            border-radius: 4px; 
            cursor: pointer; 
            font-size: 16px; 
            width: 100%;
        }
        button:hover { background: #218838; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
    </style>
</head>
<body>

<div class="top-bar">
    <h1>📝 책 수정 (<?= htmlspecialchars($display_name) ?>)</h1>
    <a href="book_list.php" class="btn">← 목록으로</a>
</div>

<?php if (isset($error)): ?>
<div class="error">❌ 수정 실패: <?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="POST">
    <div class="form-group">
        <label>책 제목 *</label>
        <input type="text" name="book_title" required value="<?= htmlspecialchars($book['book_title']) ?>">
    </div>

    <div class="form-group">
        <label>저자 *</label>
        <input type="text" name="author" required value="<?= htmlspecialchars($book['author']) ?>">
    </div>

    <div class="form-group">
        <label>ISBN</label>
        <input type="text" name="isbn" value="<?= htmlspecialchars($book['isbn']) ?>">
    </div>

    <div class="form-group">
        <label>정가 (원) *</label>
        <input type="number" name="normal_price" required value="<?= $book['normal_price'] ?>" min="0">
    </div>

    <div class="form-group">
        <label>할인율 (%)</label>
        <input type="number" name="discount_rate" value="<?= $book['discount_rate'] ?>" min="0" max="100">
    </div>

    <div class="form-group">
        <label>무료 책</label>
        <select name="is_free">
            <option value="N" <?= $book['is_free'] == 'N' ? 'selected' : '' ?>>아니오 (유료)</option>
            <option value="Y" <?= $book['is_free'] == 'Y' ? 'selected' : '' ?>>예 (무료)</option>
        </select>
    </div>

    <button type="submit">💾 수정 완료</button>
</form>

</body>
</html>
