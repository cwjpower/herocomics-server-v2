<?php
/*
 * Desc : 인증 SMS 요청
 */
require_once '../../wps-config.php';

$code = 0;
$msg = '';

if ( empty($_GET['mobile']) ) {
	$code = 404;
	$msg = '휴대전화번호를 입력해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$mobile = preg_replace( '/\D/', '', $_GET['mobile'] );

$strlen = strlen($mobile);

if ($strlen > 11 || $strlen < 10) {
	$code = 402;
	$msg = '휴대전화번호 자릿수를 확인해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$authcode = mt_rand(1500, 8500);

$result = lps_add_mobile_auth( $mobile, $authcode );

if (empty($result)) {
	$code = 412;
	$msg = '인증번호를 생성할 수 없습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$json = compact('code', 'msg', 'authcode');
echo json_encode( $json );

?>