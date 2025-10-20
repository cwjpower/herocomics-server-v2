<?php
/*
 * 2016.10.03		softsyw
 * Desc : 담벼락 게시글 미리보기
 */
require_once '../../../wps-config.php';
require_once FUNC_PATH . '/functions-activity.php';

$code = 0;
$msg = '';

if ( !wps_is_agent() ) {
	$code = 510;
	$msg = '관리자만 사용할 수 있습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( empty($_POST['id']) ) {
	$code = 410;
	$msg = '게시글을 선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$activity_id = $_POST['id'];

$activity = lps_get_activity($activity_id);

// $book_id = $activity['book_id'];
// $act_title = $activity['subject'];
// $act_content = $activity['content'];
$act_userid = $activity['user_id'];
// $created_dt = date('Y.m.d', strtotime($activity['created_dt']));
// $count_hit = $activity['count_hit'];
// $count_like = $activity['count_like'];

$user_rows = wps_get_user( $act_userid );
$user_name = $user_rows['user_name'];

// for activity meta
$attachment = '';
$act_attach = lps_get_activity_meta($activity_id, 'wps-community-attachment');
if (!empty($act_attach)) {
	$unserial = unserialize($act_attach);
	if (!empty($unserial[0])) {
		foreach ($unserial as $key => $val) {
			$attachment .= '<p><a href="' . INC_URL . '/lib/download-community-attachment.php?aid=' . $activity_id . '&key=' . $key . '" data-ajax="false">' . $val['file_name'] . '</a></p>';
		}
	}
}
$json = compact('code', 'msg', 'activity', 'user_name', 'attachment');
echo json_encode( $json );

?>