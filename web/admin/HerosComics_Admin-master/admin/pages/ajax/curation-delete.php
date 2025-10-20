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

if ( empty($_POST['id']) ) {
	$code = 400;
	$msg = '큐레이팅이 선택되지 않았습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$curation_id = intval($_POST['id']);

$result = lps_delete_curation($curation_id);

$json = compact('code', 'msg', 'result');
echo json_encode( $json );

?>
