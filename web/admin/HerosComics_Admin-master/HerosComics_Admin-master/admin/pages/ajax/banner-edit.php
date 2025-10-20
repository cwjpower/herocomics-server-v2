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

if ( !isset($_POST['bkey']) ) {
	$code = 400;
	$msg = '배너가 선택되지 않았습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( empty($_POST['banner_title']) ) {
	$code = 410;
	$msg = '배너 제목을 입력해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$result = lps_update_banner();

if ( empty($result) ) {
	$code = 501;
	$msg = '등록하지 못했습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$json = compact('code', 'msg');
echo json_encode( $json );

?>