<?php
/*
 * Desc : API 233 : 회원이 구매한 책 리스트
 * 	method : GET
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
// 	$orderby = 'i.book_down_dt DESC';	// 다운로드 최근 순
	$orderby = 'b.book_title ASC';	// 책 제목
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
		b.cover_img,
		b.publisher
	FROM
		bt_order AS o
	INNER JOIN
		bt_order_item AS i
	INNER JOIN
		bt_books AS b
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
	foreach ($rows as $key => $val) {
		$book_id = $val['ID'];
		$rows[$key]['web_url'] = 'http://booktalk.world/mobile/book/book.php?id=' . $book_id;
	}
}

$LIST = $rows;

$json = compact( 'code', 'msg', 'LIST' );
echo json_encode( $json );

?>