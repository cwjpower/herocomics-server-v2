<?php
/*
 * Desc : 프로필 보기 : 회원의 모든 정보
 * 	method : GET
 */
require_once '../../wps-config.php';
require_once FUNC_PATH . '/functions-friend.php';

$code = 0;
$msg = '';

if ( empty($_GET['uid']) ) {
	$code = 401;
	$msg = '회원의 UID가 필요합니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}
if ( empty($_GET['profile_uid']) ) {
	$code = 402;
	$msg = '프로필 회원의 UID가 필요합니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$uid = $_GET['uid'];
$profile_uid = $_GET['profile_uid'];
// $user_account = $_GET['user_login'];

$user_rows = wps_get_user_by( 'ID', $profile_uid );

$user_login = $user_rows['user_login'];
$user_name = $user_rows['user_name'];
$display_name = $user_rows['display_name'];
$created_dt = $user_rows['user_registered'];
$user_status = $user_rows['user_status'];
$user_level = $user_rows['user_level'];
$mobile = $user_rows['mobile'];
$birthday = $user_rows['birthday'];
$gender = $user_rows['gender'];
$residence = $user_rows['residence'];
$last_school = $user_rows['last_school'];
// $join_path = $user_rows['join_path'];
$last_login_dt = $user_rows['last_login_dt'];

// if ( $user_account != $user_login ) {
// 	$code = 501;
// 	$msg = '계정 정보가 정확하지 않습니다.';
// 	$json = compact('code', 'msg');
// 	exit( json_encode($json) );
// }

if ( $user_rows['user_status'] == '1' ) {
	$code = 503;
	$msg = '차단된 회원입니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( $user_rows['user_status'] == '4' ) {
	$code = 504;
	$msg = '탈퇴하신 회원입니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$umeta = wps_get_user_meta( $profile_uid );
$profile_avatar = @$umeta['wps_user_avatar'];
$profile_bg = @$umeta['wps_user_profile_bg'];
$profile_msg = @$umeta['wps_user_profile_msg'];

// last_login_dt update
lps_update_user_login_dt($uid);

// 친구 여부
$friend_id = lps_is_accepted_friend($uid, $profile_uid);

if (empty($friend_id)) {
	$is_friend = 'N';
} else {
	$is_friend = 'Y';
}

// 차단 여부
$blocked_id = lps_is_blocked_friend($uid, $profile_uid);
if (empty($blocked_id)) {
	$is_blocked = 'N';
} else {
	$is_blocked = 'Y';
}

$json = compact( 'code', 'msg', 'user_login', 'user_name', 'display_name', 'user_level', 
				'created_dt', 'mobile', 'birthday', 'gender', 
				'residence', 'last_school', 'last_login_dt', 'profile_avatar', 'profile_bg', 
				'is_friend', 'is_blocked', 'profile_msg' );
echo json_encode( $json );

?>