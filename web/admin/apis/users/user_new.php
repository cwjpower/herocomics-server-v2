<?php
/*
 * Desc : 회원 가입
 */
require_once '../../wps-config.php';

$code = 0;
$msg = '';

if ( empty($_POST['user_login']) ) {
	$code = 404;
	$msg = '계정을 입력해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( empty($_POST['user_name']) ) {
	$code = 405;
	$msg = '이름을 입력해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( empty($_POST['user_pass']) ) {
	$code = 406;
	$msg = '비밀번호를 입력해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( empty($_POST['display_name']) ) {
	$code = 407;
	$msg = '닉네임을 입력해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$user_login = trim($_POST['user_login']);
$user_name = trim($_POST['user_name']);
$user_pass = wps_get_password($_POST['user_pass']);
$display_name = trim($_POST['display_name']);

if (!filter_var($user_login, FILTER_VALIDATE_EMAIL)) {
	$code = 412;
	$msg = '요청하신 계정이 이메일 주소 형식이 아닙니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if (lps_check_user_login($user_login)) {
	$code = 413;
	$msg = '이미 사용하고 있는 이메일입니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$pattern = '/[^a-zA-Z0-9가-히]/';

if (preg_match($pattern, $display_name)) {
	$code = 421;
	$msg = '닉네임에는 특수문자를 사용할 수 없습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if (lps_check_display_name($display_name)) {
	$code = 422;
	$msg = '이미 사용중인 닉네임입니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$user_email = $user_login;
$mobile = empty($_POST['mobile']) ? '' : lps_easy_to_read_mobile($_POST['mobile']);
$authcode = empty($_POST['authcode']) ? '' : $_POST['authcode'];
// $birthday = empty($_POST['birthday']) ? '' : $_POST['birthday'];

if (empty($_POST['birthday'])) {
	$birthday = '';
} else {	// yyyymmdd -> yyyy-mm-dd
	$birthday = lps_filter_birth_format($_POST['birthday']);
}

$gender = empty($_POST['gender']) ? '' : $_POST['gender'];
$join_path = 'app';
$residence = empty($_POST['residence']) ? '' : $_POST['residence'];
$last_school = empty($_POST['last_school']) ? '' : $_POST['last_school'];

$user_status = empty($_POST['user_status']) ? '0' : $_POST['user_status'];
$user_level = empty($_POST['user_level']) ? '1' : $_POST['user_level'];

$query = "
		INSERT INTO
			bt_users
			(
				ID,
				user_login,
				user_pass,
				user_name,
				user_email,
				display_name,
				user_registered,
				user_status,
				user_level,
				mobile,
				birthday,
				gender,
				residence,
				last_school,
				join_path,
				last_login_dt
			)
		VALUES
			(
				NULL, ?, ?, ?, ?,
				?, NOW(), ?, ?, ?,
				?, ?, ?, ?, ?,
				NOW()
			)
";
$stmt = $wdb->prepare($query);
$stmt->bind_param( 'sssssiisssiis',
		$user_login, $user_pass, $user_name, $user_email,
		$display_name, $user_status, $user_level, $mobile,
		$birthday, $gender, $residence, $last_school, $join_path
);
$stmt->execute();
$user_id = $wdb->insert_id;

if ( $user_id ) {
	wps_update_user_meta( $user_id, 'wps_user_level', $user_level );
}

$json = compact('code', 'msg');
echo json_encode( $json );

?>