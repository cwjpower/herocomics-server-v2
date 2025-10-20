<?php
/*
 * 2016.08.25		softsyw
 * Desc : 관리자 > 책 등록요청에 대한 승인
 */
require_once '../../../wps-config.php';
require_once FUNC_PATH . '/functions-book.php';

$code = 0;
$msg = '';

if ( !wps_is_admin() ) {
	$code = 510;
	$msg = '사용권한이 없습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( empty($_POST['id']) ) {
	$code = 401;
	$msg = '책 아이디를 입력해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}
if ( empty($_POST['type']) ) {
	$code = 402;
	$msg = '처리방법을 선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$type = $_POST['type'];
if (stripos($type, 'reject_') !== false ) {		// 거절
	if ( empty($_POST['res_reason']) ) {
		$code = 403;
		$msg = '거절 서유를 입력해 주십시오.';
		$json = compact('code', 'msg');
		exit( json_encode($json) );
	}
}

$result = lps_edit_book_status();

$json = compact('code', 'msg', 'result');
echo json_encode( $json );

?>