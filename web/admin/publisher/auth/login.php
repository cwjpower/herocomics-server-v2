<?php
session_start();

// 이미 로그인되어 있으면 대시보드로
if (isset($_SESSION['publisher_id'])) {
    header('Location: ../dashboard.php');
    exit;
}

require_once '../../wps-config.php';
require_once '../../wps-settings.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $error = '이메일과 비밀번호를 입력해주세요.';
    } else {
        // 출판사 조회
        $query = "SELECT * FROM bt_publishers WHERE contact_email = ? AND status = 'active'";
        $stmt = $wdb->prepare($query);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $publisher = $wdb->get_row($stmt);
        
        if ($publisher && !empty($publisher['password']) && password_verify($password, $publisher['password'])) {
            // 로그인 성공
            $_SESSION['publisher_id'] = $publisher['publisher_id'];
            $_SESSION['publisher_name'] = $publisher['publisher_name'];
            $_SESSION['publisher_email'] = $publisher['contact_email'];
            
            header('Location: ../dashboard.php');
            exit;
        } else {
            $error = '이메일 또는 비밀번호가 올바르지 않습니다.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>로그인 - HeroComics 출판사</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            max-width: 450px;
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
        
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 12px;
            font-weight: 600;
        }
        
        .btn-login:hover {
            opacity: 0.9;
            color: white;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <h1>📚 HeroComics</h1>
            <p class="text-muted">출판사 관리 시스템</p>
        </div>
        
        <?php if (!empty($error)): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">이메일</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label">비밀번호</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn btn-login w-100">로그인</button>
        </form>
        
        <div class="text-center mt-3">
            <a href="register.php">출판사 회원가입</a>
        </div>
    </div>
</body>
</html>
