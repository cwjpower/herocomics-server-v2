<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['code' => 1, 'msg' => 'POST 요청만 허용됩니다.']);
    exit;
}

$db_host = 'herocomics-mariadb';
$db_name = 'herocomics';
$db_user = 'root';
$db_pass = 'rootpass';

try {
    $pdo = new PDO(
        "mysql:host={$db_host};dbname={$db_name};charset=utf8mb4",
        $db_user,
        $db_pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    echo json_encode(['code' => 1, 'msg' => 'DB 연결 실패']);
    exit;
}

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    echo json_encode(['code' => 1, 'msg' => '이메일과 비밀번호를 입력해주세요.']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT 
            u.ID as uid,
            u.user_pass,
            u.user_email,
            u.display_name,
            u.user_level,
            u.user_status,
            u.publisher_id,
            u.role,
            p.publisher_name,
            p.publisher_code,
            p.status as publisher_status
        FROM bt_users u
        LEFT JOIN bt_publishers p ON u.publisher_id = p.publisher_id
        WHERE u.user_email = ?
        AND u.user_level >= 50
        LIMIT 1
    ");
    
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        echo json_encode(['code' => 1, 'msg' => '어드민 계정이 존재하지 않습니다.']);
        exit;
    }

    if ($user['user_status'] != 0) {
        echo json_encode(['code' => 1, 'msg' => '비활성화된 계정입니다.']);
        exit;
    }

    if ($user['user_level'] == 50 && $user['publisher_status'] != 'active') {
        echo json_encode(['code' => 1, 'msg' => '출판사를 이용할 수 없습니다.']);
        exit;
    }

    if ($user['user_pass'] !== md5($password)) {
        echo json_encode(['code' => 1, 'msg' => '비밀번호가 일치하지 않습니다.']);
        exit;
    }

    $permission = ($user['user_level'] == 100) ? 'super_admin' : 'publisher_admin';

    $token_data = [
        'uid' => $user['uid'],
        'email' => $user['user_email'],
        'level' => $user['user_level'],
        'permission' => $permission,
        'publisher_id' => $user['publisher_id'],
        'role' => $user['role'],
        'time' => time()
    ];
    
    $token = base64_encode(json_encode($token_data));

   $pdo->prepare("UPDATE bt_users SET last_login_dt = NOW() WHERE ID = ?")->execute([$user['uid']]);

    $response = [
        'code' => 0,
        'msg' => '로그인 성공',
        'token' => $token,
        'admin' => [
            'uid' => $user['uid'],
            'email' => $user['user_email'],
            'name' => $user['display_name'],
            'level' => $user['user_level'],
            'permission' => $permission,
            'role' => $user['role']
        ]
    ];

    if ($user['user_level'] == 50 && $user['publisher_id']) {
        $response['admin']['publisher'] = [
            'id' => $user['publisher_id'],
            'name' => $user['publisher_name'],
            'code' => $user['publisher_code']
        ];
    }

    echo json_encode($response);

} catch (PDOException $e) {
    echo json_encode(['code' => 1, 'msg' => 'DB 오류: ' . $e->getMessage()]);
    exit;
}
?>
