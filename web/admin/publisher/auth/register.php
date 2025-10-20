<?php
session_start();
require_once '../../wps-config.php';
require_once '../../wps-settings.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $publisher_name = trim($_POST['publisher_name']);
    $publisher_code = strtoupper(trim($_POST['publisher_code']));
    $contact_email = trim($_POST['contact_email']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    $contact_name = trim($_POST['contact_name']);
    $contact_phone = trim($_POST['contact_phone']);
    
    // 유효성 검사
    if (empty($publisher_name) || empty($publisher_code) || empty($contact_email) || empty($password)) {
        $error = '필수 항목을 모두 입력해주세요.';
    } elseif ($password !== $password_confirm) {
        $error = '비밀번호가 일치하지 않습니다.';
    } elseif (strlen($password) < 6) {
        $error = '비밀번호는 최소 6자 이상이어야 합니다.';
    } else {
        // 중복 확인
        $check_query = "SELECT publisher_id FROM bt_publishers WHERE contact_email = ? OR publisher_code = ?";
        $stmt = $wdb->prepare($check_query);
        $stmt->bind_param('ss', $contact_email, $publisher_code);
        $stmt->execute();
        $existing = $wdb->get_row($stmt);
        
        if ($existing) {
            $error = '이미 등록된 이메일 또는 출판사 코드입니다.';
        } else {
            // 비밀번호 해시화
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // 출판사 등록
            $insert_query = "
                INSERT INTO bt_publishers 
                (publisher_name, publisher_code, contact_email, password, contact_name, contact_phone, status, created_at)
                VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())
            ";
            
            $stmt = $wdb->prepare($insert_query);
            $stmt->bind_param('ssssss', $publisher_name, $publisher_code, $contact_email, $hashed_password, $contact_name, $contact_phone);
            
            if ($stmt->execute()) {
                $success = '회원가입이 완료되었습니다. 관리자 승인 후 로그인 가능합니다.';
            } else {
                $error = '회원가입 중 오류가 발생했습니다.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>회원가입 - HeroComics 출판사</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .register-container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            max-width: 600px;
            width: 100%;
        }
        
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo h1 {
            color: #667eea;
            font-weight: bold;
        }
        
        .btn-register {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 12px;
            font-weight: 600;
        }
        
        .btn-register:hover {
            opacity: 0.9;
            color: white;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="logo">
            <h1>📚 HeroComics</h1>
            <p class="text-muted">출판사 회원가입</p>
        </div>
        
        <?php if (!empty($error)): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
        <div class="alert alert-success" role="alert">
            <?php echo htmlspecialchars($success); ?>
            <br><a href="login.php">로그인 페이지로 이동</a>
        </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="publisher_name" class="form-label">출판사명 *</label>
                    <input type="text" class="form-control" id="publisher_name" name="publisher_name" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="publisher_code" class="form-label">출판사 코드 *</label>
                    <input type="text" class="form-control" id="publisher_code" name="publisher_code" required>
                    <small class="text-muted">영문 대문자로 입력</small>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="contact_email" class="form-label">이메일 (로그인 ID) *</label>
                <input type="email" class="form-control" id="contact_email" name="contact_email" required>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label">비밀번호 *</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                    <small class="text-muted">최소 6자 이상</small>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="password_confirm" class="form-label">비밀번호 확인 *</label>
                    <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="contact_name" class="form-label">담당자명</label>
                    <input type="text" class="form-control" id="contact_name" name="contact_name">
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="contact_phone" class="form-label">연락처</label>
                    <input type="tel" class="form-control" id="contact_phone" name="contact_phone">
                </div>
            </div>
            
            <button type="submit" class="btn btn-register w-100">회원가입</button>
        </form>
        
        <div class="text-center mt-3">
            <a href="login.php">이미 계정이 있으신가요? 로그인</a>
        </div>
    </div>
</body>
</html>
