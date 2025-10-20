<?php
/*
 * Desc : API 읽을거에요 리스트, 찜리스트
 * 	method : GET
 */
require_once '../../wps-config.php';
require_once FUNC_PATH . '/functions-payment.php';
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

$wishlist = wps_get_user_meta($user_id, 'lps_user_wishlist');
$rows = [];

if ( !empty($wishlist) ) {
	$wishlist_unserial = unserialize($wishlist);
	$wish_books = implode(',', $wishlist_unserial);
	
	// SNS
	$sns_shared = wps_get_user_meta($user_id, 'lps_sns_shared_book');
	$sns_unserial = @unserialize($sns_shared);
	if (empty($sns_unserial)) {
		$sns_unserial = [];
	}
	
	$query = "
			SELECT
				b.ID,
				b.book_title,
				b.author,
				b.cover_img,
				m.meta_value AS epub,
				m3.meta_value AS chat_url
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
				b.ID IN ($wish_books) AND
				b.book_status = '3000'
				
	";
	$obj = $wdb->query( $query );
	$rows = $wdb->get_results($obj);
	
	if (!empty($rows)) {
		foreach ($rows as $key => $val) {
			$book_id = $val['ID'];
	
			$epub = unserialize($val['epub']);
			
			$epub_name = $epub['file_name'];
			$rows[$key]['epub_name'] = $epub['file_name'];
			$rows[$key]['share_fb_url'] = MOBILE_URL . '/book/book.php?id=' . $book_id;
			
			if (in_array($book_id, $my_books_arr)) {
				$rows[$key]['is_my_book'] = 'Y';
				$rows[$key]['epub_url'] = lps_get_order_epub_url( $user_id, $book_id );
			} else {
				$rows[$key]['is_my_book'] = 'N';
				$rows[$key]['epub_url'] = '';
			}
			
			// SNS 포함 여부 check
			if (in_array($book_id, $sns_unserial)) {
				$rows[$key]['sns'] = 1;
			} else {
				$rows[$key]['sns'] = 0;
			}
			
			unset($rows[$key]['epub']);
		}
	}
}

$LIST = $rows;

$json = compact( 'code', 'msg', 'LIST' );
echo json_encode( $json );

?>