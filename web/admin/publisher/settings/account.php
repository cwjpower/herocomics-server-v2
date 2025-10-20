<?php
require_once '../../wps-config.php';
require_once '../../wps-settings.php';

if (!isset($_SESSION['publisher_id'])) {
    header('Location: ../login.php');
    exit;
}

$publisher_id = $_SESSION['publisher_id'];

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
        'bank_name' => '',
        'bank_account' => '',
        'bank_holder' => '',
        'commission_rate' => 30
    ];
}

$base_path = '../';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>정산 계좌 설정 - HeroComics</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Noto Sans KR', -apple-system, BlinkMacSystemFont, sans-serif; 
            background: #f5f7fa;
        }
        
        .container { 
            max-width: 1200px; 
            margin: 0 auto; 
            padding: 30px;
        }
        
        .page-title {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .page-title h1 {
            font-size: 28px;
            color: #2d3748;
        }
        
        .page-icon {
            font-size: 36px;
        }
        
        .card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }
        
        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            border-bottom: 2px solid #e2e8f0;
        }
        
        .tab {
            padding: 12px 24px;
            text-decoration: none;
            color: #718096;
            border-bottom: 2px solid transparent;
            margin-bottom: -2px;
            transition: all 0.3s;
            font-weight: 500;
        }
        
        .tab:hover {
            color: #667eea;
        }
        
        .tab.active {
            color: #667eea;
            border-bottom-color: #667eea;
        }
        
        .info-box {
            background: linear-gradient(135deg, #e0e7ff 0%, #e9d5ff 100%);
            padding: 16px 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            border-left: 4px solid #667eea;
        }
        
        .info-box p {
            color: #4c51bf;
            font-size: 14px;
            font-weight: 500;
        }
        
        .form-group { 
            margin-bottom: 24px; 
        }
        
        label { 
            display: block; 
            margin-bottom: 8px; 
            font-weight: 500; 
            color: #4a5568;
            font-size: 14px;
        }
        
        input, select {
            width: 100%; 
            padding: 12px 16px; 
            border: 1px solid #e2e8f0; 
            border-radius: 8px; 
            font-size: 14px;
            transition: all 0.3s;
        }
        
        input:focus, select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        input[readonly] { 
            background: #f7fafc;
            color: #718096;
        }
        
        .btn-group { 
            margin-top: 30px; 
            text-align: right; 
        }
        
        .btn { 
            padding: 12px 32px; 
            border: none; 
            border-radius: 8px; 
            cursor: pointer; 
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-primary { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover { 
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        
        small { 
            color: #a0aec0; 
            font-size: 12px; 
            display: block; 
            margin-top: 5px; 
        }
    </style>
</head>
<body>
<?php include "../includes/sidebar.php"; ?>
<div class="main-content">
    <?php 
    include '../includes/sidebar.php'; 
    include '../includes/header.php';
    ?>

    <div class="main-content">
        <div class="container">
            <div class="page-title">
                <span class="page-icon">💰</span>
                <h1>정산 계좌 설정</h1>
            </div>

            <!-- 탭 네비게이션 -->
            <div class="tabs">
                <a href="profile.php" class="tab">기본정보</a>
                <a href="account.php" class="tab active">계좌정보</a>
                <a href="<?php echo $base_path; ?>dashboard.php" class="tab">← 대시보드</a>
            </div>

            <div class="info-box">
                <p>💡 정산금은 매월 1일에 등록하신 계좌로 자동 입금됩니다.</p>
            </div>

            <div class="card">
                <form method="POST" action="update.php">
                    <input type="hidden" name="action" value="account">
                    
                    <div class="form-group">
                        <label>은행 *</label>
                        <select name="bank_name" required>
                            <option value="">선택하세요</option>
                            <option value="국민은행" <?php echo ($publisher['bank_name'] ?? '') == '국민은행' ? 'selected' : ''; ?>>국민은행</option>
                            <option value="신한은행" <?php echo ($publisher['bank_name'] ?? '') == '신한은행' ? 'selected' : ''; ?>>신한은행</option>
                            <option value="우리은행" <?php echo ($publisher['bank_name'] ?? '') == '우리은행' ? 'selected' : ''; ?>>우리은행</option>
                            <option value="하나은행" <?php echo ($publisher['bank_name'] ?? '') == '하나은행' ? 'selected' : ''; ?>>하나은행</option>
                            <option value="농협은행" <?php echo ($publisher['bank_name'] ?? '') == '농협은행' ? 'selected' : ''; ?>>농협은행</option>
                            <option value="기업은행" <?php echo ($publisher['bank_name'] ?? '') == '기업은행' ? 'selected' : ''; ?>>기업은행</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>계좌번호 * (숫자만 입력)</label>
                        <input type="text" name="bank_account" value="<?php echo htmlspecialchars($publisher['bank_account'] ?? ''); ?>" 
                               placeholder="1234567890" pattern="[0-9]+" required>
                    </div>

                    <div class="form-group">
                        <label>예금주 *</label>
                        <input type="text" name="bank_holder" value="<?php echo htmlspecialchars($publisher['bank_holder'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>수수료율</label>
                        <input type="text" value="<?php echo ($publisher['commission_rate'] ?? 30); ?>%" readonly>
                        <small>* 수수료율은 슈퍼 어드민이 관리합니다</small>
                    </div>

                    <div class="btn-group">
                        <button type="submit" class="btn btn-primary">💾 저장하기</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
