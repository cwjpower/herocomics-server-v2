<?php
/*
 * Desc : 닉네임 중복 체크
 */
require_once '../../wps-config.php';

$code = 0;
$msg = '';

if ( empty($_GET['display_name']) ) {
	$code = 404;
	$msg = '닉네임을 입력해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$display_name = $_GET['display_name'];
$pattern = '/[^a-zA-Z0-9가-히]/';

if (preg_match($pattern, $display_name)) {
	$code = 404;
	$msg = '닉네임에는 특수문자를 사용할 수 없습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if (lps_check_display_name($display_name)) {
	$code = 413;
	$msg = '이미 사용중인 닉네임입니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$json = compact('code', 'msg');
echo json_encode( $json );

?>