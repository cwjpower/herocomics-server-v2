<?php
/*
 * Desc : API 내 소식, 담벼락에 새로운 댓글이 등록된 게시글
 * 	method : GET
 */
require_once '../../wps-config.php';

$code = 0;
$msg = '';

if ( empty($_GET['uid']) ) {
	$code = 401;
	$msg = '회원의 UID가 필요합니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$user_id = $_GET['uid'];

$query = "
		SELECT
			a.id AS ID,
		    a.subject,
		    a.created_dt,
			COUNT(*) AS unread,
		    b.cover_img,
			'comment' AS alarm_type,
			'게시글의 추천수가 10개가 되었습니다' AS alarm_msg
		FROM
			bt_activity AS a
		LEFT JOIN
			bt_activity_comment AS c
		ON
			a.id = c.activity_id
		LEFT JOIN
			bt_books AS b
		ON
			a.book_id = b.ID
		WHERE
			a.user_id = ? AND
			c.comment_read = 0
		GROUP BY
			a.id
		
		UNION
		
		SELECT 
			a.id AS ID,
		    a.subject,
		    a.created_dt,
			'1' AS unread,
		    b.cover_img,
			'like' AS alarm_type,
			'게시글의 추천수가 10개가 되었습니다' AS alarm_msg
		FROM
			bt_activity AS a
		LEFT JOIN
			bt_books AS b
		ON
			a.book_id = b.ID
		WHERE
			a.user_id = ? AND
		    a.count_like > 9 AND
		    a.hide_sitewide = '0'
		
";
$stmt = $wdb->prepare( $query );
$stmt->bind_param( 'ii', $user_id, $user_id );
$stmt->execute();
$LIST = $wdb->get_results($stmt);

$json = compact( 'code', 'msg', 'LIST' );
echo json_encode( $json );

?>