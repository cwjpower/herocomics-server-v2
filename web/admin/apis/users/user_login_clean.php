<?php
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// DB 연결
$host = 'herocomics-mariadb';
$user = 'root';
$pass = 'rootpass';
$db = 'herocomics';

$conn = mysqli_connect($host, $user, $pass, $db);
mysqli_set_charset($conn, "utf8");

// POST 데이터 받기
$user_login = $_POST['user_login'] ?? '';
$user_pass = $_POST['user_pass'] ?? '';

if (empty($user_login) || empty($user_pass)) {
    die(json_encode(['code' => 1, 'msg' => '로그인 정보를 입력하세요']));
}

// bt_users 테이블에서 확인
$query = "SELECT ID, user_login, user_pass, user_name, display_name, user_level, user_status 
          FROM bt_users 
          WHERE user_login = ?";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $user_login);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    // 비밀번호 확인 (평문 비교)
    if ($row['user_pass'] === $user_pass) {
        // 로그인 성공
        $token = base64_encode(json_encode([
            'user_id' => $row['ID'],
            'user_login' => $row['user_login'],
            'timestamp' => time()
        ]));
        
        // 마지막 로그인 시간 업데이트
        $update_query = "UPDATE bt_users SET last_login_dt = NOW() WHERE ID = ?";
        $update_stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($update_stmt, "i", $row['ID']);
        mysqli_stmt_execute($update_stmt);
        
        echo json_encode([
            'code' => 0,
            'msg' => '로그인 성공',
            'token' => $token,
            'uid' => strval($row['ID']),
            'user_name' => $row['user_name'] ?? $row['user_login'],
            'display_name' => $row['display_name'],
            'user_level' => strval($row['user_level'])
        ]);
    } else {
        echo json_encode(['code' => 1, 'msg' => '비밀번호가 틀렸습니다']);
    }
} else {
    echo json_encode(['code' => 1, 'msg' => '존재하지 않는 사용자입니다']);
}

mysqli_close($conn);
?>
