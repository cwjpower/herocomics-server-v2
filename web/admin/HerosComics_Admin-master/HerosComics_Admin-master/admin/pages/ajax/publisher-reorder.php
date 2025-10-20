<?php
require_once '../../../wps-config.php';
require_once FUNC_PATH . '/functions-page.php';

$code = 0;
$msg = '';

if ( !wps_is_admin() ) {
	$code = 510;
	$msg = '관리자만 사용할 수 있습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( empty($_POST['pb_uid']) ) {
	$code = 410;
	$msg = '출판사를 선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$result = lps_reorder_publisher();

if ( empty($result) ) {
	$code = 501;
	$msg = '정렬하지 못했습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$json = compact('code', 'msg', 'result');
echo json_encode( $json );

?>