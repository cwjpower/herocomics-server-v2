<?php
// 코믹스 브랜드 배열 정의
// 숫자를 문자열로 매핑

$wps_comics_brand = array(
    'MARVEL' => '마블 코믹스',
    'DC' => 'DC 코믹스',
    'IMAGE' => '이미지 코믹스',
    'DARK_HORSE' => '다크 호스',
    'IDW' => 'IDW',
    '' => '-'
);


// 코믹스 브랜드 배열 정의
$wps_comics_brand = array(
    'MARVEL' => '마블 코믹스',
    'DC' => 'DC 코믹스',
    'IMAGE' => '이미지 코믹스',
    'DARK_HORSE' => '다크 호스',
    'IDW' => 'IDW',
    '' => '-'
);

$wps_comics_brand_css = array(
    'MARVEL' => 'marvel',
    'DC' => 'dc',
    'IMAGE' => 'image',
    'DARK_HORSE' => 'darkhorse',
    'IDW' => 'idw',
    'TOTAL' => 'total',
    '' => 'default'
);

require_once INC_PATH . '/classes/WpsThumbnail.php';

/**
 * @param $book_id
 * @return array
 * 등록된 액션뷰 정보 조회하기
 *
 */
function lps_get_book_action($book_id){
	global $wdb;

	$query = "
		SELECT
			*
		FROM
			bt_books_action
		WHERE
			book_id = ?
		ORDER BY
		 	page_no
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param('i', $book_id);
	$stmt->execute();
	return $wdb->get_results($stmt);
}

/**
 * @param $book_id
 * @param $page
 * @return array
 * 넘겨받은 책 아이디와 페이지 정보를 이용해 해당 페이지의 액션뷰 정보를 조회한다.
 */
function lps_get_book_action_page($book_id, $page_name){
	global $wdb;


	error_log($book_id);
	error_log($page_name);
	$query = "
		SELECT
			*
		FROM
			bt_books_action
		WHERE
			book_id = ? AND
			page_no = ?
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param('is', $book_id, $page_name);

	$stmt->execute();

	return $wdb->get_row($stmt);
}


/**
 * @param $pannels_info_json
 * 액션 뷰 정보 등록하기
 *
 * 1. DB 에 정보를 등록한다.
 * 2. Server 에 avf 파일을 등록한다.  (action view frames)
 */
function lps_add_book_action($pannels_info_json){
	global $wdb;

	// get params
	$book_id = $_POST['book-id'];
	$page = intval($_POST['Page']);
	$page_name = $_POST['Page'];
	$width = intval($_POST['Width']);
	$height = intval($_POST['Height']);
	$pannels = $_POST['p'];
	$pannels_json = $pannels_info_json;

	// prepare query
	$query = "
			INSERT INTO
				bt_books_action
				( id, book_id, page_no, page_height, page_width, pannel_info, created_dt )
			VALUES
				( NULL, ?, ?, ?, ?, ?, NOW() )
	";

	$stmt = $wdb->prepare( $query );

	// bind param
	$stmt->bind_param( 'isiis', $book_id, $page_name, $height, $width, $pannels_json);
	$stmt->execute();
	error_log(mysqli_stmt_error($stmt));

	//파일 생성 로직
	if( lps_add_book_action_json($book_id) ) {
		return $book_id;
	} else {
		error_log('파일 생성 실패 in ' . __FUNCTION__ );
	}

}

/**
 * 페이지별로 action view pannel 정보를 업데이트 한다.
 */
function lps_update_book_action($pannels_info_json){
	global $wdb;

	$book_id = intval($_POST['book-id']);
	$page = intval($_POST['Page']);
	$page_name = $_POST['Page'];
	$width = intval($_POST['Width']);
	$height = intval($_POST['Height']);
	//$pannels = $_POST['p'];
	$pannels_json = $pannels_info_json;

	// prepare query
	$query = "
			UPDATE
				bt_books_action
			SET
				book_id = ?,
				page_no=?,
				page_height=?,
				page_width=?,
				pannel_info=?,
				created_dt=NOW()
			WHERE
			 book_id =? AND
			 page_no =?
	";

	$stmt = $wdb->prepare( $query );

	// bind param
	$stmt->bind_param( 'isiisis', $book_id, $page_name, $height, $width, $pannels_json, $book_id, $page_name);
	$stmt->execute();
	error_log(mysqli_stmt_error($stmt));

	//파일 생성 로직
	if( lps_add_book_action_json($book_id) ) {
		return $book_id;
	} else {
		error_log('파일 생성 실패 in ' . __FUNCTION__ );
	}

}

/**
 * 액션 뷰 파일 정보를 생성한다
 * upload/book/book_id/book_id.svf
 */
function lps_add_book_action_json($book_id){

	global $wdb;

	$book_action_rows = lps_get_book_action($book_id);

	$pannels_info=[] ;
	foreach($book_action_rows as $row){
		array_push($pannels_info , json_decode($row['pannel_info'], true) ) ;
	}

	// 생성할 json data
	$to_write_json = json_encode($pannels_info, JSON_PRETTY_PRINT);

	// 책 정보 파일이 있는 디렉토리에 $book_id.svf 파일을 생성한다.

	$book_dir = UPLOAD_PATH .'/books/'. $book_id . '/';
	$book_url = UPLOAD_URL .'/books/'. $book_id . '/' . $book_id . '.svf';
	//$upload_real['pdir'] = UPLOAD_PATH . '/books/' . $ymw;

	if ( !is_dir($book_dir) ) {
		mkdir($book_dir, 0777, true);
	}

	$svf_file = fopen($book_dir . $book_id .".svf","w");
	if(!$svf_file) die("Cannot open the file.");

	if(fwrite($svf_file, $to_write_json)) {
		fclose($svf_file);

		// bt_books svf_url 정보 업데이트
		$query = "
			UPDATE
				bt_books
			SET
				svf_url = ?
			WHERE
			 id =?
		";

		$stmt = $wdb->prepare( $query );

		// bind param
		$stmt->bind_param( 'si', $book_url, $book_id);
		$stmt->execute();

		return true;
	} else {
		error_log('svf 생성 실패 ');
		return false;
	}
}

function lps_add_book() {
	global $wdb;

	$is_pkg = $_POST['is_pkg'];
	$is_free = empty($_POST['is_free']) ? 'N' : 'Y';
	$book_title = $_POST['book_title'];
	$author = empty($_POST['author']) ? '' : $_POST['author'];
	$publisher = empty($_POST['publisher']) ? '' : $_POST['publisher'];
	$published_dt = empty($_POST['published_dt']) ? '' : $_POST['published_dt']; // 출간일
	$isbn = empty($_POST['isbn']) ? '' : preg_replace('/\D/', '', $_POST['isbn']);
	$comics_brand = empty($_POST['comics_brand']) ? '1' : $_POST['comics_brand'];
	$normal_price = empty($_POST['normal_price']) ? 0: $_POST['normal_price'];
	$discount_rate = empty($_POST['discount_rate']) ? 0 : $_POST['discount_rate'];
	$sale_price = empty($_POST['sale_price']) ? 0: $_POST['sale_price'];
	$upload_type = empty($_POST['upload_type']) ? '' : $_POST['upload_type'];
	$period_from = empty($_POST['period_from']) ? NULL : $_POST['period_from'];
	$period_to = empty($_POST['period_to']) ? NULL : $_POST['period_to'];

	$file_size = empty($_POST['file_size']) ? NULL : $_POST['file_size'];
	$support_device = empty($_POST['support_device']) ? NULL : $_POST['support_device'];
	$reading_order_before = empty($_POST['reading_order_id_before']) ? NULL : $_POST['reading_order_id_before'];
	$reading_order_after = empty($_POST['reading_order_id_after']) ? NULL : $_POST['reading_order_id_after'];

	$book_status = '1000';

	$user_id = wps_get_current_user_id();

	if ( !strcmp($is_free, 'Y') ) {
		$normal_price = 0;
		$sale_price = 0;
		$discount_rate = 0;
	}

	if (wps_get_user_level() == '6') {	// 1인 작가의 출판사는 북톡출판사
		$publisher_id = BOOKTALK_PUBLISHER_ID;
	} else {
		$publisher_id = $user_id;
	}

	error_log($published_dt);
	$epub_name = '';
	$epub_path = empty($_POST['file_path_epub'][0]) ? '' : $_POST['file_path_epub'][0];
	$cover_img = empty($_POST['file_url_cover'][0]) ? '' : $_POST['file_url_cover'][0];

	$category_first = empty($_POST['category_first']) ? 0: $_POST['category_first'];
	$category_second = empty($_POST['category_second']) ? 0 : $_POST['category_second'];
	$category_third = empty($_POST['category_third']) ? 0 : $_POST['category_third'];

	/**
	 * comics_brand 추가 by endstern
	 * ( 마블, 디씨, 이미지 ) 코믹스
	 * 새로 추가 되는 필드는 맨 뒤로 붙인다 ..
	 */
	$query = "
			INSERT INTO
				bt_books
				(
					ID,
					book_title,
					author,
					publisher,
					isbn,

					normal_price,
					discount_rate,
					sale_price,
					cover_img,
					epub_path,

					epub_name,
					period_from,
					period_to,
					category_first,
					category_second,

					category_third,
					is_pkg,
					is_free,
					upload_type,
					book_status,

					created_dt,
					user_id,
					publisher_id,
					comics_brand,
					published_dt,
					epub_size,
					support_device,
					reading_order_before,
					reading_order_after
				)
			VALUES
				(
					NULL, ?, ?, ?, ?,
					?,?, ?, ?, ?,
					?,?, ?, ?, ?,
					?,?, ?, ?, ?,
					NOW(), ?, ?, ?, ?,
					?,?,?,?
				)
	";

	// error_log($query);
	$stmt = $wdb->prepare( $query );

	$stmt->bind_param( 'ssssiiisssssiiisssiiissssii', $book_title, $author, $publisher,
		$isbn, $normal_price, $discount_rate, $sale_price, $cover_img,
		$epub_path, $epub_name,	$period_from, $period_to, $category_first,
		$category_second, $category_third, $is_pkg, $is_free, $upload_type,
		$book_status, $user_id, $publisher_id, $comics_brand, $published_dt,
		$file_size, $support_device, $reading_order_before, $reading_order_after
	);
	$stmt->execute();
	error_log(mysqli_stmt_error($stmt));

	$book_id = $wdb->insert_id;

	if (!empty($_POST['introduction_book'])) {
		$meta_key = 'lps_introduction_book';
		lps_update_book_meta($book_id, $meta_key, $_POST['introduction_book']);
	}
	if (!empty($_POST['introduction_author'])) {
		$meta_key = 'lps_introduction_author';
		lps_update_book_meta($book_id, $meta_key, $_POST['introduction_author']);
	}
	if (!empty($_POST['publisher_review'])) {
		$meta_key = 'lps_publisher_review';
		lps_update_book_meta($book_id, $meta_key, $_POST['publisher_review']);
	}
	if (!empty($_POST['book_table'])) {
		$meta_key = 'lps_book_table';
		lps_update_book_meta($book_id, $meta_key, $_POST['book_table']);
	}

	$ymw = date('YmW');
	$upload_real['pdir'] = UPLOAD_PATH . '/books/' . $book_id;  // $ymw -> book id 로 변경
	$upload_real['udir'] = UPLOAD_URL . '/books/' . $book_id;

	if ( !is_dir($upload_real['pdir']) ) {
		mkdir($upload_real['pdir'], 0777, true);
	}

	// EPUB 파일 업데이트 및 이동
	if (!empty($_POST['file_path_epub'][0]) && is_file($_POST['file_path_epub'][0])) {
		$tmp_path = $_POST['file_path_epub'][0];
		$tmp_file = basename($tmp_path);

		$new_path = $upload_real['pdir'] . '/'. $tmp_file;
		$new_url = $upload_real['udir'] . '/' . $tmp_file;

		if (rename($tmp_path, $new_path)) {
			// epub
			$file_path = $new_path;
			$file_url = $new_url;
			$file_name = $_POST['file_name_epub'][0];
			$compact = serialize(compact('file_path', 'file_url', 'file_name'));
			lps_update_book_meta($book_id, 'lps_book_epub_file', $compact);

			$query = "
					UPDATE
						bt_books
					SET
						epub_path = ?,
						epub_name = ?,
						epub_url = ?
					WHERE
						ID = ?
			";
			$stmt = $wdb->prepare( $query );
			$stmt->bind_param( 'sssi', $file_path, $file_name, $file_url, $book_id );
			$stmt->execute();
		}
	}

	// Cover image 업데이트 및 이동
	if (!empty($_POST['file_path_cover'][0]) && is_file($_POST['file_path_cover'][0])) {
		$tmp_path = $_POST['file_path_cover'][0];
		$tmp_file = basename($tmp_path);

		$new_path = $upload_real['pdir'] . '/'. $tmp_file;
		$new_url = $upload_real['udir'] . '/' . $tmp_file;

		if (rename($tmp_path, $new_path)) {
			// cover
			$file_path = $new_path;
			$file_url = $new_url;
			$file_name = $_POST['file_name_cover'][0];
			$compact = serialize(compact('file_path', 'file_url', 'file_name'));
			lps_update_book_meta($book_id, 'lps_book_cover_file', $compact);

			$query = "
					UPDATE
						bt_books
					SET
						cover_img = ?
					WHERE
						ID = ?
			";
			$stmt = $wdb->prepare( $query );
			$stmt->bind_param( 'si', $file_url, $book_id );
			$stmt->execute();
		}
	}

	// 세트
	if (!strcmp($is_pkg, 'Y')) {
		$meta_key = 'lps_pkg_book_list';
		$meta_value = serialize($_POST['pkg_books']);
		lps_update_book_meta($book_id, $meta_key, $meta_value);
	}

	return $book_id;
}

/*
 * Desc : 책 조회
 */
function lps_get_book( $book_id ) {
	global $wdb;

	$query = "
		SELECT
			*
		FROM
			bt_books
		WHERE
			ID = ?
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param('i', $book_id);
	$stmt->execute();
	return $wdb->get_row($stmt);
}

/*
 * Desc : 본인의 수정/삭제 요청 내역을 취소합니다.
 */
function lps_req_cancel_book($book_id) {
	global $wdb;

	$user_id = wps_get_current_user_id();
	$result = false;

	$book_row = lps_get_book($book_id);
	$book_status = $book_row['book_status'];

	if ($book_status == '2001') {	// 수정요청을 취소

		$pdata = unserialize(lps_get_book_meta($book_id, 'lps_book_prev_data'));

		foreach ($pdata as $key => $val) {
			$$key = $val;
		}
		$req_edit_val = empty($req_edit_dt) ? "NULL" : "'" . $req_edit_dt . "'";
		$req_del_val = empty($req_del_dt) ? "NULL" : "'" . $req_del_dt . "'";

		$query = "
			UPDATE
				bt_books
			SET
				book_title = '$book_title',
				author = '$author',
				publisher = '$publisher',
				isbn = '$isbn',
				normal_price = '$normal_price',
				sale_price = '$sale_price',
				cover_img = '$cover_img',
				epub_path = '$epub_path',
				epub_name = '$epub_name',
				period_from = '$period_from',
				period_to = '$period_to',
				category_first = '$category_first',
				category_second = '$category_second',
				category_third = '$category_third',
				is_pkg = '$is_pkg',
				upload_type = '$upload_type',
				book_status = '$book_status',
				created_dt = '$created_dt',
				req_edit_dt = $req_edit_val,
				req_del_dt = $req_del_val,
				user_id = '$user_id'
			WHERE
				ID = '$book_id'
		";
		$stmt = $wdb->prepare( $query );
		$stmt->execute();
		$result = $wdb->affected_rows;

		if ($result) {
			if (!empty($introduction_book)) {
				$meta_key = 'lps_introduction_book';
				lps_update_book_meta($book_id, $meta_key, $introduction_book);
			}
			if (!empty($introduction_author)) {
				$meta_key = 'lps_introduction_author';
				lps_update_book_meta($book_id, $meta_key, $introduction_author);
			}
			if (!empty($publisher_review)) {
				$meta_key = 'lps_publisher_review';
				lps_update_book_meta($book_id, $meta_key, $publisher_review);
			}
			if (!empty($book_table)) {
				$meta_key = 'lps_book_table';
				lps_update_book_meta($book_id, $meta_key, $book_table);
			}
			if (!empty($lps_book_epub_file)) {
				$meta_key = 'lps_book_epub_file';
				lps_update_book_meta($book_id, $meta_key, $lps_book_epub_file);
			}
			if (!empty($lps_book_cover_file)) {
				$meta_key = 'lps_book_cover_file';
				lps_update_book_meta($book_id, $meta_key, $lps_book_cover_file);
			}
		}

	} else if ($book_status == '2101') {	// 삭제요청을 취소
		$book_status = lps_get_book_meta($book_id, 'lps_req_prev_status');

		$query = "
			UPDATE
				bt_books
			SET
				book_status = '$book_status'
			WHERE
				ID = ? AND
				user_id = ?
		";
		$stmt = $wdb->prepare( $query );
		$stmt->bind_param('ii', $book_id, $user_id);
		$stmt->execute();
		$result = $wdb->affected_rows;
	}
	return $result;
}

/*
 * Desc : 책 등록 > user_id가 있으면 책 리스트에 해당 출판사의 책만 리스트업
 */
function lps_get_books_by_user( $user_id = NULL ) {
	global $wdb;

	if (empty($user_id)) {
		$query = "
			SELECT
				*
			FROM
				bt_books
			WHERE
				is_pkg = 'N' AND
				book_status = '3000'
			ORDER BY
				book_title ASC
		";
		$stmt = $wdb->prepare( $query );
	} else {
		$query = "
			SELECT
				*
			FROM
				bt_books
			WHERE
				user_id = ? AND
				is_pkg = 'N' AND
				book_status = '3000'
			ORDER BY
				book_title ASC
		";
		$stmt = $wdb->prepare( $query );
		$stmt->bind_param('i', $user_id);
	}
	$stmt->execute();
	return $wdb->get_results($stmt);
}


/*
 * Desc : 책 검색
 */
function lps_search_book_by_keyword( $q ) {
	global $wdb;

	$query = "
		SELECT
			ID,
			book_title,
			author,
			cover_img
		FROM
			bt_books
		WHERE
			book_status = '3000' AND
			book_title LIKE ?
		ORDER BY
			book_title ASC
	";
	$stmt = $wdb->prepare( $query );
	$qs = '%' . $q . '%';
	$stmt->bind_param('s', $qs);
	$stmt->execute();
	return $wdb->get_results($stmt);
}

/*
 * Desc : 출판사 입점 도서, 메인 > 출판사 클릭 후 출판사의 입점 도서 리스트
 */
function lps_get_publisher_books( $publisher_id, $orderby = 'book_title ASC' ) {
	global $wdb;

	$query = "
		SELECT
			b.*,
			m.meta_value AS hit_count,
			m2.meta_value AS order_count
		FROM
			bt_books AS b
		LEFT JOIN
			bt_books_meta AS m
		ON
			m.meta_key = 'lps_book_hit_count' AND
			b.ID = m.book_id
		LEFT JOIN
			bt_books_meta AS m2
		ON
			m2.meta_key = 'lps_book_order_count' AND
		    b.ID = m2.book_id
		WHERE
			b.user_id = ? AND
			b.is_pkg = 'N' AND
			b.book_status = '3000'
		ORDER BY
			$orderby
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param('i', $publisher_id);
	$stmt->execute();
	return $wdb->get_results($stmt);
}

/*
 * Desc : 책 정보 변경 > 특정 pkg_id에 포함되지 않은 책들만 리스트업
 */
function lps_get_books_except_pkg( $book_id, $user_id ) {
	global $wdb;

	$except_ids = unserialize(lps_get_book_meta($book_id, 'lps_pkg_book_list'));

	if (!empty($except_ids)) {
		$ids = "'" . implode("','", $except_ids) . "'";
	} else {
		$ids = 0;
	}

	$query = "
		SELECT
			*
		FROM
			bt_books
		WHERE
			user_id = ? AND
			is_pkg = 'N' AND
			book_status = '3000' AND
			ID NOT IN ( $ids )
		ORDER BY
			book_title ASC
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param('i', $user_id);
	$stmt->execute();
	return $wdb->get_results($stmt);
}

/*
 * Desc : 수정/삭제 요청 리스트
 */
function lps_get_req_book_by_page( $page = 1, $limit = 15 ) {
	global $wdb;

	$user_id = wps_is_admin() ? 0 : wps_get_current_user_id();

	$start = ($page - 1) * $limit;

	if ( !empty($user_id) ) {
		$query = "
				SELECT
					SQL_CALC_FOUND_ROWS *
				FROM
					bt_books
				WHERE
					user_id = ? AND
					book_status IN ('2001', '2101')
				ORDER BY
					req_edit_dt DESC,
					req_del_dt DESC
				LIMIT
					$start, $limit
		";
		$stmt = $wdb->prepare($query);
		$stmt->bind_param( 'i', $user_id );
	} else {
		$query = "
				SELECT
					SQL_CALC_FOUND_ROWS *
				FROM
					bt_books
				WHERE
					book_status IN ('2001', '2101')
				ORDER BY
					req_edit_dt DESC,
					req_del_dt DESC
				LIMIT
					$start, $limit
		";
		$stmt = $wdb->prepare($query);
	}
	$stmt->execute();
	$book_rows = $wdb->get_results($stmt);

	$query = "SELECT FOUND_ROWS() AS total_count";
	$stmt = $wdb->prepare($query);
	$stmt->execute();
	$total_row = $wdb->get_row($stmt);

	$compact = compact('book_rows', 'total_row');
	return $compact;
}

/*
 * Desc : Pagination
 */
function lps_get_req_book_pagination_link($page = 1, $row_count = 15) {
	global $wdb;
	require_once INC_PATH . '/classes/WpsPaginator.php';

	$user_id = wps_is_admin() ? 0 : wps_get_current_user_id();


	if ( !empty($user_id) ) {
		$query = "
				SELECT
					*
				FROM
					bt_books
				WHERE
					user_id = '$user_id' AND
					book_status IN ('2001', '2101')
				ORDER BY
					req_edit_dt DESC,
					req_del_dt DESC
		";
	} else {
		$query = "
				SELECT
					*
				FROM
					bt_books
				WHERE
					book_status IN ('2001', '2101')
				ORDER BY
					req_edit_dt DESC,
					req_del_dt DESC
		";
	}
	$paginator = new WpsPaginator($wdb, $page, $row_count);

	$paginator->ls_init_pagination( $query, null );

	return $paginator->ls_get_ajax_pagination('lps-pager');
}

/*
 * Desc : 승인된 책들만
 */
function lps_get_approved_books() {
	global $wdb;

	$query = "
		SELECT
			*
		FROM
			bt_books
		WHERE
			is_pkg = 'N' AND
			book_status = '3000'
		ORDER BY
			book_title ASC
	";
	$stmt = $wdb->prepare( $query );
	$stmt->execute();
	return $wdb->get_results($stmt);
}

/*
 * Desc : 오늘의 신간, 없을 땐 가장 최근 하루동안의 책 리스트
 */
function lps_get_todays_new( $limit = NULL ) {
	global $wdb;

	$query = "
			SELECT
				created_dt
			FROM
				bt_books
			WHERE
				is_pkg = 'N' AND
				book_status = '3000'
			ORDER BY
				created_dt DESC
			LIMIT
				0, 1
	";
	$stmt = $wdb->prepare( $query );
	$stmt->execute();
	$created_dt = $wdb->get_var($stmt);
	$last_date = substr($created_dt, 0, 10);

	if (empty($last_date)) {
		return false;
	}

	if ( !empty($limit) ) {
		$query = "
			SELECT
				ID
			FROM
				bt_books
			WHERE
				is_pkg = 'N' AND
				book_status = '3000' AND
				DATE_FORMAT(created_dt, '%Y-%m-%d') BETWEEN '$last_date' AND '$last_date'
			ORDER BY
				book_title ASC
			LIMIT
				0, $limit
		";
	} else {
		$query = "
			SELECT
				ID
			FROM
				bt_books
			WHERE
				is_pkg = 'N' AND
				book_status = '3000' AND
				DATE_FORMAT(created_dt, '%Y-%m-%d') BETWEEN '$last_date' AND '$last_date'
			ORDER BY
				book_title ASC
		";
	}
	$stmt = $wdb->prepare( $query );
	$stmt->execute();
	$results = $wdb->get_results($stmt);

	if (!empty($results)) {
		$ids = [];
		foreach ($results as $key => $val) {
			array_push($ids, $val['ID']);
		}
		return $ids;
	} else {
		return false;
	}


}

/*
 * Desc : 책 등록 > 책 리스트에서 검색 > 해당 출판사의 책만 검색
 */
function lps_search_books_by_user( $user_id, $str = NULL ) {
	global $wdb;

	if (!empty($str)) {
		$q = '%' . $str . '%';
		$query = "
			SELECT
				*
			FROM
				bt_books
			WHERE
				user_id = ? AND
				is_pkg = 'N' AND
				book_status = '3000' AND
				book_title LIKE ?
			ORDER BY
				book_title ASC
		";
		$stmt = $wdb->prepare( $query );
		$stmt->bind_param('is', $user_id, $q);
		$stmt->execute();
		return $wdb->get_results($stmt);
	} else {
		return lps_get_books_by_user($user_id);
	}
}

/*
 * Desc : 승인완료된 모든 책 검색
 */
function lps_search_approved_books( $str ) {
	global $wdb;

	$q = '%' . $str . '%';

	$query = "
		SELECT
			*
		FROM
			bt_books
		WHERE
			is_pkg = 'N' AND
			book_status = '3000' AND
			book_title LIKE ?
		ORDER BY
			book_title ASC
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param('s', $q);
	$stmt->execute();
	return $wdb->get_results($stmt);

}

function lps_update_book_meta( $book_id, $meta_key, $meta_value ) {
	global $wdb;

	$query = "
			SELECT
				bmeta_id
			FROM
				bt_books_meta
			WHERE
				book_id = ? AND
				meta_key = ?
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'is', $book_id, $meta_key );
	$stmt->execute();
	$meta_id = $wdb->get_var($stmt);

	if ( empty($meta_id) ) {		// INSERT
		$query = "
				INSERT INTO
					bt_books_meta
					(
						bmeta_id,
						book_id,
						meta_key,
						meta_value
					)
				VALUES
					(
						0, ?, ?, ?
					)
		";
		$stmt = $wdb->prepare( $query );
		$stmt->bind_param( 'iss', $book_id, $meta_key, $meta_value );
		$stmt->execute();

		return $wdb->insert_id;

	} else {		// UPDATE
		$query = "
			UPDATE
				bt_books_meta
			SET
				meta_value = ?
			WHERE
				book_id = ? AND
				meta_key = ?
		";
		$stmt = $wdb->prepare( $query );
		$stmt->bind_param( 'sis', $meta_value, $book_id, $meta_key );
		return $stmt->execute();
	}
}

function lps_get_book_meta( $book_id, $meta_key = NULL ) {
	global $wdb;

	if ( $meta_key ) {
		$query = "
				SELECT
					meta_value
				FROM
					bt_books_meta
				WHERE
					book_id = ? AND
					meta_key = ?
		";
		$stmt = $wdb->prepare( $query );
		$stmt->bind_param( 'is', $book_id, $meta_key );
		$stmt->execute();
		return $wdb->get_var($stmt);
	} else {
		$query = "
				SELECT
					meta_key,
					meta_value
				FROM
					bt_books_meta
				WHERE
					book_id = ?
		";
		$stmt = $wdb->prepare( $query );
		$stmt->bind_param( 'i', $book_id );
		$stmt->execute();

		$data = $wdb->get_results($stmt);
		$array = array();

		if ( !empty($data) ) {
			foreach ( $data as $key => $val ) {
				$array[$val['meta_key']] = $val['meta_value'];
			}
		}
		return $array;
	}
}

/*
 * Desc : meta_key를 제외한 모든 meta value
 */
function lps_get_book_meta_exclude( $book_id, $meta_key ) {
	global $wdb;

	$query = "
			SELECT
				meta_key,
				meta_value
			FROM
				bt_books_meta
			WHERE
				book_id = ? AND
				meta_key <> ?
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'is', $book_id, $meta_key );
	$stmt->execute();

	$data = $wdb->get_results($stmt);
	$array = array();

	if ( !empty($data) ) {
		foreach ( $data as $key => $val ) {
			$array[$val['meta_key']] = $val['meta_value'];
		}
	}
	return $array;
}

/*
 * Desc : 출판사의 책 수정 요청
 * 		컨텐츠의 변경이 없으면 book_status를 2001로 업데이트하지 않습니다. 원래의 책 Data와 사유만 meta table에 기록합니다.
 */
function lps_req_edit_book() {
	global $wdb;

	$book_id = $_POST['book_id'];
	$is_pkg = $_POST['is_pkg'];
	$is_free = empty($_POST['is_free']) ? 'N' : 'Y';
	$book_title = $_POST['book_title'];
	$author = empty($_POST['author']) ? '' : $_POST['author'];
	$publisher = empty($_POST['publisher']) ? '' : $_POST['publisher'];
	$published_dt = empty($_POST['published_dt']) ? '' : $_POST['published_dt'];  // 출간일 추가
	$comics_brand = empty($_POST['comics_brand']) ? '' : $_POST['comics_brand'];  // 코믹스 브랜드 추가
	$published_dt = empty($_POST['published_dt']) ? '' : $_POST['published_dt'];  // 출간일 추가
	$isbn = empty($_POST['isbn']) ? '' : preg_replace('/\D/', '', $_POST['isbn']);
	$normal_price = empty($_POST['normal_price']) ? 0: $_POST['normal_price'];
	$sale_price = empty($_POST['sale_price']) ? 0: $_POST['sale_price'];
	$upload_type = empty($_POST['upload_type']) ? '' : $_POST['upload_type'];
	$period_from = empty($_POST['period_from']) ? NULL : $_POST['period_from'];
	$period_to = empty($_POST['period_to']) ? NULL : $_POST['period_to'];
// 	$user_id = wps_get_current_user_id();

	$book_status = '2001';
	$epub_name = empty($_POST['file_name_epub'][0]) ? '' : $_POST['file_name_epub'][0];
	$epub_path = empty($_POST['file_path_epub'][0]) ? '' : $_POST['file_path_epub'][0];
	$cover_img = empty($_POST['file_url_cover'][0]) ? '' : $_POST['file_url_cover'][0];

	$category_first = empty($_POST['category_first']) ? 0: $_POST['category_first'];
	$category_second = empty($_POST['category_second']) ? 0 : $_POST['category_second'];
	$category_third = empty($_POST['category_third']) ? 0 : $_POST['category_third'];

	// 기존 Data 저장
	$book_rows = lps_get_book($book_id);
	$meta_value_rows = lps_get_book_meta_exclude($book_id, 'lps_book_prev_data');

	$lps_book_prev_data = serialize(array_merge($book_rows, $meta_value_rows));
	lps_update_book_meta($book_id, 'lps_book_prev_data', $lps_book_prev_data);

	$req_reason = $_POST['req_reason'];
	lps_update_book_meta($book_id, 'lps_req_reason_edit', $req_reason);

	$query = "
			UPDATE
				bt_books
			SET
				book_title = ?,
				author = ?,
				publisher = ?,
				isbn = ?,
				normal_price = ?,
				sale_price = ?,
				cover_img = ?,
				epub_path = ?,
				epub_name = ?,
				period_from = ?,
				period_to = ?,
				category_first = ?,
				category_second = ?,
				category_third = ?,
				is_pkg = ?,
				is_free = ?,
				upload_type = ?,
				comics_brand = ?,
				published_dt = ?

			WHERE
				ID = ?
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'ssssiisssssiiisssssi', $book_title, $author, $publisher, $isbn,
			$normal_price, $sale_price, $cover_img, $epub_path, $epub_name,
			$period_from, $period_to, $category_first, $category_second, $category_third,
			$is_pkg, $is_free, $upload_type, $comics_brand, $published_dt ,$book_id
	);
	$stmt->execute();
	$affected_rows = $wdb->affected_rows;

	if (!empty($_POST['introduction_book'])) {
		$meta_key = 'lps_introduction_book';
		$rslt1 = lps_update_book_meta($book_id, $meta_key, $_POST['introduction_book']);
	}
	if (!empty($_POST['introduction_author'])) {
		$meta_key = 'lps_introduction_author';
		$rslt2 = lps_update_book_meta($book_id, $meta_key, $_POST['introduction_author']);
	}
	if (!empty($_POST['publisher_review'])) {
		$meta_key = 'lps_publisher_review';
		$rslt3 = lps_update_book_meta($book_id, $meta_key, $_POST['publisher_review']);
	}
	if (!empty($_POST['book_table'])) {
		$meta_key = 'lps_book_table';
		$rslt4 = lps_update_book_meta($book_id, $meta_key, $_POST['book_table']);
	}

	if ( $affected_rows > 0 || $rslt1 || $rsl2 || $rslt3 || $rslt4 ) {	// 변경되었을 경우에만 상태 변경
		$query = "
				UPDATE
					bt_books
				SET
					book_status = ?,
					req_edit_dt = NOW()
				WHERE
					ID = ?
		";
		$stmt = $wdb->prepare( $query );
		$stmt->bind_param( 'si', $book_status, $book_id );
		$stmt->execute();
	} // .if

	if (!empty($_POST['file_path_epub'])) {
		if (stripos($_POST['file_path_epub'][0], '/tmp/') !== false) {	// 새로운 epub file 업로드 시

			$ymw = date('YmW');
			$upload_real['pdir'] = UPLOAD_PATH . '/books/' . $ymw;
			$upload_real['udir'] = UPLOAD_URL . '/books/' . $ymw;

			if ( !is_dir($upload_real['pdir']) ) {
				mkdir($upload_real['pdir'], 0777, true);
			}

			// EPUB 파일 업데이트 및 이동
			if (!empty($_POST['file_path_epub'][0]) && is_file($_POST['file_path_epub'][0])) {
				$tmp_path = $_POST['file_path_epub'][0];
				$tmp_file = basename($tmp_path);

				$new_path = $upload_real['pdir'] . '/'. $tmp_file;
				$new_url = $upload_real['udir'] . '/' . $tmp_file;

				if (rename($tmp_path, $new_path)) {
					// epub
					$file_path = $new_path;
					$file_url = $new_url;
					$file_name = $_POST['file_name_epub'][0];
					$compact = serialize(compact('file_path', 'file_url', 'file_name'));
					lps_update_book_meta($book_id, 'lps_book_epub_file', $compact);

					$query = "
							UPDATE
								bt_books
							SET
								epub_path = ?,
								epub_name = ?,
								book_status = ?,
								req_edit_dt = NOW(),
								epub_url = ?
							WHERE
								ID = ?
					";
					$stmt = $wdb->prepare( $query );
					$stmt->bind_param( 'ssisi', $file_path, $file_name, $book_status,$file_url, $book_id );
					$stmt->execute();
				}
			}
		} // .if
	}

	if (!empty($_POST['file_path_cover'])) {
		if (stripos($_POST['file_path_cover'][0], '/tmp/') !== false) {		// 새로운 표지이미지 업로드 시

			$ymw = date('YmW');

			error_log($ymw);

			$upload_real['pdir'] = UPLOAD_PATH . '/books/' . $ymw;
			$upload_real['udir'] = UPLOAD_URL . '/books/' . $ymw;

			if ( !is_dir($upload_real['pdir']) ) {
				mkdir($upload_real['pdir'], 0777, true);
			}

			/**
			 * 이미지가 용도별로 사이징 되지 않았음.
			 * 리스팅 기본 이미지, 추천 도서 묶음 이미지, 베스트 영역 이미지등이 조금씩 사이즈 다름
			 * 기본 260 * 400
			 * 메인 베스트 270 * 434
			 * 메인 추천 280 * 430
			 * 영역별로 따로 갈 것인지 검토하자 !
			 */
			// Cover image 업데이트 및 이동
			if (!empty($_POST['file_path_cover'][0]) && is_file($_POST['file_path_cover'][0])) {

				$tmp_path = $_POST['file_path_cover'][0];
				$tmp_file = basename($tmp_path);

				$new_path = $upload_real['pdir'] . '/'. $tmp_file;
				$new_url = $upload_real['udir'] . '/' . $tmp_file;

				// Thumbnail 경로를 기본 이미지 경로로 잡는다. 변경 가능함
				$tmp_thumb_path = $_POST['file_thumb_path_cover'][0];
				$tmp_thumb_file = basename($tmp_thumb_path);

				$new_thumb_path = $upload_real['pdir'] . '/'. $tmp_thumb_file;
				$new_thumb_url = $upload_real['udir'] . '/' . $tmp_thumb_file;


				if (rename($tmp_path, $new_path) && rename($tmp_thumb_path, $new_thumb_path)) {
					// cover
					// $file_url = $new_url;    // 원본 url
					// $file_path = $new_path;  // 원본 경로
					$file_path = $new_thumb_path;  // thumbnail 경로
					$file_url = $new_thumb_url;    // thumbnail url

					$file_name = $_POST['file_name_cover'][0];
					$compact = serialize(compact('file_path', 'file_url', 'file_name'));
					lps_update_book_meta($book_id, 'lps_book_cover_file', $compact);

					$query = "
							UPDATE
								bt_books
							SET
								cover_img = ?,
								book_status = ?,
								req_edit_dt = NOW()
							WHERE
								ID = ?
					";
					$stmt = $wdb->prepare( $query );
					$stmt->bind_param( 'sii', $file_url, $book_status, $book_id );
					$stmt->execute();
				}
			}
		} // .if
	}

	// 세트
	if (!strcmp($is_pkg, 'Y')) {
		$meta_key = 'lps_pkg_book_list';
		$meta_value = serialize($_POST['pkg_books']);
		lps_update_book_meta($book_id, $meta_key, $meta_value);
	}

	return $book_id;
}

/*
 * Desc : 춮판사 책 삭제 요청
 */
function lps_req_delete_book() {
	global $wdb;

	$book_id = $_POST['book_id'];
	$req_reason = $_POST['req_reason'];
	$book_status = '2101';

	$book_rows = lps_get_book($book_id);
	$book_prev_status = $book_rows['book_status'];

	lps_update_book_meta($book_id, 'lps_req_reason_delete', $req_reason);
	lps_update_book_meta($book_id, 'lps_req_prev_status', $book_prev_status);

	$query = "
			UPDATE
				bt_books
			SET
				book_status = ?,
				req_del_dt = NOW()
			WHERE
				ID = ?
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'si', $book_status, $book_id );
	return $stmt->execute();
}

/*
 * Desc : 책에 대한 등록 승인, 등록 거절, 수정 승인, 수정 거절, 삭제 승인, 삭제 거절
 */
function lps_edit_book_status() {
	global $wdb;

	$id = $_POST['id'];
	$type = $_POST['type'];
	$reason = empty($_POST['res_reason']) ? '' : $_POST['res_reason'];
	if (stripos($type, 'accept_') !== false ) {		// 승인
		return lps_accept_book_request($id, $type);
	} else if (stripos($type, 'reject_') !== false ) {		// 거절
		return lps_reject_book_request($id, $type, $reason);
	}

	return 0;
}

/*
 * Desc : 승인
 */
function lps_accept_book_request($book_id, $type) {
	global $wdb;

	if (!strcmp($type, 'accept_delete')) {
		$status = '8080';
	} else {
		$status = '3000';
	}

	$query = "
			UPDATE
				bt_books
			SET
				book_status = ?
			WHERE
				ID = ?
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'si', $status, $book_id );
	$result = $stmt->execute();

	if ($result) {
		$admin_id = wps_get_current_user_id();
		$accept_dt = time();
		$meta_key = 'lps_res_' . $type;
		$meta_value = serialize(compact('admin_id', 'accept_dt'));
		return lps_update_book_meta($book_id, $meta_key, $meta_value);
	} else {
		return 0;
	}
}

/*
 * Desc : 거절
 */
function lps_reject_book_request($book_id, $type, $reason) {
	global $wdb;

	if (!strcmp($type, 'reject_new')) {
		$status_code = 4000;
	} else if (!strcmp($type, 'reject_edit')) {
		$status_code = 4001;
	} else if (!strcmp($type, 'reject_delete')) {
		$status_code = 4101;
	}

	$query = "
			UPDATE
				bt_books
			SET
				book_status = ?
			WHERE
				ID = ?
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'ii', $status_code, $book_id );
	$result = $stmt->execute();

	if ($result) {
		$admin_id = wps_get_current_user_id();
		$reject_dt = time();
		$reject_reason = $reason;
		$meta_key = 'lps_res_' . $type;
		$meta_value = serialize(compact('admin_id', 'reject_dt', 'reject_reason'));
		return lps_update_book_meta($book_id, $meta_key, $meta_value);
	} else {
		return 0;
	}
}

/*
 * Desc : 비교
 */
function lps_compare_both_data($str1, $str2) {
	if (strcasecmp($str1, $str2)) {
		return $str1 . ' <code><i class="fa fa-arrow-circle-right"></i></code> ' . '<span class="label label-info" style="font-size: 100%;">' . $str2 . '</span>';
	} else {
		return $str2;
	}
}

/*
 * Desc : ISBN 조회
 */
function lps_find_isbn($isbn) {
	global $wdb;

	$query = "
			SELECT
				ID
			FROM
				bt_books
			WHERE
				isbn = ?
	";
	$stmt = $wdb->prepare($query);
	$stmt->bind_param('s', $isbn);
	$stmt->execute();
	return $wdb->get_var($stmt);
}

function lps_get_books_by_status( $status, $limit = 10 ) {
	global $wdb;

	$query = "
		SELECT
			*
		FROM
			bt_books
		WHERE
			is_pkg = 'N' AND
			book_status = ?
		ORDER BY
			ID DESC
		LIMIT
			0, $limit
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param('s', $status);
	$stmt->execute();
	return $wdb->get_results($stmt);
}

function lps_get_todays_new_book( $limit = NULL ) {
	$todays_new_book = wps_get_option( 'lps_todays_new_book' );		// 관리자 등록 책

	if (!empty($todays_new_book)) {
		$unserial = unserialize($todays_new_book);
		if (empty($unserial)) {		// 관리자 등록 책 없을 경우 Default 책
			$todays_new = lps_get_todays_new( $limit );
		} else {
			$todays_new = $unserial;
		}
	} else {
		$todays_new = lps_get_todays_new( $limit );
	}
	return $todays_new;
}

/*
 * Desc : 오늘의 신간
 */
function lps_get_todays_new_book_part( $page = 0 ) {
	$slice = 12;
	$start = $page * $slice;
	$end = $start + $slice;

	$todays_new_book = wps_get_option( 'lps_todays_new_book' );		// 관리자 등록 책

	if (!empty($todays_new_book)) {
		$unserial = unserialize($todays_new_book);
		if (empty($unserial)) {		// 관리자 등록 책 없을 경우 Default 책
			$todays_new = lps_get_todays_new();
		} else {
			$todays_new = $unserial;
		}
	} else {
		$todays_new = lps_get_todays_new();
	}

	$total_count = count($todays_new);
	$is_next = $total_count > $end ? 'Y' : 'N';
	$page++;

	$today_slice = array_slice($todays_new, $start, $slice);

	return compact('today_slice', 'is_next', 'page');
}

/*
 * Desc : 장르별 도서, 30개씩
 */
function lps_get_genre_book_part( $page = 0, $category = NULL ) {
	global $wdb;

	$slice = 4;
	$start = $page * $slice;
	$end = $start + $slice;

	$sql = empty($category) ? '' : " AND category_second = '$category'";

	$query = "
			SELECT
				SQL_CALC_FOUND_ROWS
				*
			FROM
				bt_books
			WHERE
				book_status = '3000'
				$sql
			ORDER BY
				ID DESC
			LIMIT
				$start, $slice
	";
	$stmt = $wdb->prepare( $query );
	$stmt->execute();
	$results = $wdb->get_results($stmt);

	$query = "SELECT FOUND_ROWS() AS total_rows";
	$stmt = $wdb->prepare( $query );
	$stmt->execute();
	$total_count = $wdb->get_var($stmt);

	if (!empty($results)) {
		$is_next = $total_count > $end ? 'Y' : 'N';
	} else {
		$is_next = 'N';
	}
	$page++;

	return compact('results', 'is_next', 'page', 'total_count');
}

/*
 * Desc : 무료 도서, 30개씩
 */
function lps_get_free_book_part( $page = 0, $category = NULL ) {
	global $wdb;

	$slice = 4;
	$start = $page * $slice;
	$end = $start + $slice;

	$sql = empty($category) ? '' : " AND category_second = '$category'";

	$query = "
			SELECT
				SQL_CALC_FOUND_ROWS
				*
			FROM
				bt_books
			WHERE
				book_status = '3000' AND
				is_free = 'Y'
				$sql
			ORDER BY
				ID DESC
			LIMIT
				$start, $slice
	";
	$stmt = $wdb->prepare( $query );
	$stmt->execute();
	$results = $wdb->get_results($stmt);

	$query = "SELECT FOUND_ROWS() AS total_rows";
	$stmt = $wdb->prepare( $query );
	$stmt->execute();
	$total_count = $wdb->get_var($stmt);

	if (!empty($results)) {
		$is_next = $total_count > $end ? 'Y' : 'N';
	} else {
		$is_next = 'N';
	}
	$page++;

	return compact('results', 'is_next', 'page', 'total_count');
}

/*
 * Desc : 책을 구입한 회원인 지 여부 확인
 */
function lps_has_book_user( $book_id, $user_id ) {
	global $wdb;

	$query = "
			SELECT
				o.order_id
			FROM
				bt_order AS o
			INNER JOIN
				bt_order_item AS i
			WHERE
				o.user_id = ? AND
				o.order_id = i.order_id AND
				i.book_id = ?
			LIMIT
				0, 1
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'ii', $user_id, $book_id );
	$stmt->execute();
	return $wdb->get_var($stmt);
}

/*
 * Desc : 책의 소유권을 가진 작가 혹은 출판사인지 확인
 */
function lps_is_book_owner( $book_id, $user_id ) {
	global $wdb;

// 	$user_id = wps_get_current_user_id();

	$query = "
		SELECT
			ID
		FROM
			bt_books
		WHERE
			ID = ? AND
			user_id = ?
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param('ii', $book_id, $user_id);
	$stmt->execute();
	return $wdb->get_var($stmt);
}

/*
 * Desc : 함께 구매한 책 (특정 책을 주문 시 함께 구매한 책 리스트)
 */
function lps_get_books_by_cart( $book_id ) {
	global $wdb;

	$query = "
		SELECT
			book_id
		FROM
			bt_order_item
		WHERE
			book_id <> ? AND
			order_id IN (SELECT order_id FROM bt_order_item WHERE book_id = ?)
		LIMIT
			0, 9
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param('ii', $book_id, $book_id);
	$stmt->execute();
	return $wdb->get_results($stmt);
}


/*
 * Desc : 무료 책인지 확인
 */
function lps_is_free_book( $book_id ) {
	global $wdb;

	$query = "
		SELECT
			is_free
		FROM
			bt_books
		WHERE
			ID = ?
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param('i', $book_id);
	$stmt->execute();
	$is_free = $wdb->get_var($stmt);

	return strcmp($is_free, 'N') ? true : false;
}

/*
 * Desc : 특정 책을 구입한 독자 리스트 (user_id 제외)
 *
 * 친구인지 아닌지 확인하는 SQL
SELECT
	u.ID,
    u.user_name,
    u.display_name,
    IF(f.initiator_user_id = 7 OR f.friend_user_id = 7 AND f.is_confirmed = '1', 'F', 'N'),
    f.*
FROM
	bt_order AS o
INNER JOIN
	bt_order_item AS i
INNER JOIN
	bt_users AS u
LEFT JOIN
	bt_friends AS f
ON
	u.ID = f.initiator_user_id OR
    u.ID = f.friend_user_id
WHERE
	i.book_id = 19 AND
	u.ID <> 7 AND
	o.order_id = i.order_id AND
    o.user_id = u.ID
;

 */
function lps_get_user_lists_by_book( $book_id, $user_id ) {
	global $wdb;

	$query = "
			SELECT
				u.ID,
			    u.user_name,
			    u.display_name,
				m.meta_value AS profile_avatar
			FROM
				bt_order AS o
			INNER JOIN
				bt_order_item AS i
			INNER JOIN
				bt_users AS u
			LEFT JOIN
				bt_users_meta AS m
			ON
				u.ID = m.user_id AND
				m.meta_key = 'wps_user_avatar'
			WHERE
				i.book_id = ? AND
				u.ID <> ? AND
				o.order_id = i.order_id AND
			    o.user_id = u.ID
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param('ii', $book_id, $user_id);
	$stmt->execute();
	$results = $wdb->get_results($stmt);

	if ( !empty($results) ) {
		foreach ($results as $key => $val) {
			$friend_id = $val['ID'];
			$is_confirmed = lps_get_friend_status( $user_id, $friend_id);

			if ($is_confirmed == '1') {
				$results[$key]['friend_status'] = 'F';
			} else if ($is_confirmed == '0') {
				$results[$key]['friend_status'] = 'W';
			} else {
				$results[$key]['friend_status'] = 'N';
			}
		}
	}
	return $results;
}

/*
 * Desc : 회원의 특정 책을 읽은 최종 시간
 */
function lps_get_latest_read_dt( $book_id, $user_id ) {
	global $wdb;

	$query = "
			SELECT
				read_dt_to
			FROM
				bt_user_book_read
			WHERE
				user_id = ? AND
				book_id = ?
			ORDER BY
				ID DESC
			LIMIT
				0, 1
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param('ii', $user_id, $book_id);
	$stmt->execute();
	return $wdb->get_var($stmt);
}

/*
 * Desc : 현재시각 대비 1시간 전인 지 비교한다.
 * 	$dt 포멧은 yyyy-mm-dd hh:ii:ss
 */
function lps_is_one_hour_ago( $dt ) {
	$dt_time = strtotime($dt) + 3600;

	return $dt_time > time() ? false : true;
}

/*
 * Desc : 회원이 가장 마지막에 읽은 책 book_id
 */
function lps_get_last_read_book( $user_id ) {
	global $wdb;

	$query = "
			SELECT
				*
			FROM
				bt_user_book_read
			WHERE
				user_id = ?
			ORDER BY
				ID DESC
			LIMIT
				0, 1
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param('i', $user_id);
	$stmt->execute();
	return $wdb->get_row($stmt);
}

/*
 * Desc : 내가 많이 읽은 장르
 */
function lps_get_my_most_read_genre( $user_id ) {
	global $wdb;

	$query = "
			SELECT
			    b.category_second,
			    COUNT(*) AS sub_total
			FROM
				bt_books AS b
			INNER JOIN
				bt_user_book_read AS br
			WHERE
				br.user_id = ? AND
				b.ID = br.book_id
			GROUP BY
				b.category_second
			ORDER BY
				sub_total DESC
			LIMIT
				0, 1
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param('i', $user_id);
	$stmt->execute();
	return $wdb->get_row($stmt);
}

/*
 * Desc : 장르에 속한 책들
 */
function lps_get_my_most_genre_books( $genre_id, $limit = 4 ) {
	global $wdb;

	$query = "
			SELECT
				*
			FROM
				bt_books
			WHERE
				book_status = '3000' AND
				category_second = ?
			ORDER BY
				ID DESC
			LIMIT
				0, $limit
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param('i', $genre_id);
	$stmt->execute();
	return $wdb->get_results($stmt);
}

/*
 * Desc : 내가 많이 읽은 작가
 */
function lps_get_my_most_read_author( $user_id ) {
	global $wdb;

	$query = "
			SELECT
				b.author,
				COUNT(*) AS sub_total
			FROM
				bt_books AS b
			INNER JOIN
				bt_user_book_read AS br
			WHERE
				br.user_id = ? AND
				b.ID = br.book_id
			GROUP BY
				b.author
			ORDER BY
				sub_total DESC
			LIMIT
				0, 1
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param('i', $user_id);
	$stmt->execute();
	return $wdb->get_row($stmt);
}

/*
 * Desc : 내가 많이 읽은 작가의 다른 책
 */
function lps_get_my_most_author_books( $author, $limit = 4 ) {
	global $wdb;

	$query = "
			SELECT
				*
			FROM
				bt_books
			WHERE
				book_status = '3000' AND
				author LIKE ?
			ORDER BY
				ID DESC
			LIMIT
				0, $limit
	";
	$stmt = $wdb->prepare( $query );
	$author = '%' . $author . '%';
	$stmt->bind_param('s', $author);
	$stmt->execute();
	return $wdb->get_results($stmt);
}

/*
 * Desc : 내가 많이 읽은 출판사
 */
function lps_get_my_most_read_publisher( $user_id ) {
	global $wdb;

	$query = "
			SELECT
				b.publisher,
				COUNT(*) AS sub_total
			FROM
				bt_books AS b
			INNER JOIN
				bt_user_book_read AS br
			WHERE
				br.user_id = ? AND
				b.ID = br.book_id
			GROUP BY
				b.publisher
			ORDER BY
				sub_total DESC
			LIMIT
				0, 1
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param('i', $user_id);
	$stmt->execute();
	return $wdb->get_row($stmt);
}

/*
 * Desc : 내가 많이 읽은 출판사의 다른 책
 */
function lps_get_my_most_publisher_books( $publisher, $limit = 4 ) {
	global $wdb;

	$query = "
			SELECT
				*
			FROM
				bt_books
			WHERE
				book_status = '3000' AND
				publisher LIKE ?
			ORDER BY
				ID DESC
			LIMIT
				0, $limit
	";
	$stmt = $wdb->prepare( $query );
	$publisher = '%' . $publisher . '%';
	$stmt->bind_param('s', $publisher);
	$stmt->execute();
	return $wdb->get_results($stmt);
}

/*
 * Desc : 내 친구들이 많이 읽은 책
 */
function lps_get_my_friends_books( $friend_ids, $limit = 4 ) {
	global $wdb;

	$friends = implode(',', $friend_ids);

	$query = "
			SELECT
				DISTINCT
				b.ID,
				b.book_title,
			    b.publisher,
			    b.author,
			    b.cover_img,
			    b.sale_price
			FROM
				bt_books AS b
			LEFT JOIN
				bt_user_book_read AS br
			ON
				b.ID = br.book_id AND
			    br.user_id IN ($friends)
			WHERE
				b.book_status = '3000'
			ORDER BY
				b.ID DESC
			LIMIT
				0, $limit
	";
	$stmt = $wdb->prepare( $query );
	$stmt->execute();
	return $wdb->get_results($stmt);
}

/*
 * Desc : 베스트 > 스테디셀러
 */
function lps_get_best_steady_books( $books = NULL ) {
	global $wdb;

	if ( !empty($books) ) {

		$ids = "'" . implode("','", $books) . "'";

		$query = "
				SELECT
					b.ID,
					b.book_title,
					b.author,
					b.publisher,
					b.sale_price,
					b.cover_img,
				    100 AS count
				FROM
					bt_books AS b
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
					b.publisher,
					b.sale_price,
					b.cover_img,
				    COUNT(i.book_id) AS count
				FROM
					bt_order_item AS i
				INNER JOIN
					bt_books AS b
				WHERE
					i.book_id = b.ID
				GROUP BY
					b.ID
				ORDER BY
					count DESC
				LIMIT
					0, 30
		";
	}
	$stmt = $wdb->prepare( $query );
	$stmt->execute();
	return $wdb->get_results($stmt);
}

/*
 * Desc : 베스트 > 기간
 */
function lps_get_best_period_books( $period, $books = NULL ) {
	global $wdb;

	$to = date('Y-m-d');

	if (!strcmp($period, 'day')) {
		$from = date('Y-m-d', time() - 86400);	// a day ago
		$limit = 10;
	} else if (!strcmp($period, 'week')) {
		$from = date('Y-m-d', time() - 86400 * 7);	// a week ago
		$limit = 15;
	} else if (!strcmp($period, 'month')) {
		$from = date('Y-m-d', time() - 86400 * 30);	// a month ago
		$limit = 20;
	}

	if ( !empty($books) ) {
		$ids = "'" . implode("','", $books) . "'";

		$query = "
				SELECT
					b.ID,
					b.book_title,
					b.author,
					b.publisher,
					b.sale_price,
					b.cover_img,
					100 AS count
				FROM
					bt_books AS b
				WHERE
					ID IN ( $ids )
				ORDER BY
					FIELD(ID, $ids)
		";
		$stmt = $wdb->prepare( $query );
	} else {
		$query = "
				SELECT
					b.ID,
					b.book_title,
					b.author,
					b.publisher,
					b.sale_price,
					b.cover_img,
				    COUNT(i.book_id) AS count
				FROM
					bt_order AS o
				INNER JOIN
					bt_order_item AS i
				INNER JOIN
					bt_books AS b
				WHERE
					o.order_id = i.order_id AND
					i.book_id = b.ID AND
				    o.created_dt BETWEEN '$from' AND '$to'
				GROUP BY
					b.ID
				ORDER BY
					count DESC
				LIMIT
					0, $limit
		";
	}
	$stmt = $wdb->prepare( $query );
	$stmt->execute();
	return $wdb->get_results($stmt);
}

/*
 * Desc : 베스트 > 장르별
 */
function lps_get_best_genre_books( $term_id, $books = NULL ) {
	global $wdb;

	if ( !empty($books) ) {

		$ids = "'" . implode("','", $books) . "'";

		$query = "
				SELECT
					b.ID,
					b.book_title,
					b.author,
					b.publisher,
					b.sale_price,
					b.cover_img,
					100 AS count
				FROM
					bt_books AS b
				WHERE
					ID IN ( $ids )
				ORDER BY
					FIELD(ID, $ids)
		";
		$stmt = $wdb->prepare( $query );
	} else {
		$query = "
				SELECT
					b.ID,
					b.book_title,
					b.author,
					b.publisher,
					b.sale_price,
					b.cover_img,
				    COUNT(i.book_id) AS count
				FROM
					bt_order_item AS i
				INNER JOIN
					bt_books AS b
				WHERE
					b.category_first = ? AND
					i.book_id = b.ID
				GROUP BY
					b.ID
				ORDER BY
					count DESC
				LIMIT
					0, 10
		";
		$stmt = $wdb->prepare( $query );
		$stmt->bind_param('i', $term_id);
	}
	$stmt->execute();
	return $wdb->get_results($stmt);
}

/*
 * Desc : 내가 구매한 책인 지 여부 확인
 */
function lps_is_my_book( $user_id, $book_id ) {
	global $wdb;

	$query = "
			SELECT
				i.item_id,
				i.epub_url
			FROM
				bt_order AS o
			INNER JOIN
				bt_order_item AS i
			WHERE
				o.user_id = ? AND
			    i.book_id = ? AND
				o.order_id = i.order_id
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param('ii', $user_id, $book_id);
	$stmt->execute();
	return $wdb->get_row($stmt);
}

/*
 * Desc : 회원이 읽은 책에 대한 정보 중 가장 최근 데이터
 */
function lps_get_read_book_data_by_user( $user_id, $book_id ) {
	global $wdb;

	$query = "
			SELECT
				*
			FROM
				bt_user_book_read
			WHERE
				user_id = ? AND
				book_id = ?
			ORDER BY
				ID DESC
			LIMIT
				0, 1
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param('ii', $user_id, $book_id);
	$stmt->execute();
	return $wdb->get_row($stmt);
}

/*
 * Desc : 베스트 장르 Admin Dashboard
 */
function lps_get_best_genre_book() {
	global $wdb;

	$user_id = wps_get_current_user_id();
	$user_level = wps_get_user_level();

	if ($user_level < 10) {		// 출판사, 작가
		$query = "
				SELECT
					t.name,
					COUNT(*) AS scount
				FROM
					bt_books AS b
				INNER JOIN
					bt_order_item AS i
				INNER JOIN
					bt_terms AS t
				WHERE
					b.category_second = t.term_id AND
				    b.ID = i.book_id AND
				    b.publisher_id = '$user_id'
				GROUP BY
					category_second
				ORDER BY
					scount DESC
				LIMIT
					0, 1
		";
	} else {
		$query = "
				SELECT
					t.name,
					COUNT(*) AS scount
				FROM
					bt_books AS b
				INNER JOIN
					bt_order_item AS i
				INNER JOIN
					bt_terms AS t
				WHERE
					b.category_second = t.term_id AND
				    b.ID = i.book_id
				GROUP BY
					category_second
				ORDER BY
					scount DESC
				LIMIT
					0, 1
		";
	}
	$stmt = $wdb->prepare( $query );
	$stmt->execute();
	return $wdb->get_row($stmt);
}

/*
 * Desc : 베스트 단품
 */
function lps_get_best_selling_book() {
	global $wdb;

	$user_id = wps_get_current_user_id();
	$user_level = wps_get_user_level();

	if ($user_level < 10) {		// 출판사, 작가
		$query = "
				SELECT
					b.book_title,
				    COUNT(*) AS scount
				FROM
					bt_books AS b
				INNER JOIN
					bt_order_item AS i
				WHERE
					b.is_pkg = 'N' AND
				    b.book_status = '3000' AND
					b.ID = i.book_id AND
					b.publisher_id = '$user_id'
				GROUP BY
					b.ID
				ORDER BY
					scount DESC
				LIMIT
					0, 1
		";
	} else {
		$query = "
				SELECT
					b.book_title,
				    COUNT(*) AS scount
				FROM
					bt_books AS b
				INNER JOIN
					bt_order_item AS i
				WHERE
					b.is_pkg = 'N' AND
				    b.book_status = '3000' AND
					b.ID = i.book_id
				GROUP BY
					b.ID
				ORDER BY
					scount DESC
				LIMIT
					0, 1
		";
	}
	$stmt = $wdb->prepare( $query );
	$stmt->execute();
	return $wdb->get_row($stmt);
}

/*
 * Desc : 베스트 세트
 */
function lps_get_best_selling_set() {
	global $wdb;

	$user_id = wps_get_current_user_id();
	$user_level = wps_get_user_level();

	if ($user_level < 10) {		// 출판사, 작가
		$query = "
				SELECT
					b.book_title,
				    COUNT(*) AS scount
				FROM
					bt_books AS b
				INNER JOIN
					bt_order_item AS i
				WHERE
					b.is_pkg = 'Y' AND
				    b.book_status = '3000' AND
					b.ID = i.book_id AND
					b.publisher_id = '$user_id'
				GROUP BY
					b.ID
				ORDER BY
					scount DESC
				LIMIT
					0, 1
		";
	} else {
		$query = "
				SELECT
					b.book_title,
				    COUNT(*) AS scount
				FROM
					bt_books AS b
				INNER JOIN
					bt_order_item AS i
				WHERE
					b.is_pkg = 'Y' AND
				    b.book_status = '3000' AND
					b.ID = i.book_id
				GROUP BY
					b.ID
				ORDER BY
					scount DESC
				LIMIT
					0, 1
		";
	}
	$stmt = $wdb->prepare( $query );
	$stmt->execute();
	return $wdb->get_row($stmt);
}

/*
 * Desc : 커뮤니티 랭킹 1위
 */
function lps_get_best_ranking_book() {
	global $wdb;

	$user_id = wps_get_current_user_id();
	$user_level = wps_get_user_level();

	if ($user_level < 10) {		// 출판사, 작가
		$query = "
				SELECT
					b.book_title,
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
				WHERE
					b.publisher_id = '$user_id' AND
					b.book_status = '3000'
				GROUP BY
					1
				ORDER BY
					sub_count DESC
				LIMIT
					0, 1
		";
	} else {
		$query = "
				SELECT
					b.book_title,
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
				WHERE
					b.book_status = '3000'
				GROUP BY
					1
				ORDER BY
					sub_count DESC
				LIMIT
					0, 1
		";
	}
	$stmt = $wdb->prepare( $query );
	$stmt->execute();
	return $wdb->get_row($stmt);
}

/*
 * Desc :  승인 완료된 책
 */
function lps_get_total_accepted_book() {
	global $wdb;

	$user_id = wps_get_current_user_id();
	$user_level = wps_get_user_level();

	if ($user_level < 10) {		// 출판사, 작가
		$query = "
				SELECT
					COUNT(*)
				FROM
					bt_books
				WHERE
					publisher_id = '$user_id' AND
					book_status = '3000'
		";
	} else {
		$query = "
				SELECT
					COUNT(*)
				FROM
					bt_books
				WHERE
					book_status = '3000'
		";
	}
	$stmt = $wdb->prepare( $query );
	$stmt->execute();
	return $wdb->get_var($stmt);
}

/*
 * Desc : 등록 요청 중인 책
 */
function lps_get_total_waiting_new_book() {
	global $wdb;

	$user_id = wps_get_current_user_id();
	$user_level = wps_get_user_level();

	if ($user_level < 10) {		// 출판사, 작가
		$query = "
				SELECT
					COUNT(*)
				FROM
					bt_books
				WHERE
					publisher_id = '$user_id' AND
					book_status = '1000'
		";
	} else {
		$query = "
				SELECT
					COUNT(*)
				FROM
					bt_books
				WHERE
					book_status = '1000'
		";
	}
	$stmt = $wdb->prepare( $query );
	$stmt->execute();
	return $wdb->get_var($stmt);
}

/*
 * Desc : 수정, 삭제 요청 중인 책
 */
function lps_get_total_waiting_update_book() {
	global $wdb;

	$user_id = wps_get_current_user_id();
	$user_level = wps_get_user_level();

	if ($user_level < 10) {		// 출판사, 작가
		$query = "
				SELECT
					COUNT(*)
				FROM
					bt_books
				WHERE
					publisher_id = '$user_id' AND
					book_status IN ('2001', '2101')
		";
	} else {
		$query = "
				SELECT
					COUNT(*)
				FROM
					bt_books
				WHERE
					book_status IN ('2001', '2101')
		";
	}
	$stmt = $wdb->prepare( $query );
	$stmt->execute();
	return $wdb->get_var($stmt);
}



/**
 * 북트레일러 등록
 * 북트레일러는 책정보와 연결 하지 않는다. book_id 는 향후 활용
 * 트레일러 url 은 www.youtube.com/embed/$vidio_id 의 형식으로 생성 한다.
 *
 */
function lps_add_book_trailer(){
	global $wdb;
	/**
	 * youtube url 로 비디오 아이디 구한다. for embede url
	 * https://www.youtube.com/embede/$v_id ;  로 embede url 을 만들어서 저장한다..
	 * 프론트 코딩에 적용되도록 url 변경하고 원래 url 은 버린다..
	 */

	$url = $_POST['trailer_url'];
	parse_str( parse_url( $url, PHP_URL_QUERY ), $my_array_of_vars );
	$v_id =  $my_array_of_vars['v'];

	$comics_brand = empty($_POST['comics_brand']) ? '1' : $_POST['comics_brand'];
	$trailer_title = $_POST['trailer_title'];
	$trailer_desc = $_POST['trailer_desc'];
	$trailer_url = "https://www.youtube.com/embed/" .$v_id;

	$open_yn = empty($_POST['open_yn']) ? 'N' : $_POST['open_yn'];

	$user_id = wps_get_current_user_id();

	$query = "
			INSERT INTO
				bt_book_trailers
				(
					ID, trailer_url, trailer_title, trailer_desc, comics_brand, open_yn, user_id, created_dt, updated_dt
				)
			VALUES
				(
					NULL, ?, ?, ?, ?, ?, ?, NOW(), NOW()
				)
	";

	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'sssssi', $trailer_url, $trailer_title, $trailer_desc, $comics_brand, $open_yn, $user_id );
	$stmt->execute();

	$ID = $wdb->insert_id;

	return $ID;

}

/**
 * @param $trailer_id
 * @return array
 *
 * 북트레일러의 상세 정보를 조회 한다.
 */
function lps_get_book_trailer($trailer_id){
	global $wdb;

	$query = " SELECT * FROM bt_book_trailers WHERE id = ? ";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'i', $trailer_id );
	$stmt->execute();

	return $wdb->get_row($stmt);
}

/**
 * @param $trailer_id
 * @return mixed
 *
 * 북트레일러 정보를 업데이트 한다.
 */

function lps_update_book_trailer($trailer_id){
	global $wdb;

	$trailer_id = $_POST['trailer_id'];
	$trailer_title = $_POST['trailer_title'];
	$trailer_desc = $_POST['trailer_desc'];
	$comics_brand = $_POST['comics_brand'];
	$open_yn = $_POST['open_yn'];

	/**
	 * for embed url embed url 을 만들어야 함.
	 *
	 * 먼저 등록된 url 이 www.youtube.com/embed/ 형식인지 확인하고
	 * www.youtube.com/embed/ 형식이 아니면, video id 를 추출해서 /embed/ 형식으로 변경해서 저장.
	 */

	$url = $_POST['trailer_url'];

	if ( !preg_match( '/embed/' , $url) ) {
		parse_str( parse_url( $url, PHP_URL_QUERY ), $my_array_of_vars );
		$v_id =  $my_array_of_vars['v'];
		$trailer_url = "https://www.youtube.com/embed/" .$v_id;
	} else {
		$trailer_url = $url;
	}

	// prepare query
	$query = "
			UPDATE
				bt_book_trailers
			SET
				trailer_title=?,
				trailer_desc=?,
				trailer_url=?,
				comics_brand=?,
				open_yn=?,
				updated_dt=NOW()
			WHERE
			 id =?
	";

	$stmt = $wdb->prepare( $query );

	// bind param
	$stmt->bind_param( 'sssssi', $trailer_title, $trailer_desc, $trailer_url, $comics_brand, $open_yn, $trailer_id);
	$stmt->execute();
	error_log(mysqli_stmt_error($stmt));

	//파일 생성 로직
	return $trailer_id;

}


function lps_delete_book_trailer($trailer_id){
	global $wdb;

	// 해당 아이디의 북 트레일러 삭제
	$query = "
				DELETE FROM
					bt_book_trailers
				WHERE
					ID = ?
			";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'i', $trailer_id );
	$stmt->execute();

	return $wdb->affected_rows;

}



/**
 * @return mixed
 * 전자책 미리보기 파일 등록 ( 1개씩 등록 )
 */
function lps_book_preview_add() {
	global $wdb;

	$book_id = $_POST['book_id'];
	$preview_seq = $_POST['preview_seq'];
	//$open_yn = empty($_POST['open_yn']) ? 'N' : $_POST['open_yn'];

	$user_id = wps_get_current_user_id();

	// File Attachment

	if ( !empty($_FILES['attachment']['name']) ) {

		$upload_dir = UPLOAD_PATH . '/preview/' . $book_id .'/' ;
		$upload_url = UPLOAD_URL . '/preview/'. $book_id .'/' ;

		/**
		 * thumb 디렉토리 분리 안하고 같은 디렉토리로 사용
		 * $upload_dir_thumb = $upload_dir . 'thumb';
		 * $upload_url_thumb = $upload_url . 'thumb';
		 *
		 * if ( !is_dir($upload_dir_thumb) ) {
		 *  mkdir($upload_dir_thumb, 0777, true);
		 * }
		 *
		 */

		//$meta_value = array();


		if ( !is_dir($upload_dir) ) {
			mkdir($upload_dir, 0777, true);
		}

		$wps_thumbnail = new WpsThumbnail();

		foreach ( $_FILES['attachment']['name'] as $key => $val ) {
			$file_ext = strtolower(pathinfo( $val, PATHINFO_EXTENSION ));

			if ( in_array($file_ext, unserialize(WPS_IMAGE_EXT)) ) {
				$new_file_name = wps_make_rand() . '.' . $file_ext;
			} else {
				$new_file_name = wps_make_rand();
			}

			$new_val['file_path'] = $upload_dir .  $new_file_name;
			$new_val['file_url'] = $upload_url .  $new_file_name;
			$preview_img_url = $upload_url .  $new_file_name;

			$new_val['file_name'] = $val;
			$new_val['file_size'] = $_FILES['attachment']['size'][$key];
			$new_val['file_type'] = $_FILES['attachment']['type'][$key];
			$result = move_uploaded_file( $_FILES['attachment']['tmp_name'][$key], $new_val['file_path'] );

			$thumb_suffix = '-thumb';
			$thumb_width = 360 ;
			//$thumb_height = isset($_POST['theight']) ? $_POST['theight'] : 0;	// Null이 가능함
			$thumb_name = $wps_thumbnail->resize_image( $new_val['file_path'], $thumb_suffix, $thumb_width );
			$thumb_path[$key] = $upload_dir .  $thumb_name;
			$thumb_url[$key] = $upload_url .  $thumb_name;

			//array_push($meta_value, $new_val);

		}
		//$meta_value = serialize( $meta_value );
		//wps_update_post_meta( $ID, 'wps-post-attachment', $meta_value );
	}


	$query = "
			INSERT INTO
				bt_books_preview
				(
					ID,
					book_id,
					preview_seq,
					preview_img_url,
					user_id,
					created_dt

				)
			VALUES
				(
					NULL,
					?,
					?,
					?,
					?,
					NOW()
				)
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'issi',
		$book_id,
		$preview_seq,
		$preview_img_url,
		$user_id
	);

	$stmt->execute();

	$ID = $wdb->insert_id;

	return $ID;
}


function lps_update_book_download_time( $user_id, $book_id ) {

}


function lps_add_book_preview() {
    global $wdb;
    $book_id = $_POST['book_id'];
    $preview_seq = $_POST['preview_seq'];

    $create_id = wps_get_current_user_id();

    $query = "
			INSERT INTO
				bt_books_preview
				(
					ID,
					book_id,
					preview_seq,
					user_id,
					created_dt
				)
			VALUES
				(
					NULL,
					?,
					?,
					?,
					NOW()
				)";

    $stmt = $wdb->prepare( $query );
    $stmt->bind_param( 'isi',
        $book_id,
        $preview_seq,
        $create_id
    );

    //$stmt->execute();

    if (!$stmt->execute()) {
        error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
        die;
    }

    $ID = $wdb->insert_id;


    // File Attachment
    if ( $ID && !empty($_FILES['preview-img']['name']) ) {

        //$yyyymm = date('Ym');
        $upload_dir = UPLOAD_PATH . '/books/' . $book_id .'/' ;
        $upload_url = UPLOAD_URL . '/books/'. $book_id .'/' ;

        /**
         * thumb 디렉토리 분리 안하고 같은 디렉토리로 사용
         * $upload_dir_thumb = $upload_dir . 'thumb';
         * $upload_url_thumb = $upload_url . 'thumb';
         *
         * if ( !is_dir($upload_dir_thumb) ) {
         *  mkdir($upload_dir_thumb, 0777, true);
         * }
         *
         */

        if ( !is_dir($upload_dir) ) {
            mkdir($upload_dir, 0777, true);
        }

        $wps_thumbnail = new WpsThumbnail();

        foreach ( $_FILES['preview-img']['name'] as $key => $val ) {
            $file_ext = strtolower(pathinfo( $val, PATHINFO_EXTENSION ));

            if ( in_array($file_ext, unserialize(WPS_IMAGE_EXT)) ) {
                $new_file_name = wps_make_rand() . '.' . $file_ext;
            } else {
                $new_file_name = wps_make_rand();
            }


            $new_val['file_path'] = $upload_dir .  $new_file_name;
            $new_val['file_url'] = $upload_url .  $new_file_name;
            $new_val['file_name'] = $val;
            $new_val['file_size'] = $_FILES['preview-img']['size'][$key];
            $new_val['file_type'] = $_FILES['preview-img']['type'][$key];
            $result = move_uploaded_file( $_FILES['preview-img']['tmp_name'][$key], $new_val['file_path'] );

            $thumb_suffix = '-thumb';
            $thumb_width = 360 ;
            //$thumb_height = isset($_POST['theight']) ? $_POST['theight'] : 0;	// Null이 가능함
            $thumb_name = $wps_thumbnail->resize_image( $new_val['file_path'], $thumb_suffix, $thumb_width );
            $thumb_path[$key] = $upload_dir .  $thumb_name;
            $thumb_url[$key] = $upload_url .  $thumb_name;

            $size = getimagesize($thumb_path[$key]);
            $thumb_w = $size[0];
            $thumb_h = $size[1];

            //array_push($meta_value, $new_val);

            /**
             * 파일을 업로드 하고 관련된 정보를 업데이트 한다.
             */
            $query = "
					UPDATE
						bt_books_preview
					SET
					    preview_img_url=?
					WHERE
						ID = ?
			";
            $stmt = $wdb->prepare( $query );
            $stmt->bind_param( 'si',
                $new_val['file_url'],
                $ID
            );
            $stmt->execute();


        }
        //$meta_value = serialize( $meta_value );
        //wps_update_post_meta( $ID, 'wps-post-attachment', $meta_value );
    }
    return $ID;
}

function lps_get_preview_list($book_id)
{
    global $wdb;

    $query = " SELECT * FROM bt_books_preview WHERE book_id = ? ";
    $stmt = $wdb->prepare( $query );
    $stmt->bind_param( 'i', $book_id );
    $stmt->execute();

    return $wdb->get_row($stmt);
}

/**
 * @param $preview_id
 * @return mixed
 * 프리뷰 삭제
 */
function lps_delete_preview($preview_id)
{
    global $wdb;

    $preview_info = lps_get_preview($preview_id);
    $book_id = $preview_info['book_id'];

    $tmp = explode('/', $preview_info['preview_img_url']);
    $preview_img = end($tmp);

    $preview_file_name = explode( '.', $preview_img)[0];
    $preview_ext = explode( '.', $preview_img)[1];
    $preview_thumb = $preview_file_name . '-thumb.' . $preview_ext ;
    $preview_path = $_SERVER['DOCUMENT_ROOT'] .'/upload/books/' . $book_id . '/';


    // 프리뷰 이미지 삭제
    unlink($preview_path . $preview_img );
    unlink($preview_path . $preview_thumb );

    // 해당 아이디의 뉴스 삭제
    $query = "
				DELETE FROM
					bt_books_preview
				WHERE
					ID = ?
			";
    $stmt = $wdb->prepare( $query );
    $stmt->bind_param( 'i', $preview_id );
    $stmt->execute();

    return $wdb->affected_rows;
}

function lps_get_preview($preview_id)
{
    global $wdb;

    $query = " SELECT * FROM bt_books_preview WHERE id = ? ";
    $stmt = $wdb->prepare( $query );
    $stmt->bind_param( 'i', $preview_id );
    $stmt->execute();

    return $wdb->get_row($stmt);
}
