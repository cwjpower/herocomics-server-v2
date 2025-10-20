<?php
function lps_add_memo_log() {
	global $wdb;
	

	$user_id = $_POST['userID'];
	$memo = $_POST['memo'];
	$created_by = wps_get_current_user_name();
	
	$query = "
			INSERT INTO
				bt_memo_logs
				(
					ID,
					user_id,
					memo,
					created_by,
					created_dt
				)
			VALUES
				(
					NULL, ?, ?, ?, NOW()
				)
	";
	$stmt = $wdb->prepare($query);
	$stmt->bind_param( 'iss', $user_id, $memo, $created_by );
	$stmt->execute();
	return $wdb->insert_id;
}

function lps_get_memo_logs( $id ) {
	global $wdb;
	
	$query = "
			SELECT
				*
			FROM
				bt_memo_logs
			WHERE
				ID = ?
	";
	$stmt = $wdb->prepare($query);
	$stmt->bind_param( 'i', $id );
	$stmt->execute();
	return $wdb->get_row($stmt);
}

function lps_get_memo_logs_for_user( $user_id, $limit = 5 ) {
	global $wdb;
	
	$query = "
			SELECT
				*
			FROM
				bt_memo_logs
			WHERE
				user_id = ?
			ORDER BY
				ID DESC
			LIMIT
				0, $limit
	";
	$stmt = $wdb->prepare($query);
	$stmt->bind_param( 'i', $user_id );
	$stmt->execute();
	return $wdb->get_results($stmt);
}

/*
 * Desc : 페이지 번호에 따른 메모 리스트 조회
 */
function lps_get_memo_logs_by_page( $user_id, $page = 1, $limit = 15 ) {
	global $wdb;
	
	$start = ($page - 1) * $limit;
	
	$query = "
			SELECT
				SQL_CALC_FOUND_ROWS *
			FROM
				bt_memo_logs
			WHERE
				user_id = ?
			ORDER BY
				ID DESC
			LIMIT
				$start, $limit
	";
	$stmt = $wdb->prepare($query);
	$stmt->bind_param( 'i', $user_id );
	$stmt->execute();
	$memo_rows = $wdb->get_results($stmt);
	
	$query = "SELECT FOUND_ROWS() AS total_count";
	$stmt = $wdb->prepare($query);
	$stmt->execute();
	$total_row = $wdb->get_row($stmt);
	
	$compact = compact('memo_rows', 'total_row');
	return $compact;
}

/*
 * Desc : 메모 Pagination
 */
function lps_get_memo_pagination_link( $user_id, $page = 1, $row_count = 15 ) {
	global $wdb;
	require_once INC_PATH . '/classes/WpsPaginator.php';
	
	$user_id = $wdb->real_escape_string($user_id);
	
	$query = "
			SELECT
				*
			FROM
				bt_memo_logs
			WHERE
				user_id = '$user_id'
	";
	$paginator = new WpsPaginator($wdb, $page, $row_count);
	
	$paginator->ls_init_pagination( $query, null );
	
	return $paginator->ls_get_ajax_pagination('memo_log');
}

function lps_delete_memo_logs( $user_id, $memo_id ) {
	global $wdb;
	
	$ids = "'" . implode("','",$memo_id) . "'";
			
	$query = "
			DELETE FROM
				bt_memo_logs
			WHERE
				ID IN ($ids) AND
				user_id = ?
	";
	$stmt = $wdb->prepare($query);
	$stmt->bind_param( 'i', $user_id );
	$stmt->execute();
	return $wdb->affected_rows;
}

/*
 * Desc : 페이지 번호에 회원 문의내역
 */
function lps_get_user_qna_by_page( $user_id, $page = 1, $limit = 15 ) {
	global $wdb;
	
	$start = ($page - 1) * $limit;
	
	$query = "
			SELECT
				SQL_CALC_FOUND_ROWS *
			FROM
				bt_posts_qnas
			WHERE
				post_user_id = ? AND
				post_type = 'qna_new' 
			ORDER BY
				ID DESC
			LIMIT
				$start, $limit
	";
	$stmt = $wdb->prepare($query);
	$stmt->bind_param( 'i', $user_id );
	$stmt->execute();
	$qna_rows = $wdb->get_results($stmt);
	
	$query = "SELECT FOUND_ROWS() AS total_count";
	$stmt = $wdb->prepare($query);
	$stmt->execute();
	$total_row = $wdb->get_row($stmt);
	
	$compact = compact('qna_rows', 'total_row');
	return $compact;
}

/*
 * Desc : 문의내역 Pagination
 */
function lps_get_qna_pagination_link( $user_id, $page = 1, $row_count = 15 ) {
	global $wdb;
	require_once INC_PATH . '/classes/WpsPaginator.php';
	
	$user_id = $wdb->real_escape_string($user_id);
	
	$query = "
			SELECT
				*
			FROM
				bt_posts_qnas
			WHERE
				post_user_id = '$user_id' AND
				post_type = 'qna_new'
	";
	$paginator = new WpsPaginator($wdb, $page, $row_count);
	
	$paginator->ls_init_pagination( $query, null );
	
	return $paginator->ls_get_ajax_pagination('qna_log');
}

/*
 * Desc : 페이지 번호에 회원 구매내역
 */
function lps_get_user_order_by_page( $user_id, $page = 1, $limit = 15 ) {
	global $wdb;
	
	$start = ($page - 1) * $limit;
	
	$query = "
			SELECT
				SQL_CALC_FOUND_ROWS
				o.order_id,
			    COUNT(*) AS count_order,
				o.order_status,
				o.total_amount,
				o.cybercash_paid,
				o.created_dt,
				o.updated_dt,
				u.user_name,
			    i.book_title
			FROM
				bt_order AS o
			INNER JOIN
				bt_order_item AS i
			LEFT JOIN
				bt_users AS u
			ON
				u.ID = o.user_id
			LEFT JOIN
				bt_books AS b
			ON
				b.ID = i.book_id
			WHERE 
				o.user_id = ? AND
				o.order_id = i.order_id
			GROUP BY
				o.order_id,
				i.book_title
			ORDER BY
				o.order_id DESC
			LIMIT
				$start, $limit
	";
	$stmt = $wdb->prepare($query);
	$stmt->bind_param( 'i', $user_id );
	$stmt->execute();
	$order_rows = $wdb->get_results($stmt);
	
	$query = "SELECT FOUND_ROWS() AS total_count";
	$stmt = $wdb->prepare($query);
	$stmt->execute();
	$total_row = $wdb->get_row($stmt);
	
	$compact = compact('order_rows', 'total_row');
	return $compact;
}

/*
 * Desc : 구매내역 Pagination
 */
function lps_get_order_pagination_link( $user_id, $page = 1, $row_count = 15 ) {
	global $wdb;
	require_once INC_PATH . '/classes/WpsPaginator.php';
	
	$user_id = $wdb->real_escape_string($user_id);
	
	$query = "
			SELECT
				*
			FROM
				bt_order AS o
			INNER JOIN
				bt_order_item AS i
			LEFT JOIN
				bt_users AS u
			ON
				u.ID = o.user_id
			LEFT JOIN
				bt_books AS b
			ON
				b.ID = i.book_id
			WHERE 
				o.user_id = ? AND
				o.order_id = i.order_id
			GROUP BY
				o.order_id,
				i.book_title
	";
	$paginator = new WpsPaginator($wdb, $page, $row_count);
	
	$paginator->ls_init_pagination( $query, null );
	
	return $paginator->ls_get_ajax_pagination('order_log');
}

/*
 * Desc : 페이지 번호에 회원 게시글
 */
function lps_get_user_post_by_page( $user_id, $page = 1, $limit = 15 ) {
	global $wdb;
	
	$start = ($page - 1) * $limit;
	
	$query = "
			SELECT
				SQL_CALC_FOUND_ROWS
				b.book_title,
				a.id,
				a.subject,
				a.created_dt,
				a.count_like
			FROM
				bt_activity AS a
			INNER JOIN
				bt_books AS b
			WHERE 
				a.user_id = ? AND
				a.component = 'activity' AND
				a.type = 'activity_update' AND
				a.book_id = b.ID
			ORDER BY
				a.id DESC
			LIMIT
				$start, $limit
	";
	$stmt = $wdb->prepare($query);
	$stmt->bind_param( 'i', $user_id );
	$stmt->execute();
	$post_rows = $wdb->get_results($stmt);
	
	$query = "SELECT FOUND_ROWS() AS total_count";
	$stmt = $wdb->prepare($query);
	$stmt->execute();
	$total_row = $wdb->get_row($stmt);
	
	$compact = compact('post_rows', 'total_row');
	return $compact;
}

/*
 * Desc : 게시글 Pagination
 */
function lps_get_post_pagination_link( $user_id, $page = 1, $row_count = 15 ) {
	global $wdb;
	require_once INC_PATH . '/classes/WpsPaginator.php';
	
	$user_id = $wdb->real_escape_string($user_id);
	
	$query = "
			SELECT
				*
			FROM
				bt_activity AS a
			INNER JOIN
				bt_books AS b
			WHERE 
				a.user_id = '$user_id' AND
				a.component = 'activity' AND
				a.type = 'activity_update' AND
				a.book_id = b.ID
	";
	$paginator = new WpsPaginator($wdb, $page, $row_count);
	
	$paginator->ls_init_pagination( $query, null );
	
	return $paginator->ls_get_ajax_pagination('post_log');
}

/*
 * Desc : 페이지 번호에 회원 찜목록
 */
function lps_get_user_wish_by_page( $user_id, $page = 1, $limit = 15 ) {
	global $wdb;
	
	$wishlist = wps_get_user_meta($user_id, 'lps_user_wishlist');
	$wishlist_array = unserialize($wishlist);
	
	$start = ($page - 1) * $limit;
	
	$wish_rows = @array_slice($wishlist_array, $start, $limit);
	$total_row = count($wishlist_array);
	
	$compact = compact('wish_rows', 'total_row');
	return $compact;
}


?>