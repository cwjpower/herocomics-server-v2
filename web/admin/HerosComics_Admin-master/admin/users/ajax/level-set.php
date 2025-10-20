<?php
/*
 * 2016.08.10		softsyw
 * Desc : 등급권한 설정
 */
require_once '../../../wps-config.php';
require_once FUNC_PATH . '/functions-term.php';

$code = 0;
$msg = '';

if ( !wps_is_admin() ) {
	$code = 510;
	$msg = '관리자만 사용할 수 있습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( empty($_POST['level']) ) {
	$code = 410;
	$msg = '등급을 선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$result = lps_edit_user_level_terms();

$json = compact('code', 'msg', 'result');
echo json_encode( $json );
?>