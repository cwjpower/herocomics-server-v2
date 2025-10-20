<?php
/*
 * Desc : API 차단된 회원 해제하기
 * 	method : POST
 */
require_once '../../wps-config.php';

$code = 0;
$msg = '';

if ( empty($_POST['uid']) ) {
	$code = 401;
	$msg = '회원의 UID가 필요합니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( empty($_POST['lock_ids']) ) {
	$code = 411;
	$msg = '차단을 해제할 회원을 선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$user_id = $_POST['uid'];
$lock_ids = $_POST['lock_ids'];

$query = "
		DELETE FROM
			bt_banned_users
		WHERE
			user_id = ? AND
			banned_user_id IN ($lock_ids)
";
$stmt = $wdb->prepare( $query );
$stmt->bind_param( 'i', $user_id );
$stmt->execute();
$count = $wdb->affected_rows;

$json = compact( 'code', 'msg', 'count' );
echo json_encode( $json );

?>