<?php
/*
 * Desc : API 내가 차단한 유저들
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
		    -- u.user_login,
			u.user_name,
		    u.display_name,
			m.meta_value AS profile_avatar
		FROM
			bt_users AS u
		LEFT JOIN
			bt_banned_users AS b
		ON
			u.ID = b.banned_user_id
		LEFT JOIN
			bt_users_meta AS m
		ON
			u.ID = m.user_id AND
			m.meta_key = 'wps_user_avatar'
		WHERE
			b.user_id = ?
";
$stmt = $wdb->prepare( $query );
$stmt->bind_param( 'i', $user_id );
$stmt->execute();
$LIST = $wdb->get_results($stmt);

$json = compact( 'code', 'msg', 'LIST' );
echo json_encode( $json );

?>