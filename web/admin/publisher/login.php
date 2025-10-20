<?php
session_start();

// 간단한 로그인 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // 임시 인증 (나중에 DB 체크로 변경)
    if ($email === 'marvel@herocomics.com' && $password === 'marvel123') {
        $_SESSION['publisher_id'] = 1;
        $_SESSION['user_login'] = $email;
        header('Location: settings/profile.php');
        exit;
    } else {
        $error = '로그인 실패';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>출판사 로그인</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: sans-serif; background: #f5f5f5; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .login-box { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 400px; }
        h1 { margin-bottom: 30px; text-align: center; }
        input { width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 5px; }
        button { width: 100%; padding: 12px; background: #4CAF50; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .error { color: red; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="login-box">
        <h1>🏢 출판사 로그인</h1>
        <?php if (isset($error)): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="email" name="email" placeholder="이메일" required>
            <input type="password" name="password" placeholder="비밀번호" required>
            <button type="submit">로그인</button>
        </form>
        <p style="margin-top: 20px; font-size: 12px; color: #666;">
            테스트 계정: marvel@herocomics.com / marvel123
        </p>
    </div>
</body>
</html>
