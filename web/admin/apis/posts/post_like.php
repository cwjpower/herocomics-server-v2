<?php
/*
 * Desc : API 담벼락 > 게시글 추천
 * 	method : POST
 */
require_once '../../wps-config.php';
require_once FUNC_PATH . '/functions-activity.php';

$code = 0;
$msg = '';

if ( empty($_POST['uid']) ) {
	$code = 400;
	$msg = '로그인 후 이용해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( empty($_POST['post_id']) ) {
	$code = 402;
	$msg = '책을 선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$activity_id = $_POST['post_id'];
$user_id = $_POST['uid'];

// 게시글 추천
$array = lps_recommand_activity( $activity_id, $user_id );

$result = $array['result'];
$count = $array['count'];

if ( $result > 0 ) {
	$code = 501;
	$msg = '해당 게시글을 이미 추천하셨습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$json = compact('code', 'msg', 'count');
echo json_encode( $json );
?>