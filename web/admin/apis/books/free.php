<?php
/*
 * Desc : API 무료 책
 * 	method : GET
 * 
 * 	2017.1.3	epub_url을 private 으로 변경하면서 order_item 에서 가져오기 때문에 master epub은 삭제함
 */
require_once '../../wps-config.php';
require_once INC_PATH . '/classes/WpsPaginator.php';

$code = 0;
$msg = '';

if ( empty($_GET['uid']) ) {
	$code = 401;
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
// 	$orderby = 'b.category_first ASC';	// 최근 순서
	$orderby = 'b.ID DESC';	// 최근 순서
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

// 내가 구매한 책
$query = "
		SELECT
			b.ID
		FROM
			bt_order AS o
		INNER JOIN
			bt_order_item AS i
		INNER JOIN
			bt_books AS b
		WHERE
			o.user_id = ? AND
			o.order_id = i.order_id AND
			i.book_id = b.ID
		GROUP BY
			1
";
$stmt = $wdb->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$my_books = $wdb->get_results($stmt);

$my_books_arr = [];

if (!empty($my_books)) {
	foreach ($my_books as $key => $val) {
		$book_id = $val['ID'];
		array_push($my_books_arr, $book_id);
	}
}
// /. 내가 구매한 책

$query = "
	SELECT
		b.ID,
		b.book_title,
		b.author,
		b.cover_img,
		m.meta_value AS epub
	FROM
		bt_books AS b
	LEFT JOIN
		bt_books_meta AS m
	ON
		b.ID = m.book_id AND
		m.meta_key = 'lps_book_epub_file'
	WHERE
		b.book_status = '3000' AND
		b.is_free = 'Y' AND
		b.sale_price = 0
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
		$epub = unserialize($val['epub']);
// 		$epub_url = $epub['file_url'];
		$epub_name = $epub['file_name'];
		$rows[$key]['is_my_book'] = in_array($book_id, $my_books_arr) ? 'Y' : 'N';
// 		$rows[$key]['epub_url'] = $epub['file_url'];
		$rows[$key]['epub_name'] = $epub['file_name'];
		unset($rows[$key]['epub']);
	}
}

$LIST = $rows;

$json = compact( 'code', 'msg', 'LIST' );
echo json_encode( $json );

?>