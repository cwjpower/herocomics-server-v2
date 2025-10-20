<?php
/*
 * Desc : API 랭킹, 1~10위 까지만
 * 	method : GET
 */
require_once '../../wps-config.php';
require_once FUNC_PATH . '/functions-payment.php';
require_once FUNC_PATH . '/functions-book.php';

$code = 0;
$msg = '';

// if ( empty($_GET['uid']) ) {
// 	$code = 401;
// 	$msg = '회원의 UID가 필요합니다.';
// 	$json = compact('code', 'msg');
// 	exit( json_encode($json) );
// }

$user_id = empty($_GET['uid']) ? 0 : $_GET['uid'];
$rt = empty($_GET['type']) ? 'activity' : $_GET['type'];	// 기본은 커뮤니티

$my_books_arr = [];

if (!empty($user_id)) {
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
	
	if (!empty($my_books)) {
		foreach ($my_books as $key => $val) {
			$book_id = $val['ID'];
			array_push($my_books_arr, $book_id);
		}
	}
	// /. 내가 구매한 책
}

if (!strcmp($rt, 'steady')) {	//	스테디셀러 	 

	$option_name = 'lps_best_rank_4000';
	$best_rank_book = wps_get_option( $option_name );		// 관리자 등록 책
	$book_ids = unserialize($best_rank_book);

	if ( !empty($book_ids) ) {
	
		$ids = "'" . implode("','", $book_ids) . "'";
	
		$query = "
				SELECT
					b.ID,
					b.book_title,
					b.author,
					b.cover_img,
					m.meta_value AS epub,
					m3.meta_value AS chat_url,
				    100 AS count
				FROM
					bt_books AS b
				LEFT JOIN
					bt_books_meta AS m
				ON
					b.ID = m.book_id AND
					m.meta_key = 'lps_book_epub_file'
				LEFT JOIN
					bt_books_meta AS m3
				ON
					b.ID = m3.book_id AND
					m3.meta_key = 'lps_sendbird_chat_url'
				WHERE
					ID IN ( $ids )
				ORDER BY
					FIELD(ID, $ids)
		";
	} else {
		$query = "
				SELECT
					b.ID,
					b.book_title,
					b.author,
					b.cover_img,
					m.meta_value AS epub,
					m3.meta_value AS chat_url,
					COUNT(*) AS sub_count
				FROM
					bt_books AS b
				LEFT JOIN
					bt_order_item AS o
				ON
					o.book_id = b.ID
				LEFT JOIN
					bt_books_meta AS m
				ON
					b.ID = m.book_id AND
					m.meta_key = 'lps_book_epub_file'
				LEFT JOIN
					bt_books_meta AS m3
				ON
					b.ID = m3.book_id AND
					m3.meta_key = 'lps_sendbird_chat_url'
				WHERE
					b.book_status = '3000'
				GROUP BY
					b.ID
				ORDER BY
					sub_count DESC
				LIMIT
					0, 10
		";
	}
} else if (!strcmp($rt, 'day')) {	// 일별
	$option_name = 'lps_best_rank_2000';
	$best_rank_book = wps_get_option( $option_name );		// 관리자 등록 책
	$book_ids = unserialize($best_rank_book);
	
	if ( !empty($book_ids) ) {
	
		$ids = "'" . implode("','", $book_ids) . "'";
	
		$query = "
				SELECT
					b.ID,
					b.book_title,
					b.author,
					b.cover_img,
					m.meta_value AS epub,
					m3.meta_value AS chat_url,
				    100 AS count
				FROM
					bt_books AS b
				LEFT JOIN
					bt_books_meta AS m
				ON
					b.ID = m.book_id AND
					m.meta_key = 'lps_book_epub_file'
				LEFT JOIN
					bt_books_meta AS m3
				ON
					b.ID = m3.book_id AND
					m3.meta_key = 'lps_sendbird_chat_url'
				WHERE
					ID IN ( $ids )
				ORDER BY
					FIELD(ID, $ids)
		";
	} else {
		
		$query = "
				SELECT
					b.ID,
					b.book_title,
					b.author,
					b.cover_img,
					m.meta_value AS epub,
					m3.meta_value AS chat_url,
					COUNT(*) AS sub_count
				FROM
					bt_books AS b
				LEFT JOIN
					bt_order_item AS o
				ON
					o.book_id = b.ID
				LEFT JOIN
					bt_books_meta AS m
				ON
					b.ID = m.book_id AND
					m.meta_key = 'lps_book_epub_file'
				LEFT JOIN
					bt_books_meta AS m3
				ON
					b.ID = m3.book_id AND
					m3.meta_key = 'lps_sendbird_chat_url'
				WHERE
					b.book_status = '3000'
				GROUP BY
					b.ID
				ORDER BY
					sub_count DESC
				LIMIT
					0, 10
		";
	}	
} else if (!strcmp($rt, 'week')) {	// 주별
	$option_name = 'lps_best_rank_2010';
	$best_rank_book = wps_get_option( $option_name );		// 관리자 등록 책
	$book_ids = unserialize($best_rank_book);
	
	if ( !empty($book_ids) ) {
	
		$ids = "'" . implode("','", $book_ids) . "'";
	
		$query = "
				SELECT
					b.ID,
					b.book_title,
					b.author,
					b.cover_img,
					m.meta_value AS epub,
					m3.meta_value AS chat_url,
				    100 AS count
				FROM
					bt_books AS b
				LEFT JOIN
					bt_books_meta AS m
				ON
					b.ID = m.book_id AND
					m.meta_key = 'lps_book_epub_file'
				LEFT JOIN
					bt_books_meta AS m3
				ON
					b.ID = m3.book_id AND
					m3.meta_key = 'lps_sendbird_chat_url'
				WHERE
					ID IN ( $ids )
				ORDER BY
					FIELD(ID, $ids)
		";
	} else {
		
		$query = "
				SELECT
					b.ID,
					b.book_title,
					b.author,
					b.cover_img,
					m.meta_value AS epub,
					m3.meta_value AS chat_url,
					COUNT(*) AS sub_count
				FROM
					bt_books AS b
				LEFT JOIN
					bt_order_item AS o
				ON
					o.book_id = b.ID
				LEFT JOIN
					bt_books_meta AS m
				ON
					b.ID = m.book_id AND
					m.meta_key = 'lps_book_epub_file'
				LEFT JOIN
					bt_books_meta AS m3
				ON
					b.ID = m3.book_id AND
					m3.meta_key = 'lps_sendbird_chat_url'
				WHERE
					b.book_status = '3000'
				GROUP BY
					b.ID
				ORDER BY
					sub_count DESC
				LIMIT
					0, 10
		";
	}
} else if (!strcmp($rt, 'month')) {	// 월별
	$option_name = 'lps_best_rank_2020';
	$best_rank_book = wps_get_option( $option_name );		// 관리자 등록 책
	$book_ids = unserialize($best_rank_book);
	
	if ( !empty($book_ids) ) {
	
		$ids = "'" . implode("','", $book_ids) . "'";
	
		$query = "
				SELECT
					b.ID,
					b.book_title,
					b.author,
					b.cover_img,
					m.meta_value AS epub,
					m3.meta_value AS chat_url,
				    100 AS count
				FROM
					bt_books AS b
				LEFT JOIN
					bt_books_meta AS m
				ON
					b.ID = m.book_id AND
					m.meta_key = 'lps_book_epub_file'
				LEFT JOIN
					bt_books_meta AS m3
				ON
					b.ID = m3.book_id AND
					m3.meta_key = 'lps_sendbird_chat_url'
				WHERE
					ID IN ( $ids )
				ORDER BY
					FIELD(ID, $ids)
		";
	} else {
		
		$query = "
				SELECT
					b.ID,
					b.book_title,
					b.author,
					b.cover_img,
					m.meta_value AS epub,
					m3.meta_value AS chat_url,
					COUNT(*) AS sub_count
				FROM
					bt_books AS b
				LEFT JOIN
					bt_order_item AS o
				ON
					o.book_id = b.ID
				LEFT JOIN
					bt_books_meta AS m
				ON
					b.ID = m.book_id AND
					m.meta_key = 'lps_book_epub_file'
				LEFT JOIN
					bt_books_meta AS m3
				ON
					b.ID = m3.book_id AND
					m3.meta_key = 'lps_sendbird_chat_url'
				WHERE
					b.book_status = '3000'
				GROUP BY
					b.ID
				ORDER BY
					sub_count DESC
				LIMIT
					0, 10
		";
	}
} else {	// 커뮤니티 , 댓글 많은 순		activity
	$option_name = 'lps_best_rank_1000';
	$best_rank_book = wps_get_option( $option_name );		// 관리자 등록 책
	$book_ids = unserialize($best_rank_book);
	
	if ( !empty($book_ids) ) {
	
		$ids = "'" . implode("','", $book_ids) . "'";
	
		$query = "
				SELECT
					b.ID,
					b.book_title,
					b.author,
					b.cover_img,
					m.meta_value AS epub,
					m3.meta_value AS chat_url,
				    100 AS count
				FROM
					bt_books AS b
				LEFT JOIN
					bt_books_meta AS m
				ON
					b.ID = m.book_id AND
					m.meta_key = 'lps_book_epub_file'
				LEFT JOIN
					bt_books_meta AS m3
				ON
					b.ID = m3.book_id AND
					m3.meta_key = 'lps_sendbird_chat_url'
				WHERE
					ID IN ( $ids )
				ORDER BY
					FIELD(ID, $ids)
		";
	} else {
		$query = "
				SELECT 
					a.book_id AS ID,
					b.book_title,
					b.author,
					b.cover_img,
				    m.meta_value AS epub,
					m3.meta_value AS chat_url,
					COUNT(c.comment_id) AS sub_count
				FROM
					bt_activity AS a
				LEFT JOIN
					bt_activity_comment AS c
				ON
					a.id = c.activity_id
				LEFT JOIN
					bt_books AS b
				ON
					b.ID = a.book_id
				LEFT JOIN
					bt_books_meta AS m
				ON
					b.ID = m.book_id AND
					m.meta_key = 'lps_book_epub_file'
				LEFT JOIN
					bt_books_meta AS m3
				ON
					b.ID = m3.book_id AND
					m3.meta_key = 'lps_sendbird_chat_url'
				WHERE
					b.book_status = '3000'
				GROUP BY
					1
				ORDER BY
					sub_count DESC
				LIMIT
					0, 10
		";	
	}
}

$stmt = $wdb->prepare( $query );
// $stmt->bind_param( 'i', $user_id );
$stmt->execute();
$rows = $wdb->get_results($stmt);


if (!empty($rows)) {
	foreach ($rows as $key => $val) {
		$book_id = $val['ID'];
		$epub = unserialize($val['epub']);
// 		$epub_url = $epub['file_url'];
		$epub_name = $epub['file_name'];
// 		$rows[$key]['is_my_book'] = in_array($book_id, $my_books_arr) ? 'Y' : 'N';
		
		if (in_array($book_id, $my_books_arr)) {
			$rows[$key]['is_my_book'] = 'Y';
			$rows[$key]['epub_url'] = lps_get_order_epub_url( $user_id, $book_id );
		} else {
			$rows[$key]['is_my_book'] = 'N';
			$rows[$key]['epub_url'] = '';
		}
		
// 		$rows[$key]['epub_url'] = $epub['file_url'];
		$rows[$key]['epub_name'] = $epub['file_name'];
		unset($rows[$key]['epub']);
	}
}

$LIST = $rows;

$json = compact( 'code', 'msg', 'LIST' );
echo json_encode( $json );

?>