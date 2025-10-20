<?php
session_start();
require_once '../../wps-config.php';
require_once '../../wps-settings.php';

if (!isset($_SESSION['publisher_id'])) {
    $_SESSION['publisher_id'] = 1;
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>계좌정보 - HeroComics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include "../includes/sidebar.php"; ?>

<div class="main-content" style="margin-left: 270px; padding: 20px;">
    <h1>⚙️ 출판사 설정</h1>
    
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link" href="profile.php">기본정보</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="account.php">계좌정보</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="../dashboard.php">← 대시보드</a>
        </li>
    </ul>
    
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">💰 정산 계좌 설정</h5>
            
            <div class="alert alert-info mb-4">
                💡 정산금은 매월 1일에 등록하신 계좌로 자동 입금됩니다.
            </div>
            
            <form method="POST" action="update.php">
                <input type="hidden" name="action" value="account">
                
                <div class="mb-3">
                    <label class="form-label">은행 *</label>
                    <select class="form-control" name="bank_name" required>
                        <option value="국민은행" selected>국민은행</option>
                        <option value="신한은행">신한은행</option>
                        <option value="우리은행">우리은행</option>
                        <option value="하나은행">하나은행</option>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">계좌번호 * (숫자만 입력)</label>
                    <input type="text" class="form-control" name="account_number" value="23423423423323" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">예금주 *</label>
                    <input type="text" class="form-control" name="account_holder" value="sdf" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">수수료율</label>
                    <input type="text" class="form-control" name="commission_rate" value="30.00%" readonly>
                    <small class="text-muted">* 수수료율은 슈퍼 어드민이 관리합니다</small>
                </div>
                
                <button type="submit" class="btn btn-primary">💾 저장하기</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>