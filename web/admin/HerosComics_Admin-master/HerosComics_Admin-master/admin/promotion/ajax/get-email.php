<?php
/*
 * 2017.1.20		softsyw
 * Desc : EMAIL 가져오기
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

if ( empty($_POST['id']) ) {
	$code = 401;
	$msg = '이메일을 선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$id = $_POST['id'];

$prom_row = lps_get_promotion_by_id( $id );

$title = $prom_row['prom_title'];
$content = $prom_row['prom_content'];

$json = compact('code', 'msg', 'title', 'content');
echo json_encode( $json );

?>