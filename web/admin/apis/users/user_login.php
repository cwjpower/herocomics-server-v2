<?php require_once("/var/www/html/admin/error_off.php"); ?>
<?php
/*
 * Desc : 회원 로그인 (JWT 토큰 방식)
 */
require_once '../../wps-config.php';
require_once '../jwt_helper.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Accept");


if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

$code = 0;
$msg = '';

if (empty($_POST['user_login'])) {
    $code = 510;
    $msg = '계정(이메일 주소)을 입력해 주십시오.';
    $json = compact('code', 'msg');
    exit(json_encode($json));
}

if (empty($_POST['user_pass'])) {
    $code = 602;
    $msg = '비밀번호를 입력해 주십시오.';
    $json = compact('code', 'msg');
    exit(json_encode($json));
}

$user_login = $_POST['user_login'];
$user_pass = wps_get_password($_POST['user_pass']);
$user_data = wps_get_user_by('user_login', $user_login);
$ID = $user_data['ID'];

if (empty($ID)) {
    $code = 501;
    $msg = '존재하지 않는 이메일 주소입니다.';
    $json = compact('code', 'msg');
    exit(json_encode($json));
}

if (strcmp($user_data['user_pass'], $user_pass)) {
    $code = 601;
    $msg = '비밀번호를 잘못 입력하셨습니다.';
    $json = compact('code', 'msg');
    exit(json_encode($json));
}

if ($user_data['user_status'] == '1') {
    $code = 503;
    $msg = '차단된 회원입니다.';
    $json = compact('code', 'msg');
    exit(json_encode($json));
}

if ($user_data['user_status'] == '4') {
    $code = 504;
    $msg = '탈퇴하신 회원입니다.';
    $json = compact('code', 'msg');
    exit(json_encode($json));
}

// 사용자 메타 정보
$umeta = wps_get_user_meta($ID);
$user_level = $umeta['wps_user_level'];
$profile_avatar = @$umeta['wps_user_avatar'];

// JWT 토큰 생성
$token_payload = [
    'uid' => $ID,
    'user_login' => $user_data['user_login'],
    'user_name' => $user_data['user_name'],
    'display_name' => $user_data['display_name'],
    'user_level' => $user_level,
    'exp' => time() + (7 * 24 * 60 * 60) // 7일 유효
];

$token = JWTHelper::encode($token_payload);

// 응답
$uid = $ID;
$user_name = $user_data['user_name'];
$display_name = $user_data['display_name'];

$json = compact('code', 'msg', 'token', 'uid', 'user_name', 'display_name', 'user_level', 'profile_avatar');
echo json_encode($json);
?>
