<?php
/*
 * Desc : API 친구 요청 받은 리스트
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
			u.ID,
			u.user_name,
		    u.display_name,
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
			f.friend_user_id = ? AND
			f.is_confirmed = '0' AND
			u.ID = f.initiator_user_id
";
$stmt = $wdb->prepare( $query );
$stmt->bind_param( 'i', $user_id );
$stmt->execute();
$LIST = $wdb->get_results($stmt);

$json = compact( 'code', 'msg', 'LIST' );
echo json_encode( $json );

?>