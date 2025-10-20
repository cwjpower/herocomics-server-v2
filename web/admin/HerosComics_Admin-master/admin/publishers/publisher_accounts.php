<?php
require_once '../../wps-config.php';
require_once '../admin-header.php';

$mysqli = new mysqli('herocomics-mariadb', 'root', 'rootpass', 'herocomics');

// 폼 제출 처리
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if($_POST['action'] == 'create_account') {
        $publisher_id = $_POST['publisher_id'];
        $email = $_POST['email'];
        $password = md5($_POST['password']);
        
        // bt_users에 출판사 계정 생성
        $query = "INSERT INTO bt_users (user_login, user_email, user_pass, user_level, publisher_id, user_status) 
                  VALUES ('$email', '$email', '$password', '7', $publisher_id, '0')";
        $mysqli->query($query);
        echo "<script>alert('계정이 생성되었습니다.');</script>";
    }
}

// 출판사 목록과 계정 정보 조회
$query = "SELECT p.*, u.user_email, u.user_status, u.ID as user_id
          FROM bt_publishers p
          LEFT JOIN bt_users u ON p.publisher_id = u.publisher_id AND u.user_level = '7'
          ORDER BY p.publisher_id";
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
        <h1>출판사 계정 관리</h1>
    </section>
    
    <section class="content">
        <div class="box">
            <div class="box-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>출판사명</th>
                            <th>로그인 계정</th>
                            <th>상태</th>
                            <th>관리</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['publisher_name']; ?></td>
                            <td><?php echo $row['user_email'] ?: '계정 없음'; ?></td>
                            <td>
                                <?php if($row['user_email']): ?>
                                    <?php echo $row['user_status'] == '0' ? '<span class="label label-success">활성</span>' : '<span class="label label-danger">비활성</span>'; ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if(!$row['user_email']): ?>
                                    <button onclick="createAccount(<?php echo $row['publisher_id']; ?>, '<?php echo $row['publisher_name']; ?>')" class="btn btn-xs btn-primary">계정 생성</button>
                                <?php else: ?>
                                    <button onclick="resetPassword(<?php echo $row['user_id']; ?>)" class="btn btn-xs btn-warning">비밀번호 초기화</button>
                                    <button onclick="toggleStatus(<?php echo $row['user_id']; ?>)" class="btn btn-xs btn-info">상태 변경</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- 계정 생성 모달 -->
        <div id="createModal" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); background:white; padding:20px; border:1px solid #ccc; z-index:1000;">
            <h3>출판사 계정 생성</h3>
            <form method="POST">
                <input type="hidden" name="action" value="create_account">
                <input type="hidden" name="publisher_id" id="publisher_id">
                <div class="form-group">
                    <label>출판사: <span id="publisher_name"></span></label>
                </div>
                <div class="form-group">
                    <label>이메일 (로그인ID)</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>비밀번호</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">생성</button>
                <button type="button" onclick="closeModal()" class="btn btn-default">취소</button>
            </form>
        </div>
    </section>
</div>

<script>
function createAccount(id, name) {
    document.getElementById('publisher_id').value = id;
    document.getElementById('publisher_name').innerText = name;
    document.getElementById('createModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('createModal').style.display = 'none';
}

function resetPassword(userId) {
    if(confirm('비밀번호를 초기화하시겠습니까? (초기 비밀번호: 1234)')) {
        location.href = 'publisher_reset_password.php?user_id=' + userId;
    }
}

function toggleStatus(userId) {
    location.href = 'publisher_toggle_status.php?user_id=' + userId;
}
</script>

<?php require_once '../admin-footer.php'; ?>
