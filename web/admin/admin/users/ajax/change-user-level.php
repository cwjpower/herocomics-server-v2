<?php
/*
 * 2016.08.08		softsyw
 * Desc : 회원등급 변경
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

if ( empty($_POST['user_list']) ) {
	$code = 410;
	$msg = '회원을 선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( empty($_POST['change_level']) ) {
	$code = 411;
	$msg = '변경하실 회원등급을  선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$result = lps_update_user_level();

$json = compact('code', 'msg', 'result');
echo json_encode( $json );
?>