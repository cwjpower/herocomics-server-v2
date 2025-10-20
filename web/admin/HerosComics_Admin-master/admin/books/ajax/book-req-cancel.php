<?php
/*
 * 2016.09.12		softsyw
 * Desc : 수정/삭제 요청 취소
 */
require_once '../../../wps-config.php';
require_once FUNC_PATH . '/functions-book.php';

$code = 0;
$msg = '';

if ( !wps_is_agent() ) {
	$code = 510;
	$msg = '관리자만 사용할 수 있습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( empty($_POST['id']) ) {
	$code = 401;
	$msg = '요청을 취소할 책을 선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$book_id = $_POST['id'];

$result = lps_req_cancel_book($book_id);

$json = compact('code', 'msg', 'result');
echo json_encode( $json );

?>