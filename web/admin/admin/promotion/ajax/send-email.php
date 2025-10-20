<?php
/*
 * 2017.1.20		softsyw
 * Desc : EMAIL 등록
 */
require_once '../../../wps-config.php';
require_once FUNC_PATH . '/functions-promotion.php';

$code = 0;
$msg = '';

if ( !wps_is_admin() ) {
	$code = 510;
	$msg = '관리자만 사용할 수 있습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( empty($_POST['prom_type']) ) {
	$code = 401;
	$msg = '프로모션 종류를 선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}
if ( empty($_POST['prom_title']) ) {
	$code = 402;
	$msg = '제목을 입력해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}
if ( empty($_POST['message']) ) {
	$code = 403;
	$msg = '내용을 입력해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}
if ( empty($_POST['user_list']) ) {
	$code = 404;
	$msg = '받는사람 이메일을 등록해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$_POST['prom_content'] = $_POST['message'];

// $data_rows = explode( "\n", $_POST['user_list'] );
// $user_count = 0;

// foreach ( $data_rows as $key => $val ) {
// 	$mobile = lps_sanitize_mobile_number( $val );
// 	if ( lps_check_mobile_number($mobile) ) {
// 		$user_count++;
// 	}
// }

$_POST['user_count'] = 5;

$result = lps_add_promotion();

if (empty($result)) {
	$code = 503;
	$msg = 'SMS를 등록하지 못했습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$json = compact('code', 'msg', 'result');
echo json_encode( $json );

?>