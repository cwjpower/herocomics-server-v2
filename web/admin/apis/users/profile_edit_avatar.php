<?php
/*
 * Desc : 프로필 사진 변경
 */
require_once '../../wps-config.php';
require_once INC_PATH . '/classes/WpsThumbnail.php';

$code = 0;
$msg = '';

if ( empty($_POST['uid']) ) {
	$code = 510;
	$msg = '회원 아이디가 필요합니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( empty($_FILES['profile_avatar']['tmp_name']) ) {
	$code = 602;
	$msg = '프로필 이미지 파일을 선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( stripos($_FILES['profile_avatar']['type'], 'image') === false ) {
	$code = 603;
	$msg = '이미지 파일이 아닙니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$uid = $_POST['uid'];

$user_data = wps_get_user_by( 'ID', $uid );

if ( $user_data['user_status'] == '1' ) {
	$code = 503;
	$msg = '차단된 회원입니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( $user_data['user_status'] == '4' ) {
	$code = 504;
	$msg = '탈퇴하신 회원입니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

// 파일 업로드
$upload_dir = UPLOAD_PATH . '/tmp';
if ( !is_dir($upload_dir) ) {
	mkdir($upload_dir, 0777, true);
}

$img_files = array( 'jpg', 'jpeg', 'gif', 'png' );
$able_thumb = 1;

$wps_thumbnail = new WpsThumbnail();

$file_ext = strtolower(pathinfo( $_FILES['profile_avatar']['name'], PATHINFO_EXTENSION ));

if ( in_array($file_ext, $img_files) ) {
	$new_file_name = wps_make_rand() . '.' . $file_ext;
	$able_thumb = 1;
} else {
	$code = 403;
	$msg = '프로필 사진으로 사용할 수 없는 파일 포멧입니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$upload_path = $upload_dir . '/' . $new_file_name;
$result = move_uploaded_file( $_FILES['profile_avatar']['tmp_name'], $upload_path );

if ( $result ) {
	$ym = date('Ym');

	// 	$file_url = UPLOAD_URL . '/tmp/' . $new_file_name;
	$file_path = UPLOAD_PATH . '/tmp/' . $new_file_name;
	// 	$file_name = $_FILES['profile_avatar']['name'];

	// Thumbnail
	if (empty($_POST['thumbW'])) {
		$thumb_suffix = '-200x200';
		$thumb_width = 200;
		$thumb_height = 200;
	} else {
		$tsw = $_POST['thumbW'];
		$tsh = $_POST['thumbH'];
		$thumb_suffix = '-' . $tsw . 'x' . $tsh;
		$thumb_width = $tsw;
		$thumb_height = $tsh;
	}

	$thumb_name = $wps_thumbnail->resize_image( $file_path, $thumb_suffix, $thumb_width, $thumb_height );
	$thumb_path = UPLOAD_PATH . '/tmp/' . $thumb_name;

	$user_profile_path = UPLOAD_PATH . '/user_avatar/' . $ym . '/' . $thumb_name;
	$user_profile_avatar = UPLOAD_URL . '/user_avatar/' . $ym . '/' . $thumb_name;

	rename($thumb_path, $user_profile_path);

	unlink($file_path);

	wps_update_user_meta($uid, 'wps_user_avatar', $user_profile_avatar);

} else {
	$code = 505;
	$msg = '파일을 업로드할 수 없습니다. 관리자에게 문의해 주십시오.';
}

$json = compact( 'code', 'msg', 'user_profile_avatar' );
echo json_encode( $json );

?>