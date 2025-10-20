<?php
/*
 * Desc : API 친구 리스트, 내가 요청해서 상대방이 수락한 경우와 내가 요청받고 수락한 경우
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

// 친구 리스트
$query = "
		SELECT
			u.ID,
			u.user_name,
		    u.display_name,
			f.is_favorite,
			m.meta_value AS profile_avatar
		FROM
			bt_friends AS f
		INNER JOIN
			bt_users AS u
		LEFT JOIN
			bt_users_meta AS m
		ON
			u.ID = m.user_id AND
			m.meta_key = 'wps_user_avatar'
		WHERE
			f.initiator_user_id = ? AND
			f.is_confirmed = '1' AND
			u.ID = f.friend_user_id
			OR
			f.friend_user_id = ? AND
			f.is_confirmed = '1' AND
			u.ID = f.initiator_user_id 
";
$stmt = $wdb->prepare( $query );
$stmt->bind_param( 'ii', $user_id, $user_id );
$stmt->execute();
$friends = $wdb->get_results($stmt);

// 차단 회원 리스트
$banned = [];
$query = "
		SELECT
			u.ID
		FROM
			bt_users AS u
		LEFT JOIN
			bt_banned_users AS b
		ON
			u.ID = b.banned_user_id
		WHERE
			b.user_id = ?
";
$stmt = $wdb->prepare( $query );
$stmt->bind_param( 'i', $user_id );
$stmt->execute();
$banned = $wdb->get_results($stmt);

if (!empty($friends)) {
	foreach ($friends as $key => $val) {
		$uid = $val['ID'];
		foreach ($banned as $k => $v) {
			$buid = $v['ID'];
			if ( $uid == $buid ) {
				unset($friends[$key]);
			}
		}
	}
}

$LIST = array_values($friends);

$json = compact( 'code', 'msg', 'LIST' );
echo json_encode( $json );

?>