<?php
function lps_add_banner() {
	global $wdb;
	
	$user_id = wps_get_current_user_id();

	$banner_title = $_POST['banner_title'];
	$banner_url = empty($_POST['banner_url']) ? '' : $_POST['banner_url'];
	$banner_target = empty($_POST['banner_target']) ? '' : $_POST['banner_target'];
	$hide_or_show = empty($_POST['hide_or_show']) ? 'show' : $_POST['hide_or_show'];		// hide
	$banner_from = empty($_POST['banner_from']) ? NULL : $_POST['banner_from'];
	$banner_to = empty($_POST['banner_to']) ? NULL: $_POST['banner_to'];
	$banner_order = intval(lps_get_max_banner_order()) + 1;
	$banner_section = empty($_POST['bnr_section']) ? '' : $_POST['bnr_section'];

	$yyyymm = date('Ym');
	$upload_dir = UPLOAD_PATH . '/banner/' . $yyyymm;
	$upload_url = UPLOAD_URL . '/banner/' . $yyyymm;

	if ( !is_dir($upload_dir) ) {
		mkdir($upload_dir, 0777, true);
	}

	foreach ( $_POST['file_path'] as $key => $val ) {
		$attach_org_fname = $_POST['file_name'][$key];
		$attach_tmp_path = $val;
		$attach_tmp_fname = basename($attach_tmp_path);

		$attach_new_path = $upload_dir . '/' . $attach_tmp_fname;
		$attach_new_url = $upload_url . '/' . $attach_tmp_fname;

		if ( is_file( $attach_tmp_path ) ) {
			rename( $attach_tmp_path, $attach_new_path );
		}
			
		$file_url = $attach_new_url;
		$file_path = $attach_new_path;
		$file_name = $attach_org_fname;
	}
	
	$query = "
			INSERT INTO
				bt_banner
				(
					ID,
					bnr_title,
					bnr_url,
					bnr_target,
					hide_or_show,
					bnr_file_path,
					bnr_file_url,
					bnr_file_name,
					bnr_created,
					bnr_order,
					bnr_from,
					bnr_to,
					user_id,
					bnr_section
				)
			VALUES
				(
					NULL, ?, ?, ?, ?,
					?, ?, ?, NOW(), ?, 
					?, ?, ?, ?
				)
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'sssssssissis', $banner_title, $banner_url, $banner_target, $hide_or_show,
						$file_path, $file_url, $file_name, $banner_order, 
						$banner_from, $banner_to, $user_id, $banner_section
	);
	$stmt->execute();
	return $wdb->insert_id;
}

function lps_update_banner() {
	global $wdb;

	$user_id = wps_get_current_user_id();

	$banner_id = $_POST['bkey'];
	$banner_title = $_POST['banner_title'];
	$banner_url = empty($_POST['banner_url']) ? '' : $_POST['banner_url'];
	$banner_target = empty($_POST['banner_target']) ? '' : $_POST['banner_target'];
	$hide_or_show = empty($_POST['hide_or_show']) ? 'show' : $_POST['hide_or_show'];		// hide
	$banner_from = empty($_POST['banner_from']) ? NULL : $_POST['banner_from'];
	$banner_to = empty($_POST['banner_to']) ? NULL: $_POST['banner_to'];
	//$banner_section = empty($_POST['bnr_section']) ? 'main' : $_POST['bnr_section'];

	if (!empty($_FILES['attachment']['tmp_name'])) {
		$yyyymm = date('Ym');
		$upload_dir = UPLOAD_PATH . '/banner/' . $yyyymm;
		$upload_url = UPLOAD_URL . '/banner/' . $yyyymm;
		
		if ( !is_dir($upload_dir) ) {
			mkdir($upload_dir, 0777, true);
		}
		
		$attach_org_fname = $_FILES['attachment']['name'];
		$attach_tmp_path = $_FILES['attachment']['tmp_name'];
		$attach_tmp_fname = wps_make_rand() . '.' . get_file_extension($attach_org_fname);
	
		$attach_new_path = $upload_dir . '/' . $attach_tmp_fname;
		$attach_new_url = $upload_url . '/' . $attach_tmp_fname;
	
		if ( is_file( $attach_tmp_path ) ) {
			rename( $attach_tmp_path, $attach_new_path );
		}
			
		$file_url = $attach_new_url;
		$file_path = $attach_new_path;
		$file_name = $attach_org_fname;
		
		$query = "
				UPDATE
					bt_banner
				SET
					bnr_file_path = ?,
					bnr_file_url = ?,
					bnr_file_name = ?
				WHERE
					ID = ?
		";
		$stmt = $wdb->prepare( $query );
		$stmt->bind_param( 'sssi', $file_path, $file_url, $file_name, $banner_id );
		$stmt->execute();
	}

	$query = "
			UPDATE 
				bt_banner
			SET
				bnr_title = ?,
				bnr_url = ?,
				bnr_target = ?,
				hide_or_show = ?,
				bnr_from = ?,
				bnr_to = ?
			WHERE
				ID = ?
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'ssssssi', $banner_title, $banner_url, $banner_target, $hide_or_show,
			$banner_from, $banner_to, $banner_id
	);
	return $stmt->execute();
}

function lps_delete_banner( $banner_id ) {
	global $wdb;
	
	$banner_rows = lps_get_banner_by_id($banner_id);
	$banner_file_path = $banner_rows['bnr_file_path'];
	@unlink( $banner_file_path );
	
	$query = "
		DELETE FROM
			bt_banner
		WHERE
			ID = ?
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param('i', $banner_id);
	return $stmt->execute();
}

function lps_get_banner_by_id( $banner_id ) {
	global $wdb;
	
	$query = "
		SELECT
			*
		FROM
			bt_banner
		WHERE
			ID = ?
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param('i', $banner_id);
	$stmt->execute();
	return $wdb->get_row($stmt);
}

/*
 * Desc : 순서 정렬
 */
function lps_reorder_banner() {
	global $wdb;
	
	$affected = 0;
	
	$query = "
		UPDATE
			bt_banner
		SET
			bnr_order = ?
		WHERE
			ID = ?
	";
	$stmt = $wdb->prepare( $query );
	
	foreach ($_POST['banner_id'] as $key => $val) {
		$stmt->bind_param('ii', $key, $val);
		if ($stmt->execute()) {
			$affected++;
		}
	}
	return $affected;
}

function lps_get_banners($section) {
	global $wdb;
	
	$query = "
		SELECT
			*
		FROM
			bt_banner
			WHERE bnr_section = ?
		ORDER BY
			bnr_order ASC
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 's', $section);
	$stmt->execute();
	return $wdb->get_results($stmt);
}

function lps_get_front_banners() {
	global $wdb;
	
	$query = "
		SELECT
			*
		FROM
			bt_banner
		WHERE
			hide_or_show = 'show' AND
			CURDATE() BETWEEN bnr_from AND bnr_to
		ORDER BY
			bnr_order ASC
	";
	$stmt = $wdb->prepare( $query );
	$stmt->execute();
	return $wdb->get_results($stmt);
}

function lps_get_max_banner_order() {
	global $wdb;

	$query = "
		SELECT
			MAX(bnr_order)
		FROM
			bt_banner
	";
	$stmt = $wdb->prepare( $query );
	$stmt->execute();
	return $wdb->get_var($stmt);
}

function lps_add_curation() {
	global $wdb;


	error_log(print_r($_POST, true));
	$user_id = wps_get_current_user_id();

	$curation_title = $_POST['curation_title'];
	$curation_sub_title = $_POST['curation_sub_title'];
	$curation_content = $_POST['curation_content'];
	$curation_status = $_POST['curation_status'];
	$curator_level = wps_get_user_level();
	$curation_meta = serialize($_POST['pkg_books']);
	$cover_img = '';
	
	$curation_order = intval(lps_get_max_curation_order()) + 1;

	$yyyymm = date('Ym');
	$upload_dir = UPLOAD_PATH . '/curation/' . $yyyymm;
	$upload_url = UPLOAD_URL . '/curation/' . $yyyymm;


	if ( !is_dir($upload_dir) ) {
		mkdir($upload_dir, 0777, true);
	}


	foreach ( $_POST['file_path'] as $key => $val ) {
		$attach_org_fname = $_POST['file_name'][$key];
		$attach_tmp_path = $val;
		$attach_tmp_fname = basename($attach_tmp_path);

		$attach_new_path = $upload_dir . '/' . $attach_tmp_fname;
		$attach_new_url = $upload_url . '/' . $attach_tmp_fname;

		if ( is_file( $attach_tmp_path ) ) {
			rename( $attach_tmp_path, $attach_new_path );
		}
			
		$file_url = $attach_new_url;
		$file_path = $attach_new_path;
		$file_name = $attach_org_fname;
		
		$cover_img = serialize(compact('file_url', 'file_path', 'file_name'));
	}

	$query = "
			INSERT INTO
				bt_curation
				(
					ID,
					curation_title,
					cover_img,
					curation_order,
					curation_status,
					curator_level,
					curation_meta,
					created_dt,
					user_id,
					hit_count,
					curation_sub_title,
					curation_content
				)
			VALUES
				(
					NULL, ?, ?, ?, ?,
					?, ?, NOW(), ?, 0, ?, ?
				)
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'ssiiisiss', $curation_title, $cover_img, $curation_order, $curation_status,
								$curator_level, $curation_meta, $user_id, $curation_sub_title, $curation_content
			);

	$stmt->execute();
	return $wdb->insert_id;
}

function lps_edit_curation() {
	global $wdb;

	//error_log(print_r($_POST, true));
	$curation_id = $_POST['curation_id'];

	$curation_title = $_POST['curation_title'];
	$curation_sub_title = $_POST['curation_sub_title'];
	$curation_content = $_POST['curation_content'];
	$curation_status = $_POST['curation_status'];
	$curation_meta = serialize($_POST['pkg_books']);
	$cover_img = '';
	
	foreach ( $_POST['file_path'] as $key => $val ) {
		
		if (stripos($val, '/tmp/') !== false) {		// 새로운 표지이미지 업로드 시
			$yyyymm = date('Ym');
			$upload_dir = UPLOAD_PATH . '/curation/' . $yyyymm;
			$upload_url = UPLOAD_URL . '/curation/' . $yyyymm;
			
			if ( !is_dir($upload_dir) ) {
				mkdir($upload_dir, 0777, true);
			}
			
			$attach_org_fname = $_POST['file_name'][$key];
			$attach_tmp_path = $val;
			$attach_tmp_fname = basename($attach_tmp_path);
			
			$attach_new_path = $upload_dir . '/' . $attach_tmp_fname;
			$attach_new_url = $upload_url . '/' . $attach_tmp_fname;
			
			if ( is_file( $attach_tmp_path ) ) {
				rename( $attach_tmp_path, $attach_new_path );
			}
			
			$file_url = $attach_new_url;
			$file_path = $attach_new_path;
			$file_name = $attach_org_fname;
				
			$cover_img = serialize(compact('file_url', 'file_path', 'file_name'));
				
			$query = "
					UPDATE
						bt_curation
					SET
						cover_img = ?
					WHERE
						ID = ?
			";
			$stmt = $wdb->prepare( $query );
			$stmt->bind_param( 'si', $cover_img, $curation_id );
			$stmt->execute();
		}
	}

	$query = "
			UPDATE
				bt_curation
			SET
				curation_title = ?,
				curation_status = ?,
				curation_meta = ?,
				created_dt = NOW(),
				curation_sub_title = ?,
				curation_content = ?
			WHERE
				ID = ?
	";
	$stmt = $wdb->prepare( $query );

	$stmt->bind_param( 'sisssi', $curation_title, $curation_status, $curation_meta, $curation_sub_title, $curation_content, $curation_id  );

	return $stmt->execute();
}

function lps_delete_curation( $curation_id ) {
	global $wdb;

	$curation_rows = lps_get_curation_by_id($curation_id);
	$cover_img = unserialize($curation_rows['cover_img']);
	@unlink( $cover_img['file_path'] );

	$query = "
		DELETE FROM
			bt_curation
		WHERE
			ID = ?
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param('i', $curation_id);
	return $stmt->execute();
}

function lps_reorder_curation() {
	global $wdb;

	$affected = 0;

	$query = "
		UPDATE
			bt_curation
		SET
			curation_order = ?
		WHERE
			ID = ?
	";
	$stmt = $wdb->prepare( $query );

	foreach ($_POST['curation_id'] as $key => $val) {
		$stmt->bind_param('ii', $key, $val);
		if ($stmt->execute()) {
			$affected++;
		}
	}
	return $affected;
}

/*
 * Desc : 특정 회원들의 큐레이션 리스트
 */
function lps_get_curations() {
	global $wdb;
	
	$query = "
		SELECT
			*
		FROM
			bt_curation
		ORDER BY
			curation_order ASC
	";
	$stmt = $wdb->prepare( $query );
	$stmt->execute();
	return $wdb->get_results($stmt);
}

/*
 * Desc : 큐레이션 전체보기에서 사용 (회원들 것만 모아서)
 */
function lps_get_user_curations() {
	global $wdb;

	$query = "
		SELECT
			*
		FROM
			bt_curation
		WHERE
			curation_status = '3000' AND
			curator_level <> '10'
		ORDER BY
			ID DESC
	";
	$stmt = $wdb->prepare( $query );
	$stmt->execute();
	return $wdb->get_results($stmt);
}

/*
 * Desc : Web main > 큐레이션 검색
 * lps_search_book_by_keyword
 */
function lps_search_curation_by_keyword( $q ) {
	global $wdb;

	$query = "
		SELECT
			*
		FROM
			bt_curation
		WHERE
			curation_status = '3000' AND
			curation_title LIKE ?
		ORDER BY
			ID DESC
	";
	$stmt = $wdb->prepare( $query );
	$qs = '%' . $q . '%';
	$stmt->bind_param('s', $qs);
	$stmt->execute();
	return $wdb->get_results($stmt);
}

function lps_get_main_curations() {
	global $wdb;
	
	$query = "
		SELECT
			*
		FROM
			bt_curation
		WHERE
			curation_status = '3000'
		ORDER BY
			curation_order ASC
		LIMIT
			0, 10
	";
	$stmt = $wdb->prepare( $query );
	$stmt->execute();
	return $wdb->get_results($stmt);
}

function lps_get_curation_by_id( $curation_id ) {
	global $wdb;

	$query = "
		SELECT
			*
		FROM
			bt_curation
		WHERE
			ID = ?
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param('i', $curation_id);
	$stmt->execute();
	return $wdb->get_row($stmt);
}

/*
 * Desc : 큐레이션 조회수 증가
 */
function lps_update_curation_hit( $curation_id ) {
	global $wdb;

	$query = "
		UPDATE
			bt_curation
		SET
			hit_count = hit_count + 1
		WHERE
			ID = ?
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param('i', $curation_id);
	return $stmt->execute();
}

function lps_get_max_curation_order() {
	global $wdb;

	$user_id = wps_get_current_user_id();
	
	$query = "
		SELECT
			MAX(curation_order)
		FROM
			bt_curation
		WHERE
			user_id = ?
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param('i', $user_id);
	$stmt->execute();
	return $wdb->get_var($stmt);
	
}

/*
 * Desc : 출판사 입점도서 추가
 */
function lps_add_publisher() {
	global $wdb;

	$publisher_id = $_POST['publisher'];
	$period_from = $_POST['period_from'];
	$period_to = $_POST['period_to'];
	$publisher_ci = '';
	
	$publisher_order = intval(lps_get_max_publisher_order()) + 1;

	if (!empty($_POST['file_path'][0])) {	// 사진 수정 시
		$yyyymm = date('Ym');
		$upload_dir = UPLOAD_PATH . '/user_avatar/' . $yyyymm;
		$upload_url = UPLOAD_URL . '/user_avatar/' . $yyyymm;
	
		if ( !is_dir($upload_dir) ) {
			mkdir($upload_dir, 0777, true);
		}
	
		foreach ( $_POST['file_path'] as $key => $val ) {
			$attach_org_fname = $_POST['file_name'][$key];
			$attach_tmp_path = $val;
			$attach_tmp_fname = basename($attach_tmp_path);
	
			$attach_new_path = $upload_dir . '/' . $attach_tmp_fname;
			$attach_new_url = $upload_url . '/' . $attach_tmp_fname;
	
			if ( is_file( $attach_tmp_path ) ) {
				rename( $attach_tmp_path, $attach_new_path );
			}
				
			$file_url = $attach_new_url;
// 			$file_path = $attach_new_path;
// 			$file_name = $attach_org_fname;
	
		}
		wps_update_user_meta($publisher_id, 'wps_user_avatar', $file_url);
		
		$publisher_ci = $file_url;
	}

	$query = "
			INSERT INTO
				bt_publisher_book
				(
					ID,
					publisher_id,
					period_from,
					period_to,
					publisher_order,
					created_dt,
					publisher_ci
				)
			VALUES
				(
					NULL, ?, ?, ?, ?,
					NOW(), ?
				)
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'issis', $publisher_id, $period_from, $period_to, $publisher_order, $publisher_ci );
	$stmt->execute();
	return $wdb->insert_id;
}

function lps_get_max_publisher_order() {
	global $wdb;

	$user_id = wps_get_current_user_id();
	
	$query = "
		SELECT
			MAX(publisher_order)
		FROM
			bt_publisher_book
	";
	$stmt = $wdb->prepare( $query );
	$stmt->execute();
	return $wdb->get_var($stmt);
	
}

function lps_get_publishers() {
	global $wdb;

	$query = "
		SELECT
			*
		FROM
			bt_publisher_book
		WHERE
			1
		ORDER BY
			publisher_order ASC
	";
	$stmt = $wdb->prepare( $query );
	$stmt->execute();
	return $wdb->get_results($stmt);
}


function lps_delete_publisher() {
	global $wdb;

	$pb_uid = $_POST['pb_uid'];
	$deleted = 0;

	$query = "
			DELETE FROM
				bt_publisher_book
			WHERE
				ID = ?
	";
	$stmt = $wdb->prepare( $query );

	foreach ($pb_uid as $key => $val) {
		$stmt->bind_param('i', $val);
		$stmt->execute();
		$deleted++;
	}
	
	return $deleted;
}

function lps_reorder_publisher() {
	global $wdb;

	$affected = 0;

	$query = "
		UPDATE
			bt_publisher_book
		SET
			publisher_order = ?
		WHERE
			ID = ?
	";
	$stmt = $wdb->prepare( $query );

	foreach ($_POST['pb_uid'] as $key => $val) {
		$stmt->bind_param('ii', $key, $val);
		if ($stmt->execute()) {
			$affected++;
		}
	}
	return $affected;
}
?>