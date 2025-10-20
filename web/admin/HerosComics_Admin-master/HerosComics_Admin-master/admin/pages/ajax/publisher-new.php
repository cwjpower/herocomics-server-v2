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
if ( empty($_POST['publisher']) ) {
	$code = 401;
	$msg = '출판사를  선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( empty($_POST['period_from']) ) {
	$code = 410;
	$msg = '등록기간을 입력해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( empty($_POST['period_to']) ) {
	$code = 412;
	$msg = '등록기간을 등록해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$result = lps_add_publisher();

if ( empty($result) ) {
	$code = 501;
	$msg = '등록하지 못했습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$json = compact('code', 'msg', 'result');
echo json_encode( $json );

?>