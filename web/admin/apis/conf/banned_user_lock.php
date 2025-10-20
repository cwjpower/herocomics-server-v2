<?php
/*
 * Desc : API 회원 차단하기
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

if ( empty($_POST['lock_uid']) ) {
	$code = 411;
	$msg = '차단을 회원을 선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$user_id = $_POST['uid'];
$lock_uid = $_POST['lock_uid'];

$query = "
		INSERT INTO
			bt_banned_users
			(
				ID,
				user_id,
				banned_user_id
			)
		VALUES
			(
				NULL, ?, ?
			)
";
$stmt = $wdb->prepare( $query );
$stmt->bind_param( 'ii', $user_id, $lock_uid );
$stmt->execute();

$json = compact( 'code', 'msg' );
echo json_encode( $json );

?>