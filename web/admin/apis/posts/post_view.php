<?php
/*
 * Desc : API 담벼락 > 게시글 보기
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

$activity_id = intval($_GET['post_id']);
$user_id = empty($_GET['uid']) ? 0 : $_GET['uid'];

lps_update_activity_view_count($activity_id);	// update view count

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

$book_id = $act_rows['book_id'];
$title = htmlspecialchars($act_rows['subject']);
$content = strip_tags($act_rows['content']);
$writer_uid = $act_rows['user_id'];
$created_dt = $act_rows['created_dt'];
$count_hit = $act_rows['count_hit'];
$count_like = $act_rows['count_like'];

if ($user_id == $writer_uid) {	// 본인의 게시글을 조회 시엔 내 소식 개수를 위한 댓글 정보를 업데이트한다.
	lps_update_activity_comment_read($activity_id);		// 게시글에 딸린 댓글 읽음 처리
	lps_update_fav_above_10($activity_id);					// 10개 이상 추천한 게시글 읽음 처리
}

$user_rows = wps_get_user( $writer_uid );
$writer_name = $user_rows['display_name'];

// for activity meta
$ATTACHMENT = [];
$act_attach = lps_get_activity_meta($activity_id, 'wps-community-attachment');
if (!empty($act_attach)) {
	$ATTACHMENT = unserialize($act_attach);
}

// for comment of activity
// $LIST =  lps_get_activity_comments( $activity_id );

$query = "
		SELECT
			c.comment_id,
			c.comment_user_id AS comment_author_id,
			c.comment_author,
			c.comment_date,
			c.comment_content,
			IF( c.comment_user_id = b.banned_user_id, 1, 0) AS is_blocked
		FROM
			bt_activity_comment AS c
		LEFT JOIN
			bt_banned_users AS b
		ON
			c.comment_user_id = b.banned_user_id AND
    		b.user_id = '$user_id'
		WHERE
			c.activity_id = ?
		ORDER BY
			c.comment_id ASC
";
$stmt = $wdb->prepare( $query );
$stmt->bind_param( 'i', $activity_id );
$stmt->execute();
$LIST = $wdb->get_results($stmt);

// if (empty($LIST)) {
// 	$LIST = '';
// }

$json = compact( 'code', 'msg', 'title', 'content', 'writer_uid', 
				'writer_name', 'created_dt', 'count_hit', 'count_like', 'ATTACHMENT',
				'LIST'
		);

echo json_encode( $json );

?>