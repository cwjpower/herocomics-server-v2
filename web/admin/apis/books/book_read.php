<?php
/*
 * Desc : API 회원의 독서 정보 업데이트
 * 	method : POST
 */
require_once '../../wps-config.php';
require_once FUNC_PATH . '/functions-book.php';

$code = 0;
$msg = '';

if ( empty($_POST['uid']) ) {
	$code = 401;
	$msg = '회원의 UID가 필요합니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( empty($_POST['book_id']) ) {
	$code = 402;
	$msg = '책 UID가 필요합니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( empty($_POST['total_page']) ) {
	$code = 403;
	$msg = '총 페이지 수가 필요합니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( empty($_POST['last_page']) ) {
	$code = 403;
	$msg = '마지막 읽은 페이지 번호가 필요합니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$user_id = $_POST['uid'];
$book_id = $_POST['book_id'];
$total_page = $_POST['total_page'];
$last_page = $_POST['last_page'];
$epub_index = empty($_POST['epub_index']) ? '' : $_POST['epub_index'];

lps_update_book_meta($book_id, 'lps_book_total_page', $total_page);		// Total Page 업데이트

// 해당 책을 마지막으로 읽은 시각을 찾는다. 1시간이 지났으면 새로 등록, 지나지 않았으면 마지막 최종 읽은 시간과 페이지를 업데이트
$last_dt = lps_get_latest_read_dt($book_id, $user_id);
$check = lps_is_one_hour_ago( $last_dt );

if ($check) {	// 마지막 독서 시각이 1시간 지났다면 
	$read_dt_from = date('Y-m-d H:i:s');
	$read_dt_to = $read_dt_from;
	
	$query = "
			INSERT INTO
				bt_user_book_read
				(
					ID,
					user_id,
					book_id,
					read_dt_from,
					read_dt_to,
					read_page_to,
					epub_index
				)
			VALUES
				(
					NULL, ?, ?, ?, ?,
					?, ?
				)
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'iissis', $user_id, $book_id, $read_dt_from, $read_dt_to, $last_page, $epub_index );
	$stmt->execute();
// 	$read_id = $wdb->insert_id;
} else {	// 업데이트
	$query = "
			UPDATE
				bt_user_book_read
			SET
				read_page_to = ?,
				read_dt_to = NOW(),
				epub_index = ?
			WHERE
				user_id = ? AND
				book_id = ? AND
				read_dt_to = ?
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'isiis', $last_page, $epub_index, $user_id, $book_id, $last_dt );
	$stmt->execute();
}

$json = compact( 'code', 'msg' );
echo json_encode( $json );

?>