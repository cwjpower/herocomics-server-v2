<?php
/*
 * 2016.08.25		softsyw
 * Desc : 출판사 > 책 삭제 요청 
 */
require_once '../../../wps-config.php';
require_once FUNC_PATH . '/functions-book.php';

$code = 0;
$msg = '';

if ( !wps_is_agent() ) {
	$code = 510;
	$msg = '사용권한이 없습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( empty($_POST['book_id']) ) {
	$code = 400;
	$msg = '책을 선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}
if ( empty($_POST['req_reason']) ) {
	$code = 401;
	$msg = '삭제 요청 사유를 입력해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$result = lps_req_delete_book();

$json = compact('code', 'msg', 'result');
echo json_encode( $json );

?>