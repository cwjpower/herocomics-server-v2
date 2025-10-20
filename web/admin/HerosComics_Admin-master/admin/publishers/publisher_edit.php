<?php
require_once '../../wps-config.php';
$mysqli = new mysqli('herocomics-mariadb', 'root', 'rootpass', 'herocomics');

$id = $_GET['id'] ?? 0;

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $query = "UPDATE bt_publishers SET 
              publisher_name = '{$_POST['publisher_name']}',
              publisher_code = '{$_POST['publisher_code']}',
              contact_email = '{$_POST['contact_email']}',
              contact_phone = '{$_POST['contact_phone']}',
              commission_rate = {$_POST['commission_rate']}
              WHERE publisher_id = $id";
    
    if($mysqli->query($query)) {
        echo "<script>alert('수정되었습니다.');location.href='publisher_list.php';</script>";
    }
}

$result = $mysqli->query("SELECT * FROM bt_publishers WHERE publisher_id = $id");
$publisher = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>출판사 수정</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h1>출판사 수정</h1>
    <form method="POST">
        <div class="form-group">
            <label>출판사명</label>
            <input type="text" name="publisher_name" class="form-control" value="<?php echo $publisher['publisher_name']; ?>" required>
        </div>
        <div class="form-group">
            <label>출판사 코드</label>
            <input type="text" name="publisher_code" class="form-control" value="<?php echo $publisher['publisher_code']; ?>" required>
        </div>
        <div class="form-group">
            <label>이메일</label>
            <input type="email" name="contact_email" class="form-control" value="<?php echo $publisher['contact_email']; ?>" required>
        </div>
        <div class="form-group">
            <label>연락처</label>
            <input type="text" name="contact_phone" class="form-control" value="<?php echo $publisher['contact_phone']; ?>">
        </div>
        <div class="form-group">
            <label>수수료율(%)</label>
            <input type="number" name="commission_rate" class="form-control" value="<?php echo $publisher['commission_rate']; ?>">
        </div>
        <button type="submit" class="btn btn-primary">수정</button>
        <a href="publisher_list.php" class="btn btn-default">취소</a>
    </form>
</div>
</body>
</html>
