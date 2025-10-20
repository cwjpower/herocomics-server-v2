<?php
require_once '../../wps-config.php';
require_once '../admin-header.php';

$mysqli = new mysqli('herocomics-mariadb', 'root', 'rootpass', 'herocomics');

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['publisher_name'];
    $code = $_POST['publisher_code'];
    $email = $_POST['contact_email'];
    $phone = $_POST['contact_phone'];
    $rate = $_POST['commission_rate'];
    
    $query = "INSERT INTO bt_publishers (publisher_name, publisher_code, contact_email, contact_phone, commission_rate, status) 
              VALUES ('$name', '$code', '$email', '$phone', $rate, 'active')";
    
    if($mysqli->query($query)) {
        echo "<script>alert('출판사가 추가되었습니다.');location.href='publisher_list.php';</script>";
    }
}
?>

<style>
.content-wrapper { background: #f4f4f4; padding: 20px; }
.box { background: white; border-top: 3px solid #3c8dbc; padding: 20px; }
</style>

<div class="content-wrapper">
    <section class="content-header">
        <h1>새 출판사 추가</h1>
    </section>
    
    <section class="content">
        <div class="box">
            <form method="POST">
                <div class="form-group">
                    <label>출판사명 *</label>
                    <input type="text" name="publisher_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>출판사 코드 *</label>
                    <input type="text" name="publisher_code" class="form-control" placeholder="예: MARVEL, DC" required>
                </div>
                <div class="form-group">
                    <label>담당자 이메일 *</label>
                    <input type="email" name="contact_email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>연락처</label>
                    <input type="text" name="contact_phone" class="form-control">
                </div>
                <div class="form-group">
                    <label>수수료율(%)</label>
                    <input type="number" name="commission_rate" class="form-control" value="30" min="0" max="100">
                </div>
                <button type="submit" class="btn btn-primary">추가</button>
                <a href="publisher_list.php" class="btn btn-default">취소</a>
            </form>
        </div>
    </section>
</div>

<?php require_once '../admin-footer.php'; ?>
