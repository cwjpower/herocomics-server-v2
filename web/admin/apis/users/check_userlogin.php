<?php
/*
 * Desc : User Email Check
 */
require_once '../../wps-config.php';

$code = 0;
$msg = '';

if ( empty($_GET['user_login']) ) {
	$code = 404;
	$msg = '계정을 입력해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$user_login = $_GET['user_login'];

if (!filter_var($user_login, FILTER_VALIDATE_EMAIL)) {
	$code = 412;
	$msg = '이메일 주소 형식이 아닙니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if (lps_check_user_login($user_login)) {
	$code = 413;
	$msg = '이미 사용하고 있는 이메일입니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$json = compact('code', 'msg');
echo json_encode( $json );

?>