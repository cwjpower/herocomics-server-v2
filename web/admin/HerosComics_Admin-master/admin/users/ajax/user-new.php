<?php
/*
 * 2016.08.10		softsyw
 * Desc : 회원 추가
 */
require_once '../../../wps-config.php';

$code = 0;
$msg = '';

if ( !wps_is_admin() ) {
	$code = 510;
	$msg = '관리자만 사용할 수 있습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( empty($_POST['user_pass']) ) {
	$code = 414;
	$msg = '비밀번호를 입력해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}
if ( empty($_POST['user_login']) ) {
	$code = 411;
	$msg = '계정(이메일)을 입력해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}
if ( empty($_POST['user_name']) ) {
	$code = 412;
	$msg = '이름을 입력해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$user_login = $_POST['user_login'];	// email

if (!filter_var($user_login, FILTER_VALIDATE_EMAIL)) {
	$code = 4112;
	$msg = '이메일 주소 형식이 아닙니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if (lps_check_user_login($user_login)) {
	$code = 4113;
	$msg = '이미 사용하고 있는 이메일입니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

// 사용자 업데이트
$result = lps_add_user();

$json = compact('code', 'msg', 'result');
echo json_encode( $json );
?>