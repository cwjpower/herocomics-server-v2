<?php
/*
 * 2016.10.13	softsyw
 * Desc : 출판사 입점도서 메인에서 삭제
 */
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
	$msg = '출판사 입점도서에서 제외할 출판사를 선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$result = lps_delete_publisher();

$json = compact('code', 'msg', 'result');
echo json_encode( $json );

?>