<?php
session_start();

// POST 로그인 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['admin_id'] ?? '';
    $pw = $_POST['admin_pw'] ?? '';
    
    // 간단한 로그인 (나중에 DB 연동)
    if ($id === 'admin' && $pw === 'admin1234') {
        $_SESSION['admin_id'] = $id;
        header('Location: dashboard.php');
        exit;
    } else {
        $error = '아이디 또는 비밀번호가 틀립니다.';
    }
}
?>
<!doctype html>
<meta charset="utf-8">
<title>HeroComics 관리자 로그인</title>
<style>
body{font-family:system-ui;display:flex;justify-content:center;align-items:center;height:100vh;margin:0;background:#f5f5f5}
.login-box{background:white;padding:40px;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,0.1);width:300px}
h2{margin:0 0 30px 0;text-align:center;color:#333}
input{width:100%;padding:10px;margin:8px 0;border:1px solid #ddd;border-radius:6px;box-sizing:border-box}
button{width:100%;padding:12px;background:#007bff;color:white;border:none;border-radius:6px;cursor:pointer;margin-top:10px}
button:hover{background:#0056b3}
.error{color:red;font-size:14px;margin:10px 0}
</style>

<div class="login-box">
    <h2>🎮 HeroComics</h2>
    <?php if (isset($error)): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>
    <form method="POST">
        <input type="text" name="admin_id" placeholder="아이디" required autofocus>
        <input type="password" name="admin_pw" placeholder="비밀번호" required>
        <button type="submit">로그인</button>
    </form>
    <p style="text-align:center;color:#999;font-size:12px;margin-top:20px">
        테스트: admin / admin1234
    </p>
</div>
