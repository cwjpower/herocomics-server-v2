<?php
/*
 * Desc : API 책 알림 켜기/끄기
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

if ( empty($_POST['book_id']) ) {
	$code = 411;
	$msg = '책을 선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( empty($_POST['action']) ) {
	$code = 412;
	$msg = '책 알림에 대한 상태를 선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$user_id = $_POST['uid'];
$book_id = $_POST['book_id'];
$action = $_POST['action'];	// ON / OFF

$meta_key = 'lps_activity_alarm_off';
$meta_value = wps_get_user_meta($user_id, $meta_key);

$off_books = unserialize($meta_value);

if (!strcmp($action, 'OFF')) {		// meta_value 에 추가
	if (empty($off_books)) {
		$off_books = array($book_id);
	} else {
		array_push($off_books, $book_id);
	}
} else if (!strcmp($action, 'ON')) {	// meta_value 에서 제외
	foreach ($off_books as $key => $val) {
		if ($val == $book_id) {
			unset($off_books[$key]);
		}
	}
} else {
	$code = 414;
	$msg = '상태값이 잘못되었습니다. ON 혹은 OFF만 사용할 수 있습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$serialized = serialize($off_books);
$result = wps_update_user_meta( $user_id, $meta_key, $serialized );

if (empty($result)) {
	$code = 412;
	$msg = '알림 OFF를 처리할 수 없습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$json = compact( 'code', 'msg' );
echo json_encode( $json );

?>