<?php
/*
 * Desc : API 내 서재
 * 	method : GET
 * 		default는 다운로드 순이다. 
 */
require_once '../../wps-config.php';
require_once FUNC_PATH . '/functions-book.php';
require_once INC_PATH . '/classes/WpsPaginator.php';

$code = 0;
$msg = '';

if ( empty($_GET['uid']) ) {
	$code = 510;
	$msg = '회원의 UID가 필요합니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$user_id = $_GET['uid'];

// page number
$page = empty($_GET['page']) ? 1 : $_GET['page'];
$sparam = [];
$ob = 'bt';

// search
$q = !isset($_GET['q']) ? '' : trim($_GET['q']);
$sql = '';
$pph = '';
$sparam = [];

// Simple search
if ( !empty($q) ) {
	$sql = " AND b.book_title LIKE ? ";
	array_push( $sparam, '%' . $q . '%' );
}
// Positional placeholder ?
if ( !empty($sql) ) {
	$pph_count = substr_count($sql, '?');
	for ( $i = 0; $i < $pph_count; $i++ ) {
		$pph .= 's';
	}
}

if (!empty($pph)) {
	array_unshift($sparam, $pph);
}

// 정렬순서
if (empty($_GET['ob'])) {
	$orderby = 'i.book_down_dt DESC';	// 다운로드 최근 순
// 	$orderby = 'b.book_title ASC';	// 책 제목
} else {
	$ob = $_GET['ob'];
// 	if (!strcmp($ob, 'lb')) {	// 구매일
// 		$orderby = 'o.created_dt DESC';
// 	} else if (!strcmp($ob, 'gr')) {	// 장르별
// 		$orderby = 'o.created_dt DESC';
// 	} else if (!strcmp($ob, 'lv')) {	// 최근 읽은
// 		$orderby = 'o.created_dt DESC';
// 	} else {	// bt 제목
// 		$orderby = 'b.book_title ASC';
// 	}
}

$query = "
	SELECT
		b.ID,
		b.book_title,
		b.author,
		b.publisher,
		b.cover_img,
		i.epub_url,
		m.meta_value AS epub,
	    m2.meta_value AS total_page,
	    m3.meta_value AS chat_url
	FROM
		bt_order AS o
	INNER JOIN
		bt_order_item AS i
	INNER JOIN
		bt_books AS b
	LEFT JOIN
		bt_books_meta AS m
	ON
		b.ID = m.book_id AND
		m.meta_key = 'lps_book_epub_file'
	LEFT JOIN
		bt_books_meta AS m2
	ON
		b.ID = m2.book_id AND
		m2.meta_key = 'lps_book_total_page'
	LEFT JOIN
		bt_books_meta AS m3
	ON
		b.ID = m3.book_id AND
		m3.meta_key = 'lps_sendbird_chat_url'
	WHERE
		o.user_id = '$user_id' AND
		o.order_id = i.order_id AND
		i.book_id = b.ID
		$sql
	ORDER BY
		$orderby
";
$paginator = new WpsPaginator($wdb, $page, 1000);
$rows = $paginator->ls_init_pagination( $query, $sparam );
// $total_count = $paginator->ls_get_total_rows();
// $total_records = $paginator->ls_get_total_records();

if (!empty($rows)) {
	
	// 책 알림 OFF book ids
	$meta_key = 'lps_activity_alarm_off';
	$alarm_off = wps_get_user_meta($user_id, $meta_key);
	$off_books = unserialize($alarm_off);
	if ( empty($off_books) ) {
		$off_books = [];
	}
	
	foreach ($rows as $key => $val) {
		$book_id = $val['ID'];
		$epub = unserialize($val['epub']);
		$epub_url = $epub['file_url'];
		$epub_name = $epub['file_name'];
		
		$br_row = lps_get_read_book_data_by_user( $user_id, $book_id );
		
		if (empty($br_row)) {
			$rows[$key]['read_page_to'] = "0";
			$rows[$key]['epub_index'] = "0";
		} else {
			$rows[$key]['read_page_to'] = "". $br_row['read_page_to'] . "";
			$rows[$key]['epub_index'] = "" . $br_row['epub_index'] . "";
		}
		
		if ( in_array($book_id, $off_books) ) {
			$rows[$key]['alarm_status'] = 'OFF';
		} else {
			$rows[$key]['alarm_status'] = 'ON';
		}
		
		$rows[$key]['epub_url'] = $epub['file_url'];
		$rows[$key]['epub_name'] = $epub['file_name'];
		unset($rows[$key]['epub']);
	}
}

$LIST = $rows;

$json = compact( 'code', 'msg', 'LIST' );
echo json_encode( $json );

?>