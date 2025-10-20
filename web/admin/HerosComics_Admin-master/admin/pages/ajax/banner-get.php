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

if ( !isset($_POST['id']) ) {
	$code = 400;
	$msg = '배너가 선택되지 않았습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$banner_id = $_POST['id'];

$banner = lps_get_banner_by_id($banner_id);

$json = compact('code', 'msg', 'banner');
echo json_encode( $json );

?>