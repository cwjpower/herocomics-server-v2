<?php
/*
 * Desc : API Sendbird 채팅 URL 추가
 * 	method : POST
 */
require_once '../../wps-config.php';
require_once FUNC_PATH . '/functions-book.php';

$code = 0;
$msg = '';

if ( empty($_POST['book_id']) ) {
	$code = 401;
	$msg = '책 ID가 필요합니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( empty($_POST['chat_url']) ) {
	$code = 402;
	$msg = '자유대화방 URL이 필요합니다';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$chat_url = $_POST['chat_url'];
$book_id = $_POST['book_id'];

$result = lps_update_book_meta($book_id, 'lps_sendbird_chat_url', $chat_url);

if (empty($result)) {
	$code = 501;
	$msg = '자유대화방 URL을 등록하지 못했습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$json = compact( 'code', 'msg' );
echo json_encode( $json );

?>