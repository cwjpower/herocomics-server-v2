<?php
/*
 * Desc : 인증 SMS 비교
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

if ( empty($_GET['authcode']) ) {
	$code = 402;
	$msg = '인증번호를 입력해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$mobile = preg_replace( '/\D/', '', $_GET['mobile'] );
$authcode = $_GET['authcode'];

$result = lps_check_mobile_auth($mobile, $authcode);

if (empty($result)) {
	$code = 412;
	$msg = '인증번호를 잘못 입력하셨습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$json = compact('code', 'msg');
echo json_encode( $json );

?>