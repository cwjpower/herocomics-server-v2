<?php
function wps_add_post() {
	global $wdb;

	$post_name = empty($_POST['post_name']) ? wps_get_current_user_display_name() : $_POST['post_name'];
	$post_content = empty($_POST['post_content']) ? '' : $_POST['post_content'];
	$post_date = empty($_POST['post_date']) ? date('Y-m-d H:i:s') : $_POST['post_date'];
	$post_title = empty($_POST['post_title']) ? '' : $_POST['post_title'];
	
	$post_parent = empty($_POST['post_parent']) ? '0' : $_POST['post_parent'];
	$post_status = empty($_POST['post_status']) ? 'open' : $_POST['post_status'];
	$post_password = empty($_POST['post_password']) ? '' : $_POST['post_password'];
	$post_user_id = empty($_POST['post_user_id']) ? wps_get_current_user_id(): $_POST['post_user_id'];
	$post_email = empty($_POST['post_email']) ? '' : $_POST['post_email'];
	$post_order = empty($_POST['post_order']) ? 0: $_POST['post_order'];
	$post_type = empty($_POST['post_type']) ? 'unknown' : $_POST['post_type'];
	$post_type_secondary = empty($_POST['post_type_secondary']) ? '' : $_POST['post_type_secondary'];
	$post_type_area = empty($_POST['post_type_area']) ? '' : implode(',', $_POST['post_type_area']);
	$post_modified = date('Y-m-d H:i:s');
	
	/*
	 * Desc : 모든 담벼락을 선택한 경우엔  1.post_status:all 2.post_type_secondary:community 처리
	 */
	if (stripos($post_type_area, 'post') !== false) {	// 모든 담벼락
		$post_status = 'all';
		$post_type_secondary = 'community';
	}
	
	// CKEditor 에서 사용한 이미지
	preg_match_all('/(src)=("[^"]*")/i', $post_content, $images, PREG_SET_ORDER);
	
	if ( !empty($images) ) {
		$images_tmp = array();		// Temporary directory path
		$images_real = array();		// Real directory path
	
		foreach ( $images  as $key => $val ) {
			if ( stripos($val[2], UPLOAD_URL) !== false ) {
				$images_tmp[] = $val[2];
			}
		}
	
		if ( !empty($images_tmp) ) {
			$ym = date('Ym');
			
			// Image
			$upload_tmp['path'] = UPLOAD_PATH . '/tmp';
			$upload_real['url'] = UPLOAD_URL . '/ckeditor/' . $ym;
			$upload_real['path'] = UPLOAD_PATH . '/ckeditor/' . $ym;
	
			if ( !is_dir($upload_real['path']) ) {
				mkdir($upload_real['path'], 0777, true);
			}
	
			foreach ( $images_tmp as $key => $val ) {
				$img_url = str_replace( '"', '', $val );	// 큰 따옴표가 있는 것에 주의!
				$changed_tmp = str_replace( UPLOAD_URL, UPLOAD_PATH, $img_url );
				$changed_real = str_replace( $upload_tmp['path'], $upload_real['path'], $changed_tmp );
	
				if ( @rename($changed_tmp, $changed_real) ) {
					$post_content = str_replace( $img_url, UPLOAD_URL . '/ckeditor/' . $ym . '/' . basename($img_url), $post_content );
	
					$images_real[] = $changed_real;
				}
			}
		}
	}
	// ./CKEditor

	$query = "
			INSERT INTO
				bt_posts
				(
					ID,
					post_name,
					post_content,
					post_date,
					post_title,
					post_parent,
					post_status,
					post_password,
					post_user_id,
					post_email,
					post_order,
					post_type,
					post_type_secondary,
					post_type_area,
					post_modified									
				)
			VALUES
				(
					NULL, ?, ?, ?, ?,
					?, ?, ?, ?, ?,
					?, ?, ?, ?, ?
				)
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'ssssisssssssss',
			$post_name,
			$post_content,
			$post_date,
			$post_title,
			$post_parent,
			$post_status,
			$post_password,
			$post_user_id,
			$post_email,
			$post_order,
			$post_type,
			$post_type_secondary,
			$post_type_area,
			$post_modified
		);
	$stmt->execute();
	
	$ID = $wdb->insert_id;
	
	// CKEditor 에서 사용한 이미지를 DB에서 관리한다.
	if ( !empty($images_real) ) {
		$serialized = serialize($images_real);
		wps_update_post_meta( $ID, 'wps_ckeditor_attached_images', $serialized );
	}
	
	// File Attachment
	if ( $ID && !empty($_FILES['attachment']['name']) ) {
		
		$yyyymm = date('Ym');
		$upload_dir = UPLOAD_PATH . '/board/' . $ID . '/' . $yyyymm;
		$upload_url = UPLOAD_URL . '/board/' . $ID . '/' . $yyyymm;
		$meta_value = array();
		
		if ( !is_dir($upload_dir) ) {
			mkdir($upload_dir, 0777, true);
		}
		
		foreach ( $_FILES['attachment']['name'] as $key => $val ) {
			$file_ext = strtolower(pathinfo( $val, PATHINFO_EXTENSION ));
			
			if ( in_array($file_ext, unserialize(WPS_IMAGE_EXT)) ) {
				$new_file_name = wps_make_rand() . '.' . $file_ext;
			} else {
				$new_file_name = wps_make_rand();
			}
			
			$new_val['file_path'] = $upload_dir . '/' . $new_file_name;
			$new_val['file_url'] = $upload_url . '/' . $new_file_name;
			$new_val['file_name'] = $val;
			$new_val['file_size'] = $_FILES['attachment']['size'][$key];
			$new_val['file_type'] = $_FILES['attachment']['type'][$key];
			$result = move_uploaded_file( $_FILES['attachment']['tmp_name'][$key], $new_val['file_path'] );
			array_push($meta_value, $new_val);
		}
		$meta_value = serialize( $meta_value );
		wps_update_post_meta( $ID, 'wps-post-attachment', $meta_value );
	}
	
	return $ID;
}

function wps_update_post() {
	global $wdb;

	$ID = $_POST['ID'];
	$post_name = empty($_POST['post_name']) ? wps_get_current_user_name() : $_POST['post_name'];
	$post_content = empty($_POST['post_content']) ? '' : $_POST['post_content'];
	$post_title = empty($_POST['post_title']) ? '' : $_POST['post_title'];
	$post_status = empty($_POST['post_status']) ? 'open' : $_POST['post_status'];
	$post_order = empty($_POST['post_order']) ? 0: $_POST['post_order'];
	$post_type_area = empty($_POST['post_type_area']) ? '' : implode(',', $_POST['post_type_area']);
	$post_modified = date('Y-m-d H:i:s');
	
	// CKEditor 에서 사용한 이미지
	preg_match_all('/(src)=("[^"]*")/i', $post_content, $images, PREG_SET_ORDER);
	
	if ( !empty($images) ) {
		$images_tmp = array();		// Temporary directory path
		$images_real = array();		// Real directory path
	
		foreach ( $images  as $key => $val ) {
			if ( stripos($val[2], UPLOAD_URL) !== false ) {
				$images_tmp[] = $val[2];
			}
		}
	
		if ( !empty($images_tmp) ) {
			$ym = date('Ym');
	
			// Image
			$upload_tmp['path'] = UPLOAD_PATH . '/tmp';
			$upload_real['url'] = UPLOAD_URL . '/ckeditor/' . $ym;
			$upload_real['path'] = UPLOAD_PATH . '/ckeditor/' . $ym;
	
			if ( !is_dir($upload_real['path']) ) {
				mkdir($upload_real['path'], 0777, true);
			}
	
			foreach ( $images_tmp as $key => $val ) {
				$img_url = str_replace( '"', '', $val );	// 큰 따옴표가 있는 것에 주의!
				$changed_tmp = str_replace( UPLOAD_URL, UPLOAD_PATH, $img_url );
				$changed_real = str_replace( $upload_tmp['path'], $upload_real['path'], $changed_tmp );
	
				if ( @rename($changed_tmp, $changed_real) ) {
					$post_content = str_replace( $img_url, UPLOAD_URL . '/ckeditor/' . $ym . '/' . basename($img_url), $post_content );
	
					$images_real[] = $changed_real;
				}
			}
		}
	}
	// ./CKEditor

	$query = "
			UPDATE
				bt_posts
			SET
				post_name = ?,
				post_content = ?,
				post_title = ?,
				post_status = ?,
				post_order = ?,
				post_type_area = ?,
				post_modified	= ?								
			WHERE
				ID = ?
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'ssssissi',
			$post_name,
			$post_content,
			$post_title,
			$post_status,
			$post_order,
			$post_type_area,
			$post_modified,
			$ID
		);
	$result =$stmt->execute();

	// CKEditor 에서 사용한 이미지를 DB에서 관리한다.
	if ( !empty($images_real) ) {
		$serialized = serialize($images_real);
		wps_update_post_meta( $ID, 'wps_ckeditor_attached_images', $serialized );
	}
	
	// File Attachment
	if ( $result && !empty($_FILES['attachment']['name']) ) {
		
		// 기존 업로드 파일 정보
		$wps_post_attachment = wps_get_post_meta( $ID, 'wps-post-attachment' );
		$unserialized = unserialize( $wps_post_attachment );
		
		if ( empty($unserialized) ) {
			$unserialized = array();
		}
		
		$yyyymm = date('Ym');
		$upload_dir = UPLOAD_PATH . '/board/' . $ID . '/' . $yyyymm;
		$upload_url = UPLOAD_URL . '/board/' . $ID . '/' . $yyyymm;
		$meta_value = array();
	
		if ( !is_dir($upload_dir) ) {
			mkdir($upload_dir, 0777, true);
		}
	
		foreach ( $_FILES['attachment']['name'] as $key => $val ) {
			$file_ext = strtolower(pathinfo( $val, PATHINFO_EXTENSION ));
				
			if ( in_array($file_ext, unserialize(WPS_IMAGE_EXT)) ) {
				$new_file_name = wps_make_rand() . '.' . $file_ext;
			} else {
				$new_file_name = wps_make_rand();
			}
				
			$new_val['file_path'] = $upload_dir . '/' . $new_file_name;
			$new_val['file_url'] = $upload_url . '/' . $new_file_name;
			$new_val['file_name'] = $val;
			$new_val['file_size'] = $_FILES['attachment']['size'][$key];
			$new_val['file_type'] = $_FILES['attachment']['type'][$key];
			$result = move_uploaded_file( $_FILES['attachment']['tmp_name'][$key], $new_val['file_path'] );
			array_push($unserialized, $new_val);
		}
		$meta_value = serialize( $unserialized );
		wps_update_post_meta( $ID, 'wps-post-attachment', $meta_value );
	}
	
	return $result;
}

function wps_get_post( $post_id ) {
	global $wdb;

	$query = "
			SELECT
				*
			FROM
				bt_posts
			WHERE
				ID = ?
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'i', $post_id );
	$stmt->execute();
	return $wdb->get_row($stmt);
}

function wps_delete_post( $post_id ) {
	global $wdb;

// 	$query = "
// 			SELECT
// 				comment_ID
// 			FROM
// 				bt_comments
// 			WHERE
// 				comment_post_ID = ?
// 	";
// 	$stmt = $wdb->prepare( $query );
// 	$stmt->bind_param( 'i', $post_id );
// 	$stmt->execute();
// 	$comments = $wdb->get_results($stmt);
// 	if ( !empty($comments) ) {
// 		foreach ( $comments as $key => $val ) {
// 			lps_delete_comment( $val['comment_ID'] );
// 		}
// 	}

	wps_delete_post_meta( $post_id );

	$query = "
				DELETE FROM
					bt_posts
				WHERE
					ID = ?
			";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'i', $post_id );
	$stmt->execute();
	return $wdb->affected_rows;
}

function wps_get_post_field( $post_id, $field ) {
	global $wdb;
	
	$query = "
			SELECT
				$field
			FROM
				bt_posts
			WHERE
				ID = ?
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'i', $post_id );
	$stmt->execute();
	return $wdb->get_var($stmt);
}

function wps_get_post_meta( $post_id, $meta_key = NULL ) {
	global $wdb;

	if ( empty($meta_key) ) {
		$query = "
				SELECT
					*
				FROM
					bt_posts_meta
				WHERE
					post_id = ?
		";
		$stmt = $wdb->prepare( $query );
		$stmt->bind_param( 'i', $post_id );
		$stmt->execute();
		return $wdb->get_results($stmt);
	} else {
		$query = "
				SELECT
					meta_value
				FROM
					bt_posts_meta
				WHERE
					post_id = ? AND
					meta_key = ?
		";
		$stmt = $wdb->prepare( $query );
		$stmt->bind_param( 'is', $post_id, $meta_key );
		$stmt->execute();
		return $wdb->get_var($stmt);
	}
}

/*
 * Desc : QnA와 같이 답변이 있을 경우, 해당 글의 답변 글을 찾는다.
 */
function wps_get_child_post_id( $post_id, $post_type_secondary ) {
	global $wdb;

	$query = "
			SELECT
				ID
			FROM
				bt_posts
			WHERE
				post_parent = ? AND
				post_type_secondary = ?
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'is', $post_id, $post_type_secondary );
	$stmt->execute();
	return $wdb->get_var($stmt);
}

function wps_get_post_count( $type = 'post_new', $post_parent = NULL ) {
	global $wdb;

	if ( empty($post_parent) ) {
		$query = "
				SELECT
					COUNT(*)
				FROM
					bt_posts
				WHERE
					post_type = ?
		";
		$stmt = $wdb->prepare( $query );
		$stmt->bind_param( 's', $type );
	} else {
		$query = "
				SELECT
					COUNT(*)
				FROM
					bt_posts
				WHERE
					post_parent = ? AND
					post_type = ?
		";
		$stmt = $wdb->prepare( $query );
		$stmt->bind_param( 'is', $post_parent, $type );
	}
	$stmt->execute();
	return $wdb->get_var($stmt);
}

function wps_get_post_prev( $post_id, $type = NULL ) {
	global $wdb;

	if ( empty($type) ) {
		$type = 'post_new';
	}

	$query = "
			SELECT
			    ID,
				post_title
			FROM
			    bt_posts
			WHERE
			    ID < ? AND
				post_type = '$type' AND
				post_parent = ( SELECT post_parent FROM bt_posts WHERE ID = ? )
			ORDER BY
				ID DESC
			LIMIT
				0 , 1
	";
	$stmt = $wdb->prepare($query);
	$stmt->bind_param( 'ii', $post_id, $post_id );
	$stmt->execute();
	$prev_row = $wdb->get_row($stmt);
	return $prev_row;
}

function wps_get_post_next( $post_id, $type = NULL ) {
	global $wdb;

	if ( empty($type) ) {
		$type = 'post_new';
	}

	$query = "
			SELECT
			    ID,
				post_title
			FROM
			    bt_posts
			WHERE
			    ID > ? AND
				post_type = '$type' AND
				post_parent = ( SELECT post_parent FROM bt_posts WHERE ID = ? )
			ORDER BY
				ID ASC
			LIMIT
				0 , 1
	";
	$stmt = $wdb->prepare($query);
	$stmt->bind_param( 'ii', $post_id, $post_id );
	$stmt->execute();
	$next_row = $wdb->get_row($stmt);
	return $next_row;
}

function wps_update_post_meta( $post_id, $meta_key, $meta_value ) {
	global $wdb;

	$query = "
			SELECT
				meta_id
			FROM
				bt_posts_meta
			WHERE
				post_id = ? AND
				meta_key = ?
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'is', $post_id, $meta_key );
	$stmt->execute();
	$meta_id = $wdb->get_var($stmt);

	if ( empty($meta_id) ) {
		$query = "
				INSERT INTO
					bt_posts_meta
					(
						meta_id,
						post_id,
						meta_key,
						meta_value
					)
				VALUES
					(
						NULL, ?, ?, ?
					)
		";
		$stmt = $wdb->prepare( $query );
		$stmt->bind_param( 'iss', $post_id, $meta_key, $meta_value );
		$stmt->execute();

		return $wdb->insert_id;
	} else {
		$query = "
			UPDATE
				bt_posts_meta
			SET
				meta_value = ?
			WHERE
				post_id = ? AND
				meta_key = ?
		";
		$stmt = $wdb->prepare( $query );
		$stmt->bind_param( 'sis', $meta_value, $post_id, $meta_key );
		$stmt->execute();
		return $wdb->affected_rows;
	}
}

function wps_delete_post_meta( $post_id, $meta_key = NULL ) {
	global $wdb;

	if ( empty($meta_key) ) {
		$query = "
				DELETE FROM
					bt_posts_meta
				WHERE
					post_id = ?
			";
		$stmt = $wdb->prepare( $query );
		$stmt->bind_param( 'i', $post_id );
	} else {
		$query = "
				DELETE FROM
					bt_posts_meta
				WHERE
					post_id = ? AND
					meta_key = ?
			";
		$stmt = $wdb->prepare( $query );
		$stmt->bind_param( 'is', $post_id, $meta_key );
	}
	$stmt->execute();
	return $wdb->affected_rows;
}


/*
 * Desc : 게시판 설정의 list, view, edit 내용 확인
 */
function wps_get_board_access( $post_id, $type = NULL ) {
	$wps_board_access = unserialize(wps_get_post_meta( $post_id, 'wps_board_access' ));
	if ( $wps_board_access ) {
		if ( empty($type) ) {
			foreach ( $wps_board_access as $key => $val ) {
				$board_access[$key] = $val;
			}
			return $board_access;
		} else {
			$result = false;
			foreach ( $wps_board_access as $key => $val ) {
				if ( !strcmp($key, $type) ) {
					$result = $val;
				}
			}
			return $result;
		}
	} else {
		return false;
	}
}

function lps_add_comment() {
	global $wdb;
	
	$comment_post_ID = $_POST['post_id'];
	$comment_author = empty($_POST['comment_author']) ? wps_get_current_user_name() : $_POST['comment_author'];
	$comment_author_email = empty($_POST['comment_author_email']) ? '' : $_POST['comment_author_email'];
	$comment_date = date('Y-m-d H:i:s');
	$comment_content = empty($_POST['comment_content']) ? '' : $_POST['comment_content'];
	$comment_approved = empty($_POST['comment_approved']) ? '' : $_POST['comment_approved'];
	$comment_type = empty($_POST['comment_type']) ? '' : $_POST['comment_type'];
	$comment_user_id = wps_get_current_user_id() ? wps_get_current_user_id() : 0; 
	
	$query = "
				INSERT INTO
					bt_comments
					(
						comment_ID,
						comment_post_ID,
						comment_author,
						comment_author_email,
						comment_date,
						comment_content,
						comment_approved,
						comment_type,
						comment_user_id
					)
				VALUES
					(
						NULL, ?, ?, ?, ?,
						?, ?, ?, ?
					)
		";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'issssssi',
			$comment_post_ID,
			$comment_author,
			$comment_author_email,
			$comment_date,
			$comment_content,
			$comment_approved,
			$comment_type,
			$comment_user_id
	);
	$stmt->execute();
	
	return $wdb->insert_id;
}

function lps_get_comment( $comment_id ) {
	global $wdb;
	
	$query = "
			SELECT
				*
			FROM
				bt_comments
			WHERE
				comment_ID = ?
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'i', $comment_id );
	$stmt->execute();
	return $wdb->get_row($stmt); 
}

function lps_delete_comment( $comment_id ) {
	global $wdb;

	lps_delete_comment_meta( $comment_id );

	$query = "
				DELETE FROM
					bt_comments
				WHERE
					comment_ID = ?
			";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'i', $comment_id );
	$stmt->execute();
	return $wdb->affected_rows;
}

function lps_delete_comment_meta( $comment_id, $meta_key = NULL ) {
	global $wdb;

	if ( empty($meta_key) ) {
		$query = "
				DELETE FROM
					wps_commentmeta
				WHERE
					comment_id = ?
			";
		$stmt = $wdb->prepare( $query );
		$stmt->bind_param( 'i', $comment_id );
	} else {
		$query = "
				DELETE FROM
					wps_commentmeta
				WHERE
					comment_id = ? AND
					meta_key = ?
			";
		$stmt = $wdb->prepare( $query );
		$stmt->bind_param( 'is', $comment_id, $meta_key );
	}
	$stmt->execute();
	return $wdb->affected_rows;
}

/*
 * Desc : comments of a post
 */
function lps_get_comments( $post_id ) {
	global $wdb;
	
	$query = "
			SELECT
				c.comment_ID,
				c.comment_post_ID,
				c.comment_author,
				c.comment_date,
				c.comment_content,
				c.comment_type,
				c.comment_user_id,
				u.user_name
			FROM
				bt_comments AS c
			LEFT JOIN
				wps_users AS u
			ON
				c.comment_user_id = u.ID
			WHERE
				c.comment_post_ID = ?
			ORDER BY
				1 DESC
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'i', $post_id );
	$stmt->execute();
	return $wdb->get_results($stmt);
}


function lps_get_posts_by_type( $post_type, $limit = 5, $post_type_area = NULL ) {
	global $wdb;
	
	$sql = '';
	if (!empty($post_type_area)) {
		$sql = "AND p.post_type_area LIKE '%$post_type_area%'";
	}
	
	$query = "
		SELECT
			p.ID,
			p.post_name,
			p.post_date,
			p.post_title,
			p.post_parent,
			p.post_status,
			p.post_user_id,
			p.post_email,
			p.post_order,
			p.post_type,
			p.post_type_secondary,
			p.post_type_area,
			p.post_modified,
			m.meta_value AS post_view_count
		FROM
			bt_posts AS p
		LEFT JOIN
			bt_posts_meta AS m
		ON
			p.ID = m.post_id AND
			m.meta_key = 'post_view_count'
		WHERE
			p.post_type = ?
			$sql
		ORDER BY
			p.post_order DESC,
			p.post_modified DESC
		LIMIT
			0, $limit
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 's', $post_type );
	$stmt->execute();
	return $wdb->get_results($stmt);
}

/*
 * Desc : 전체 담벼락 공지, 해당 출판사나 작가에 해당될 때만
 */
function lps_get_community_notice() {
	global $wdb;
	
	$query = "
			SELECT
				ID,
				post_title,
				post_date,
				post_name
			FROM
				bt_posts
			WHERE
				post_status = 'all' AND
				post_type = 'notice_new' AND
				post_type_secondary = 'community'
			ORDER BY
				ID DESC
	";
	$stmt = $wdb->prepare( $query );
// 	$stmt->bind_param( 'i', $post_id );
	$stmt->execute();
	return $wdb->get_results($stmt);
}


/*
 * Desc : 개별 책에 대한 공지
 */
function lps_get_community_notice_by_book( $book_id ) {
	global $wdb;
	
	$query = "
			SELECT
				p.ID,
			    p.post_title,
			    p.post_date,
				p.post_name
			FROM
				bt_posts AS p
			LEFT JOIN
				bt_posts_meta AS pm
			ON
				p.ID = pm.post_id
			LEFT JOIN
				bt_users AS u
			ON
				p.post_user_id = u.ID
			WHERE
			    pm.meta_key = 'wps_notice_books' AND
				pm.meta_value LIKE ?
			ORDER BY
				p.ID DESC
	";
	$stmt = $wdb->prepare( $query );
	$param = '%\"' . $book_id . '\"%';
	$stmt->bind_param( 's', $param );
	$stmt->execute();
	return $wdb->get_results($stmt);
}

/*
 * Desc : 모든 담벼락 공지와 개별 책 공지를 최근 순으로 
 */
function lps_get_all_community_notice( $book_id ) {
	$arr1 = lps_get_community_notice();
	$arr2 = lps_get_community_notice_by_book( $book_id );
	
	$arr3 = array_merge($arr1, $arr2);
	rsort($arr3);
	return $arr3;
}

/*
 * Desc : 북톡 시스템 공지, App만
 */
function lps_get_app_notice() {
	global $wdb;

	$query = "
			SELECT
				ID,
				post_title,
				post_date,
				post_name
			FROM
				bt_posts
			WHERE
				post_status = 'open' AND
				post_type = 'notice_new' AND
				post_type_area LIKE 'app%'
			ORDER BY
				ID DESC
	";
	$stmt = $wdb->prepare( $query );
	$stmt->execute();
	return $wdb->get_results($stmt);
}


/*
 * Desc : 조회수 업데이트 후 조회수 리턴
 */
function lps_update_post_view_count( $post_id ) {
	$view_count = wps_get_post_meta($post_id, 'wps_post_view_count');
	$view_count += 1;
	wps_update_post_meta($post_id, 'wps_post_view_count', $view_count);
	return $view_count;
}

/*
 * Desc : 게시글을 올린 회원인지 체크
 */
function lps_is_post_author( $post_id, $user_id ) {
	global $wdb;

	$query = "
			SELECT
				id
			FROM
				bt_posts
			WHERE
				ID = ? AND
				post_user_id = ?
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'ii', $post_id, $user_id );
	$stmt->execute();
	return $wdb->get_var($stmt);
}


/*
 * Desc : 읽지 않은 공지사항 갯수
 * 	parameter last_dt : 최종적으로 notice_list.php 를 접속한 날짜임.
 */
function lps_get_unread_notice( $last_dt ) {
	global $wdb;

	$query = "
			SELECT
				COUNT(*) AS count
			FROM
				bt_posts
			WHERE
				post_date > ? AND
				post_type = 'notice_new' AND
				post_type_area LIKE 'app%'
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 's', $last_dt );
	$stmt->execute();
	return $wdb->get_var($stmt);
}

/*
 * Desc : QnA 종류의 게시글에서 질문 등록하기
 */
function wps_add_post_question() {
	global $wdb;

	$post_type = empty($_POST['post_type']) ? 'qna' : $_POST['post_type'];
	$post_name = empty($_POST['post_name']) ? wps_get_current_user_name() : $_POST['post_name'];
	$post_content = empty($_POST['post_content']) ? '' : $_POST['post_content'];
	$post_date = empty($_POST['post_date']) ? date('Y-m-d H:i:s') : $_POST['post_date'];
	$post_title = empty($_POST['post_title']) ? '' : $_POST['post_title'];
	$post_term_id = empty($_POST['post_term_id']) ? '0' : $_POST['post_term_id'];
	$post_user_id = empty($_POST['post_user_id']) ? wps_get_current_user_id(): $_POST['post_user_id'];
	$post_status = empty($_POST['post_status']) ? 'waiting' : $_POST['post_status'];
	
	// File Attachment
	if ( !empty($_POST['file_path']) ) {
		$yyyymm = date('Ym');
		$upload_dir = UPLOAD_PATH . '/qna/' . $yyyymm;
		$upload_url = UPLOAD_URL . '/qna/' . $yyyymm;
		$meta_value = array();
	
		if ( !is_dir($upload_dir) ) {
			mkdir($upload_dir, 0777, true);
		}
	
		foreach ( $_POST['file_path'] as $key => $val ) {
			$file_ext = strtolower(pathinfo( $val, PATHINFO_EXTENSION ));
			$file_name = basename($_POST['file_name'][$key]);
	
			if ( in_array($file_ext, unserialize(WPS_IMAGE_EXT)) ) {
				$new_file_name = wps_make_rand() . '.' . $file_ext;
			} else {
				$new_file_name = wps_make_rand();
			}
	
			$new_val['file_path'] = $upload_dir . '/' . $new_file_name;
			$new_val['file_url'] = $upload_url . '/' . $new_file_name;
			$new_val['file_name'] = $file_name;
			$result = rename( $_POST['file_path'][$key], $new_val['file_path'] );
			array_push($meta_value, $new_val);
		}
	
		$meta_value = serialize( $meta_value );
	} else {
		$meta_value = '';
	}

	$query = "
			INSERT INTO
				bt_posts_qnas
				(
					ID,
					post_type,
					post_name,
					post_content,
					post_title,
					post_date,
					post_term_id,
					post_user_id,
					post_status,
					post_attachment
				)
			VALUES
				(
					NULL, ?, ?, ?, ?,
					?, ?, ?, ?, ?
				)
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'sssssiiss',
			$post_type,
			$post_name,
			$post_content,
			$post_title,
			$post_date,
			$post_term_id,
			$post_user_id,
			$post_status,
			$meta_value
	);
	$stmt->execute();
	return $wdb->insert_id;
}

function wps_get_post_qnas( $post_id ) {
	global $wdb;

	$query = "
			SELECT
				*
			FROM
				bt_posts_qnas
			WHERE
				ID = ?
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'i', $post_id );
	$stmt->execute();
	return $wdb->get_row($stmt);
}

/*
 * Desc : QnA 종류의 게시글에서 답변하기
 */
function wps_add_post_answer() {
	global $wdb;

	$post_id = $_POST['post_id'];
	$post_content = empty($_POST['post_content']) ? '' : $_POST['post_content'];
	$post_title = empty($_POST['post_title']) ? '' : $_POST['post_title'];
	$post_user_id = empty($_POST['post_user_id']) ? wps_get_current_user_id(): $_POST['post_user_id'];

	$query = "
			UPDATE
				bt_posts_qnas
			SET
				post_status = 'close',
				post_ans_title = ?,
				post_ans_content = ?,
				post_ans_user_id = ?,
				post_ans_date = NOW()
			WHERE
				ID = ?
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'sssi',
			$post_title,
			$post_content,
			$post_user_id,
			$post_id
	);
	return $stmt->execute();
}

function wps_delete_post_qnas( $post_id, $user_id = NULL ) {
	global $wdb;

	if (empty($user_id)) {	// for admin
		$query = "
				DELETE FROM
					bt_posts_qnas
				WHERE
					ID = ?
		";
		$stmt = $wdb->prepare( $query );
		$stmt->bind_param( 'i', $post_id );
	} else {
		$query = "
				DELETE FROM
					bt_posts_qnas
				WHERE
					ID = ? AND
					post_user_id = ?
		";
		$stmt = $wdb->prepare( $query );
		$stmt->bind_param( 'ii', $post_id, $user_id );
	}
	$stmt->execute();
	return $wdb->affected_rows;
}

?>