<?php
// 페이지 정보 설정
$page_title = "출판사 설정";
$current_page = "settings";

// ✅ DB 연결 먼저 (여기서 자동으로 session_start 됨)
// ✅ DB 연결 먼저 (여기서 자동으로 session_start 됨)
require_once '../../includes/html_helpers.php';
require_once '../../wps-config.php';
require_once '../../wps-settings.php';

// ✅ 세션 체크
if (!isset($_SESSION['publisher_id'])) {
    $_SESSION['publisher_id'] = 1; // 임시
}

$publisher_id = $_SESSION['publisher_id'];

// 출판사 정보 조회
if (!isset($wdb) || !is_object($wdb)) {
    die("DB 연결 실패");
}

$query = "SELECT * FROM bt_publishers WHERE publisher_id = ?";
$stmt = $wdb->prepare($query);
if (!$stmt) {
    die("쿼리 준비 실패: " . $wdb->error);
}
$stmt->bind_param('i', $publisher_id);
$stmt->execute();
$result = $stmt->get_result();
$publisher = $result->fetch_assoc();
$stmt->close();

if (!$publisher) {
    $publisher = [
        'publisher_code' => 'TEST',
        'publisher_name' => '테스트 출판사',
        'publisher_name_en' => '',
        'contact_name' => '',
        'contact_email' => '',
        'contact_phone' => '',
        'address' => ''
    ];
}

// 헤더 포함
require_once '../layout/header.php';
?>

<!-- 출판사 설정 페이지 컨텐츠 -->
<h1 class="mb-4">⚙️ 출판사 설정</h1>

<!-- 탭 네비게이션 -->
<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link active" href="profile.php">기본정보</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="account.php">계좌정보</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="../dashboard.php">← 대시보드</a>
    </li>
</ul>

<!-- 기본정보 카드 -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h5 class="card-title mb-4">📋 기본 정보</h5>
        
        <form method="POST" action="update.php">
            <input type="hidden" name="action" value="profile">
            
            <div class="mb-3">
                <label class="form-label">출판사 코드</label>
                <input type="text" class="form-control" value="<?php echo h($publisher['publisher_code']); ?>" readonly>
            </div>

            <div class="mb-3">
                <label class="form-label">출판사명 (한글) *</label>
                <input type="text" class="form-control" name="publisher_name" value="<?php echo h($publisher['publisher_name']); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">출판사명 (영문)</label>
                <input type="text" class="form-control" name="publisher_name_en" value="<?php echo htmlspecialchars(strval($publisher['publisher_name_en'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">담당자명</label>
                <input type="text" class="form-control" name="contact_name" value="<?php echo h($publisher['contact_name']); ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">이메일 *</label>
                <input type="email" class="form-control" name="contact_email" value="<?php echo h($publisher['contact_email']); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">전화번호</label>
                <input type="tel" class="form-control" name="contact_phone" value="<?php echo h($publisher['contact_phone']); ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">주소</label>
                <input type="text" class="form-control" name="address" value="<?php echo h($publisher['address']); ?>">
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-primary">💾 저장하기</button>
            </div>
        </form>
    </div>
</div>

<!-- 비밀번호 변경 카드 -->
<div class="card shadow-sm">
    <div class="card-body">
        <h5 class="card-title mb-4">🔐 비밀번호 변경</h5>
        
        <form method="POST" action="update.php" onsubmit="return validatePassword()">
            <input type="hidden" name="action" value="password">
            
            <div class="mb-3">
                <label class="form-label">현재 비밀번호 *</label>
                <input type="password" class="form-control" name="current_password" id="current_password" required>
            </div>

            <div class="mb-3">
                <label class="form-label">새 비밀번호 *</label>
                <input type="password" class="form-control" name="new_password" id="new_password" required>
                <div class="form-text">최소 8자 이상 입력해주세요</div>
            </div>

            <div class="mb-3">
                <label class="form-label">새 비밀번호 확인 *</label>
                <input type="password" class="form-control" name="confirm_password" id="confirm_password" required>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-success">🔑 비밀번호 변경</button>
            </div>
        </form>
    </div>
</div>

<script>
function validatePassword() {
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (newPassword.length < 8) {
        alert('비밀번호는 최소 8자 이상이어야 합니다.');
        return false;
    }
    
    if (newPassword !== confirmPassword) {
        alert('새 비밀번호가 일치하지 않습니다.');
        return false;
    }
    
    return true;
}
</script>

<?php
// ✅ DB 연결 정리
if (isset($wdb) && is_object($wdb)) {
    @$wdb->close();
}
require_once '../layout/footer.php';
?>
