<?php
function lps_add_activity() {
	global $wdb;

	$user_id = wps_get_current_user_id();
	$book_id = $_POST['book_id'];
	$act_title = $_POST['act_title'];
	$act_content = $_POST['act_content'];
	
	$component = empty($_POST['component']) ? 'activity' : $_POST['component'];
	$type = empty($_POST['type']) ? 'activity_update' : $_POST['type'];
	$item_id = empty($_POST['item_id']) ? 0 : $_POST['item_id'];
	$secondary_item_id = empty($_POST['secondary_item_id']) ? 0 : $_POST['secondary_item_id'];
	$hide_sitewide = empty($_POST['hide_sitewide']) ? 0 : $_POST['hide_sitewide'];
	
	$query = "
			INSERT INTO
				bt_activity
				(
					id,
					user_id,
					book_id,
					component,
					type,
					subject,
					content,
					item_id,
					secondary_item_id,
					hide_sitewide,
					created_dt,
					count_hit,
					count_like,
					count_comment
													
				)
			VALUES
				(
					NULL, ?, ?, ?, ?,
					?, ?, ?, ?, ?, 
					NOW(), 0, 0, 0
				)
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'iissssiii',
			$user_id, $book_id, $component, $type, $act_title, $act_content, $item_id, $secondary_item_id, $hide_sitewide
	);
	$stmt->execute();

	$ID = $wdb->insert_id;
	
	// File Attachment
	if ( $ID && !empty($_POST['file_path']) ) {
		$yyyymm = date('Ym');
		$upload_dir = UPLOAD_PATH . '/community/' . $yyyymm . '/' . $book_id;
		$upload_url = UPLOAD_URL . '/community/' . $yyyymm . '/' . $book_id;
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
		lps_update_activity_meta( $ID, 'wps-community-attachment', $meta_value );
	}
	
	return $ID;
}


/*
 * Desc : 게시글 조회
 */
function lps_get_activity( $activity_id ) {
	global $wdb;

	$query = "
		SELECT
			*
		FROM
			bt_activity
		WHERE
			ID = ? AND
			is_deleted = '0'
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param('i', $activity_id);
	$stmt->execute();
	return $wdb->get_row($stmt);
}

/*
 * Desc : 게시글 삭제는 is_deleted 를 '1'로 업데이트
 */
function lps_delete_status_activity( $activity_id, $user_id = NULL ) {
	global $wdb;
	
	if (empty($user_id)) {
		$query = "
				UPDATE
					bt_activity
				SET
					is_deleted = '1',
					deleted_dt = CURDATE()
				WHERE
					id = ?
			";
		$stmt = $wdb->prepare( $query );
		$stmt->bind_param('i', $activity_id);
	} else {
		$query = "
				UPDATE
					bt_activity
				SET
					is_deleted = '1',
					deleted_dt = CURDATE()
				WHERE
					id = ? AND
					user_id = ?
			";
		$stmt = $wdb->prepare( $query );
		$stmt->bind_param('ii', $activity_id, $user_id);
	}
	$stmt->execute();
	return $wdb->affected_rows;
}

/*
 * Desc : 게시글 삭제
 */
function lps_delete_activity( $activity_id, $user_id ) {
	global $wdb;
	
	lps_delete_activity_comment_by_activity($activity_id);
	lps_delete_activity_meta($activity_id);
	
	$query = "
		DELETE FROM
			bt_activity
		WHERE
			id = ? AND
			user_id = ?
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param('ii', $activity_id, $user_id);
	$stmt->execute();
	return $wdb->affected_rows;
}

function lps_update_activity_meta( $activity_id, $meta_key, $meta_value ) {
	global $wdb;

	$query = "
			SELECT
				meta_id
			FROM
				bt_activity_meta
			WHERE
				activity_id = ? AND
				meta_key = ?
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'is', $activity_id, $meta_key );
	$stmt->execute();
	$meta_id = $wdb->get_var($stmt);

	if ( empty($meta_id) ) {
		$query = "
				INSERT INTO
					bt_activity_meta
					(
						meta_id,
						activity_id,
						meta_key,
						meta_value
					)
				VALUES
					(
						NULL, ?, ?, ?
					)
		";
		$stmt = $wdb->prepare( $query );
		$stmt->bind_param( 'iss', $activity_id, $meta_key, $meta_value );
		$stmt->execute();

		return $wdb->insert_id;
	} else {
		$query = "
			UPDATE
				bt_activity_meta
			SET
				meta_value = ?
			WHERE
				activity_id = ? AND
				meta_key = ?
		";
		$stmt = $wdb->prepare( $query );
		$stmt->bind_param( 'sis', $meta_value, $activity_id, $meta_key );
		$stmt->execute();
		return $wdb->affected_rows;
	}
}

function lps_delete_activity_meta( $activity_id ) {
	global $wdb;

	$query = "
		DELETE FROM
			bt_activity_meta
		WHERE
			activity_id = ?
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param('i', $activity_id);
	return $stmt->execute();
}


function lps_get_activity_meta( $activity_id, $meta_key = NULL ) {
	global $wdb;

	if ( empty($meta_key) ) {
		$query = "
				SELECT
					*
				FROM
					bt_activity_meta
				WHERE
					activity_id = ?
		";
		$stmt = $wdb->prepare( $query );
		$stmt->bind_param( 'i', $activity_id );
		$stmt->execute();
		return $wdb->get_results($stmt);
	} else {
		$query = "
				SELECT
					meta_value
				FROM
					bt_activity_meta
				WHERE
					activity_id = ? AND
					meta_key = ?
		";
		$stmt = $wdb->prepare( $query );
		$stmt->bind_param( 'is', $activity_id, $meta_key );
		$stmt->execute();
		return $wdb->get_var($stmt);
	}
}

function lps_add_activity_comment() {
	global $wdb;

	$activity_id = $_POST['activity_id'];
	$user_id = empty($_POST['user_id']) ? wps_get_current_user_id() : $_POST['user_id'];
	$user_level = empty($_POST['user_level']) ? wps_get_user_level() : $_POST['user_level'];
	$comment_date = date('Y-m-d H:i:s');
	$comment_content = $_POST['comment'];
	$comment_author = empty($_POST['comment_author']) ? wps_get_current_user_display_name() : $_POST['comment_author'];
	$comment_author_ip = empty($_POST['comment_author_ip']) ? $_SERVER['REMOTE_ADDR'] : $_POST['comment_author_ip'];
	$comment_read = empty($_POST['comment_read']) ? 0 : $_POST['comment_read'];
	
	$query = "
			INSERT INTO
				bt_activity_comment
				(
					comment_id,
					comment_author,
					comment_author_ip,
					comment_date,
					comment_content,
					comment_read,
					activity_id,
					comment_user_id,
					comment_user_level
				)
			VALUES
				(
					NULL, ?, ?, ?, ?,
					?, ?, ?, ?
				)
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'ssssiiii',
			$comment_author,
			$comment_author_ip,
			$comment_date,
			$comment_content,
			$comment_read,
			$activity_id,
			$user_id,
			$user_level
	);
	$stmt->execute();
	$comment_id = $wdb->insert_id;

	if ($comment_id) {
		$query = "
				UPDATE
					bt_activity
				SET
					count_comment = count_comment + 1
				WHERE
					id = ?
		";
		$stmt = $wdb->prepare( $query );
		$stmt->bind_param( 'i', $activity_id );
		$stmt->execute();
		$result = $wdb->affected_rows;
	}
	
	return $comment_id;
}


function lps_get_activity_comment( $comment_id ) {
	global $wdb;

	$query = "
			SELECT
				*
			FROM
				bt_activity_comment
			WHERE
				comment_id = ?
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'i', $comment_id );
	$stmt->execute();
	return $wdb->get_row($stmt);
}

/*
 * Desc : 게시글 본인 댓글 삭제
 */
function lps_delete_activity_comment( $comment_id, $user_id ) {
	global $wdb;

	$cmt_rows = lps_get_activity_comment($comment_id);
	$activity_id = $cmt_rows['activity_id'];	// for count_comment
	
	$query = "
			DELETE FROM
				bt_activity_comment
			WHERE
				comment_id = ? AND
				comment_user_id = ?
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'ii', $comment_id, $user_id );
	$stmt->execute();
	$result = $wdb->affected_rows;
	
	if ($result) {
		$query = "
				UPDATE
					bt_activity
				SET
					count_comment = count_comment - 1
				WHERE
					id = ?
		";
		$stmt = $wdb->prepare( $query );
		$stmt->bind_param( 'i', $activity_id );
		$stmt->execute();
	}
	
	return $result;
}

/*
 * Desc : 게시글의 댓글 삭제 CMS
 */
function lps_delete_activity_comment_by_cms( $comment_id ) {
	global $wdb;

	$cmt_rows = lps_get_activity_comment($comment_id);
	$activity_id = $cmt_rows['activity_id'];	// for count_comment
	
	$query = "
			DELETE FROM
				bt_activity_comment
			WHERE
				comment_id = ?
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'i', $comment_id );
	$stmt->execute();
	$result = $wdb->affected_rows;
	
	if ($result) {
		$query = "
				UPDATE
					bt_activity
				SET
					count_comment = count_comment - 1
				WHERE
					id = ?
		";
		$stmt = $wdb->prepare( $query );
		$stmt->bind_param( 'i', $activity_id );
		$stmt->execute();
	}
	
	return $result;
}

/*
 * Desc : activity에 속한 comment 삭제
 */
function lps_delete_activity_comment_by_activity( $activity_id ) {
	global $wdb;

	$query = "
			DELETE FROM
				bt_activity_comment
			WHERE
				activity_id = ?
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'i', $activity_id );
	$stmt->execute();
	return $wdb->affected_rows;
}

/*
 * Desc : 게시글의 모든 댓글들
 */
function lps_get_activity_comments( $activity_id ) {
	global $wdb;

	$query = "
			SELECT
				*
			FROM
				bt_activity_comment
			WHERE
				activity_id = ?
			ORDER BY
				comment_id ASC
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'i', $activity_id );
	$stmt->execute();
	return $wdb->get_results($stmt);
}

/*
 * Desc : 개시글 추천하기 (중복 금지)
 */
function lps_recommand_activity( $activity_id, $user_id ) {
	global $wdb;
	
	$result = 0;	// 참여 여부 플래그
	$meta_key = 'lps_activity_recommand_users';
	
	// 참여 여부 확인
	if (lps_check_recommand_activity($activity_id, $user_id)) {	// 추천한 적이 있으면
		$rec_users = lps_get_activity_meta($activity_id, $meta_key);
		$unserial = unserialize($rec_users);
		$result = 1;
		
	} else {	// 추천한 적이 없으면
		$rec_users = lps_get_activity_meta($activity_id, $meta_key);
		$unserial = empty(unserialize($rec_users)) ? array() : unserialize($rec_users);
		array_push($unserial, $user_id);
		
		$meta_value = serialize($unserial);
		lps_update_activity_meta($activity_id, $meta_key, $meta_value);
		
		$query = "
				UPDATE
					bt_activity
				SET
					count_like = count_like + 1
				WHERE
					id = ?
		";
		$stmt = $wdb->prepare( $query );
		$stmt->bind_param( 'i', $activity_id );
		$stmt->execute();
	}
	
	$count = count($unserial);
	
	return compact('result', 'count');
}

/*
 * Desc : 게시글에 대한 사용자의 추천 여부 확인
 */
function lps_check_recommand_activity( $activity_id, $user_id ) {
	$meta_key = 'lps_activity_recommand_users';
	
	$rec_users = lps_get_activity_meta($activity_id, $meta_key);
	$unserial = unserialize($rec_users);
	
	if (!empty($unserial)) {	// 아이디 체크
		if (in_array($user_id, $unserial)) {
			return true;
		}
	}
	return false;
}

/*
 * Desc : 게시글을 올린 회원인지 체크
 */
function lps_is_activity_author( $activity_id, $user_id ) {
	global $wdb;

	$query = "
			SELECT
				id
			FROM
				bt_activity
			WHERE
				id = ? AND
				user_id = ?
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'ii', $activity_id, $user_id );
	$stmt->execute();
	return $wdb->get_var($stmt);
}


function lps_update_activity_view_count( $activity_id ) {
	global $wdb;
	
	$query = "
			UPDATE
				bt_activity
			SET
				count_hit = count_hit + 1
			WHERE
				id = ?
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'i', $activity_id );
	$stmt->execute();
	return $wdb->affected_rows;
}

/*
 * Desc : 읽지 않은 댓글 수, 책 알림 끄기의 책은 제외
 */
function lps_get_unread_activity_comment( $user_id ) {
	global $wdb;

	$meta_value = wps_get_user_meta($user_id, 'lps_activity_alarm_off');
	$off_books = @unserialize($meta_value);

	if ( !empty($off_books ) ) {
		$books_exclude = implode(',', $off_books);
		$query = "
				SELECT 
					COUNT(*) AS count
				FROM
					bt_activity AS a
				LEFT JOIN
					bt_activity_comment AS c
				ON
					a.id = c.activity_id
				WHERE
					a.user_id = ? AND
				    c.comment_read = 0 AND
					a.book_id NOT IN ( $books_exclude )
		";
	} else {		
		$query = "
				SELECT 
					COUNT(*) AS count
				FROM
					bt_activity AS a
				LEFT JOIN
					bt_activity_comment AS c
				ON
					a.id = c.activity_id
				WHERE
					a.user_id = ? AND
				    c.comment_read = 0
		";
	}
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'i', $user_id );
	$stmt->execute();
	return $wdb->get_var($stmt);
}

/*
 * Desc : 게시글에 딸린 댓글을 읽음으로 업데이트, 본인 확인 필터링 후 호출
 */
function lps_update_activity_comment_read( $activity_id ) {
	global $wdb;
	
	$query = "
			UPDATE
				bt_activity_comment
			SET
				comment_read = 1
			WHERE
				activity_id = ?
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'i', $activity_id );
	$stmt->execute();
	return $wdb->affected_rows;
}


/*
 * Desc : 읽지 않은 담벼락의 공지사항 갯수
 * 	parameter last_dt : 최종적으로 notice_list.php 를 접속한 날짜임.
 */
function lps_get_unread_community_notice( $book_id, $last_dt ) {
	global $wdb;

	$query = "
			SELECT
				COUNT(*) AS count
			FROM
				bt_posts AS p
			LEFT JOIN
				bt_posts_meta AS pm
			ON
				p.ID = pm.post_id
			WHERE
				p.post_date > ? AND
				p.post_status = 'all' AND
				p.post_type = 'notice_new' AND
				p.post_type_secondary = 'community' OR
				pm.meta_key = 'wps_notice_books' AND
				pm.meta_value LIKE ?
	";
	$stmt = $wdb->prepare( $query );
	$param = '%\"' . $book_id . '\"%';
	$stmt->bind_param( 'ss', $last_dt, $param );
	$stmt->execute();
	return $wdb->get_var($stmt);
}

/*
 * Desc : 대기중 1:1 문의 Dashboard
 */
function lps_get_total_waiting_qna() {
	global $wdb;

	$query = "
			SELECT
				COUNT(*)
			FROM
				bt_posts_qnas
			WHERE
				post_type = 'qna_new' AND
			    post_status = 'waiting'
	";
	$stmt = $wdb->prepare( $query );
	$stmt->execute();
	return $wdb->get_var($stmt);
}

/*
 * Desc : 10개 이상의 추천 등록된 게시글 개수
 * 		hide_sitewide : 확인 여부 ( 0 : 미확인, 1: 확인 )
 */
function lps_get_fav_above_10( $user_id ) {
	global $wdb;

	$meta_value = wps_get_user_meta($user_id, 'lps_activity_alarm_off');
	$off_books = @unserialize($meta_value);

	if ( !empty($off_books ) ) {
		$books_exclude = implode(',', $off_books);
		$query = "
				SELECT 
					COUNT(*) AS count
				FROM
					bt_activity AS a
				WHERE
					a.user_id = ? AND
				    a.count_like > 9 AND
				    a.hide_sitewide = '0' AND
					a.book_id NOT IN ( $books_exclude )
		";
	} else {		
		$query = "
				SELECT 
					COUNT(*) AS count
				FROM
					bt_activity AS a
				WHERE
					a.user_id = ? AND
				    a.count_like > 9 AND
				    a.hide_sitewide = '0'
		";
	}
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'i', $user_id );
	$stmt->execute();
	return $wdb->get_var($stmt);
}

function lps_update_fav_above_10( $activity_id ) {
	global $wdb;
	
	$query = "
			UPDATE
				bt_activity
			SET
				hide_sitewide = '1'
			WHERE
				id = ? AND
				count_like > 9 AND
				hide_sitewide = '0'
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'i', $activity_id );
	$stmt->execute();
	return $wdb->affected_rows;
}

?>