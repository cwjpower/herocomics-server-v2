<?php
/*
 * Desc : API 담벼락 > 공지사항 보기
 * 	method : GET
 */
require_once '../../wps-config.php';

$code = 0;
$msg = '';

if ( empty($_GET['post_id']) ) {
	$code = 400;
	$msg = '공지사항을 선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$post_id = intval($_GET['post_id']);

// for post : notice_new
$post_rows = wps_get_post($post_id);

if (empty($post_rows['ID'])) {
	$code = 401;
	$msg = '공지글이 존재하지 않습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$title = htmlspecialchars($post_rows['post_title']);
$content = strip_tags($post_rows['post_content']);
$writer_uid = $post_rows['post_user_id'];
$created_dt = $post_rows['post_date'];
$count_hit = lps_update_post_view_count($post_id);

$user_rows = wps_get_user( $writer_uid );
$writer_name = $user_rows['user_name'];

// for activity meta
$ATTACHMENT = '';
$act_attach = wps_get_post_meta($post_id, 'wps-post-attachment');
if (!empty($act_attach)) {
	$ATTACHMENT = unserialize($act_attach);
}

$json = compact( 'code', 'msg', 'title', 'content', 'writer_uid', 
				'writer_name', 'created_dt', 'count_hit', 'ATTACHMENT'
		);

echo json_encode( $json );

?>