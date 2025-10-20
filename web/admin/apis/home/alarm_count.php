<?php
/*
 * Desc : API 내 소식 개수
 * 		내가 작성한 담벼락에 남긴 댓글 수, 안 읽은 댓글 개수. +
 * 		내가 작성한 게시글에 추천인 수가 10개 이상일 때
 * 	method : GET
 */
require_once '../../wps-config.php';
require_once FUNC_PATH . '/functions-activity.php';

$code = 0;
$msg = '';

if ( empty($_GET['uid']) ) {
	$code = 401;
	$msg = '회원의 UID가 필요합니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$user_id = $_GET['uid'];

$count1 = lps_get_unread_activity_comment($user_id);
$count2 = lps_get_fav_above_10($user_id);

$count = intval($count1) + intval($count2);

$json = compact( 'code', 'msg', 'count' );
echo json_encode( $json );

?>