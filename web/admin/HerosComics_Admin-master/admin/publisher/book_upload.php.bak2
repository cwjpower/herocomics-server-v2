<?php
require_once '../../wps-config.php';


if (!isset($_SESSION['login']['userid']) || $_SESSION['login']['user_level'] != 7) {
    header('Location: /admin/login.php');
    exit;
}

$publisher_id = $_SESSION['login']['publisher_id'];
$display_name = $_SESSION['login']['display_name'];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>책 업로드 - Hero Comics</title>
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
        input[type="file"] { padding: 10px; }
        button { 
            background: #007bff; 
            color: white; 
            padding: 12px 30px; 
            border: none; 
            border-radius: 4px; 
            cursor: pointer; 
            font-size: 16px; 
            width: 100%;
        }
        button:hover { background: #0056b3; }
        .info { 
            background: #e7f3ff; 
            padding: 15px; 
            border-left: 4px solid #007bff; 
            margin-bottom: 20px; 
            border-radius: 4px;
        }
        .warning {
            background: #fff3cd;
            padding: 15px;
            border-left: 4px solid #ffc107;
            margin-bottom: 20px;
            border-radius: 4px;
        }
    </style>
</head>
<body>

<div class="top-bar">
    <h1>📚 새 책 업로드 (<?= htmlspecialchars($display_name) ?>)</h1>
    <a href="book_list.php" class="btn">← 목록으로</a>
</div>

<div class="info">
    <strong>📦 ZIP 파일 업로드 안내:</strong><br>
    • frame.avf 파일이 <strong>없어도 됩니다!</strong> 자동으로 생성됩니다.<br>
    • 만화 이미지 파일만 ZIP으로 압축해서 올려주세요.<br>
    • 지원 이미지: jpg, jpeg, png, gif, webp
</div>

<div class="warning">
    <strong>⚠️ 주의사항:</strong><br>
    • 이미지 파일명은 숫자 순서대로 정렬됩니다 (001.jpg, 002.jpg ...)<br>
    • 표지 이미지와 책 파일은 필수입니다.
</div>

<form action="book_upload_process.php" method="POST" enctype="multipart/form-data">

    <div class="form-group">
        <label>책 제목 *</label>
        <input type="text" name="book_title" required placeholder="예: Spider-Man Vol.1">
    </div>

    <div class="form-group">
        <label>저자 *</label>
        <input type="text" name="author" required placeholder="예: Stan Lee">
    </div>

    <div class="form-group">
        <label>ISBN</label>
        <input type="text" name="isbn" placeholder="예: 978-1234567890">
    </div>

    <div class="form-group">
        <label>정가 (원) *</label>
        <input type="number" name="normal_price" required placeholder="10000" min="0">
    </div>

    <div class="form-group">
        <label>할인율 (%)</label>
        <input type="number" name="discount_rate" value="0" min="0" max="100">
    </div>

    <div class="form-group">
        <label>무료 책</label>
        <select name="is_free">
            <option value="N">아니오 (유료)</option>
            <option value="Y">예 (무료)</option>
        </select>
    </div>

    <div class="form-group">
        <label>표지 이미지 * (jpg, png)</label>
        <input type="file" name="cover_img" accept="image/*" required>
    </div>

    <div class="form-group">
        <label>책 파일 (ZIP) * - frame.avf 자동 생성!</label>
        <input type="file" name="book_zip" accept=".zip" required>
    </div>

    <button type="submit">📤 업로드 시작</button>
</form>

</body>
</html>
