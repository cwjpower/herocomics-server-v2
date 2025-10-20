<?php
/*
 * Desc : API 리스트 > 내 소식 > 읽음 처리
 * 	method : GET
 */
require_once '../../wps-config.php';
require_once FUNC_PATH . '/functions-book.php';
require_once FUNC_PATH . '/functions-activity.php';

$code = 0;
$msg = '';

if ( empty($_GET['post_id']) ) {
	$code = 400;
	$msg = '게시글을 선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( empty($_GET['uid']) ) {
	$code = 404;
	$msg = '로그인 후 이용해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$activity_id = intval($_GET['post_id']);
$user_id = $_GET['uid'];

// for activity
$act_rows = lps_get_activity($activity_id);

if (empty($act_rows['id'])) {
	$code = 401;
	$msg = '게시글이 존재하지 않습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$is_deleted = $act_rows['is_deleted'];

if ( $is_deleted ) {
	$code = 501;
	$msg = '삭제된 게시글입니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$writer_uid = $act_rows['user_id'];

if ($user_id == $writer_uid) {	// 본인의 게시글을 조회 시엔 내 소식 개수를 위한 댓글 정보를 업데이트한다.
	lps_update_activity_comment_read($activity_id);		// 게시글에 딸린 댓글 읽음 처리
}

$json = compact( 'code', 'msg');
echo json_encode( $json );

?>