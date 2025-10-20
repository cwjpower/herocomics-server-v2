<?php
/*
 * 2016.08.11		softsyw
 * Desc : 일반 설정 정보 저장
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

foreach ($_POST as $key => $val) {
	wps_update_option_value($key, $val);
}

$json = compact('code', 'msg');
echo json_encode( $json );
?>