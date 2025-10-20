<?php
/*
 * Desc : 관리자 여부 리턴
 */
function wps_is_admin() {
	if ( wps_get_user_level() == 10 ) {
		return true;
	}
	return false;
}
/*
 * Desc : 1인 작가 이상의 권한
 */
function wps_is_agent() {
	if ( wps_get_user_level() > 5 ) {
		return true;
	}
	return false;
}

/*
 * Desc : 춮판사 권한
 */
function wps_is_publisher() {
	if ( wps_get_user_level() == 6 || wps_get_user_level() == 7 ) {
		return true;
	}
	return false;
}

/*
 * Desc : user ID 리턴
 */
function wps_get_current_user_id() {
	if ( !empty( $_SESSION['login'] ) ) {
		return (int) $_SESSION['login']['userid'];
	}
	return 0;
}

/*
 * Desc : user Name 리턴
 */
function wps_get_current_user_name() {
	if ( !empty( $_SESSION['login'] ) ) {
		return $_SESSION['login']['user_name'];
	}
	return '';
}

/*
 * Desc : user login ID 리턴
 */
function wps_get_current_user_login() {
	if ( !empty( $_SESSION['login'] ) ) {
		return $_SESSION['login']['user_login'];
	}
	return '';
}

function wps_get_user_level() {
	if ( !empty( $_SESSION['login'] ) ) {
		return $_SESSION['login']['user_level'];
	} else {
		return 0;
	}
}

/*
 * Desc : user Display Name 리턴
 */
function wps_get_current_user_display_name() {
	if ( !empty( $_SESSION['login'] ) ) {
		return $_SESSION['login']['display_name'];
	}
	return '';
}

/*
 * Desc : 사용자 인증
 */
function wps_auth_redirect() {
	if ( !wps_get_current_user_id() ) {
		$redirect = base64_encode($_SERVER['REQUEST_URI']);
		wps_redirect( CONTENT_URL . '/users/user_login.php?redirect=' . $redirect );
	}
}

/*
 * Desc : 사용자 인증 : 모바일
 */
function wps_auth_mobile_redirect() {
	if ( !wps_get_current_user_id() ) {
		$redirect = base64_encode($_SERVER['REQUEST_URI']);
		wps_redirect( MOBILE_URL . '/users/user_login.php?redirect=' . $redirect );
	}
}

/*
 * Desc : 관리자 존재 여부
 */
function wps_exist_admin() {
	global $wdb;

	$query = "
			SELECT
				COUNT(*) AS cnt
			FROM
				bt_users AS u
			INNER JOIN
				bt_users_meta AS m
			WHERE
				u.ID = m.user_id AND
				m.meta_key = 'wps_user_level' AND
				m.meta_value = '10'
	";
	$stmt = $wdb->prepare( $query );
	$stmt->execute();
	return $wdb->get_var($stmt);
}

function wps_add_admin() {
	return lps_update_admin();
// 	return wps_edit_admin( $user_id );
}

/*
 * Desc : 관리자 등록/수정 
 */
function lps_update_admin() {
	global $wdb;
	
	$ID = empty($_POST['ID']) ? 0 : $_POST['ID'];
	
	$user_login = $_POST['userid'];
	$user_pass = wps_get_password($_POST['userpw']);
	$user_name = empty($_POST['user_name']) ? '관리자' : $_POST['user_name'];
	$user_email = $user_login;
// 	$user_email = empty($_POST['user_email']) ? '' : $_POST['user_email'];
	$user_status = empty($_POST['user_status']) ? '0' : $_POST['user_status'];
	$user_level = 10;
	$display_name = 'BookTalk';
	
	if ( empty($ID) ) {
		$query = "
				INSERT INTO
					bt_users
					(
						ID,
						user_login,
						user_pass,
						user_name,
						user_email,
						display_name,
						user_registered,
						user_status,
						user_level
					)
				VALUES
					(
						NULL, ?, ?, ?, ?,
						?, NOW(), ?, ?
					)
		";
		$stmt = $wdb->prepare( $query );
		$stmt->bind_param( 'sssssii', $user_login, $user_pass, $user_name, $user_email, $display_name, $user_status, $user_level );
		$stmt->execute();
		
		var_dump($wdb->error);
		
		$ID = $wdb->insert_id;
		
		if ( $ID ) {
			wps_update_user_meta( $ID, 'wps_user_level', $user_level );
		}
		return $ID;
		
	} else {
		$query = "
				UPDATE
					bt_users
				SET
					user_login = ?,
					user_name = ?,
					user_email = ?,
					user_status = ?
				WHERE
					ID = ?
		";
		$stmt = $wdb->prepare($query);
		$stmt->bind_param( 'ssssi', $user_login, $user_name, $user_email, $user_status, $ID );
		$result = $stmt->execute();
		
		wps_update_user_meta( $user_id, 'wps_modified_dt', date("Y-m-d H:i:s") );
		
		return $result;
	}
}

/*
 * Desc : 회원 수정
 */
function lps_update_user() {
	global $wdb;
	
	if ( empty($_POST['user_id']) ) {
		$user_id = wps_get_current_user_id();
	} else {
		$user_id = $_POST['user_id'];
	}
	
	$user_login = $_POST['user_login'];
	$user_name = $_POST['user_name'];
	$display_name = empty($_POST['display_name']) ? '': $_POST['display_name'];
	$user_pass = empty($_POST['user_pass']) ? '' : wps_get_password($_POST['user_pass']);
	$user_email = $user_login;
// 	$user_email = empty($_POST['user_email']) ? '' : $_POST['user_email'];
	$mobile = empty($_POST['mobile']) ? '' : $_POST['mobile'];
	$birthday = empty($_POST['birthday']) ? '' : $_POST['birthday'];
	$gender = empty($_POST['gender']) ? '' : $_POST['gender'];
	$join_path = empty($_POST['join_path']) ? '' : $_POST['join_path'];
	$residence = empty($_POST['residence']) ? '0' : $_POST['residence'];
	$last_school = empty($_POST['last_school']) ? '0': $_POST['last_school'];
	
	$user_status = empty($_POST['user_status']) ? '0' : $_POST['user_status'];
	$user_level = empty($_POST['user_level']) ? '1' : $_POST['user_level'];
	$quit_reason = empty($_POST['quit_reason']) ? '' : $_POST['quit_reason'];	// 탈퇴사유
	
	// 탈퇴 처리
	/*
	 * Desc : 탈퇴처리
	 * 		아이디, 닉네임, 회원등급을 제외한 모든 기본 정보는 Nullify
	 */
	if ($user_status == '4') {	// 탈퇴
		
		$user_row = wps_get_user($user_id);
		
		$query = "
				UPDATE
					bt_users
				SET
					user_pass = 'withdraw',
					user_name = '',
					user_email = '',
					user_status = 4,
					mobile = '',
					birthday = '',
					gender = '',
					residence = 0,
					last_school = 0,
					join_path = ''
				WHERE
					ID = ?
		";
		$stmt = $wdb->prepare($query);
		$stmt->bind_param( 'i', $user_id );
		$result = $stmt->execute();
		
		if ($result) {
			$user_row['quit_reason'] = $quit_reason;
			$user_row['quit_dt'] = date('Y-m-d H:i:s');
			$user_row['quit_by'] = wps_get_current_user_name();
			$meta_value = serialize($user_row);
			
			wps_update_user_meta( $user_id, 'wps_user_withdraw_log', $meta_value );
		}
		
	} else {
		
		if (empty($user_pass)) {
			$query = "
					UPDATE
						bt_users
					SET
						user_login = ?,
						user_name = ?,
						user_email = ?,
						display_name = ?,
						user_status = ?,
						user_level = ?,
						mobile = ?,
						birthday = ?,
						gender = ?,
						residence = ?,
						last_school = ?,
						join_path = ?
					WHERE
						ID = ?
			";
			$stmt = $wdb->prepare($query);
			$stmt->bind_param( 'sssssssssiisi', 
					$user_login, $user_name, $user_email, $display_name, $user_status,
					$user_level, $mobile, $birthday, $gender, $residence, 
					$last_school, $join_path, $user_id
			);
		} else {
			$query = "
					UPDATE
						bt_users
					SET
						user_login = ?,
						user_pass = ?,
						user_name = ?,
						user_email = ?,
						display_name = ?,
						user_status = ?,
						user_level = ?,
						mobile = ?,
						birthday = ?,
						gender = ?,
						residence = ?,
						last_school = ?,
						join_path = ?
					WHERE
						ID = ?
			";
			$stmt = $wdb->prepare($query);
			$stmt->bind_param( 'ssssssssssiisi',
					$user_login, $user_pass, $user_name, $user_email, $display_name, 
					$user_status, $user_level, $mobile, $birthday, $gender, 
					$residence, $last_school, $join_path, $user_id
			);
		}
		$result = $stmt->execute();
		
		if ( $result ) {
			wps_update_user_meta( $user_id, 'wps_user_level', $user_level );
			wps_update_user_meta( $user_id, 'wps_modified_dt', date("Y-m-d H:i:s") );
		}
	}
	return $result;
}

/*
 * Desc : 회원 정보 수정 시 아이디를 변경할 때 새로운 아이디가 존재하는 지 검사
 */
function lps_taken_user_login( $user_id, $user_login ) {
	global $wdb;
	
	$query = "
			SELECT
				ID
			FROM
				bt_users
			WHERE
				ID != ? AND
				user_login = ?
	";
	$stmt = $wdb->prepare($query);
	$stmt->bind_param('is',  $user_id, $user_login);
	$stmt->execute();
	$ID = $wdb->get_var($stmt);
	return $ID;
}

/*
 * Desc : 회원 탈퇴
 */
function lps_withdraw_user( $user_id = NULL ) {
	global $wdb;
	
	if ( empty($user_id) ) {
		$user_id = wps_get_current_user_id();
	}
	
	return wps_update_user_status( $user_id, 4 );
}

/*
 * Desc : 회원아이디 사용 여부 체크
 */
function lps_check_user_login( $user_login ) {
	global $wdb;

	$query = "
		SELECT
			ID
		FROM
			bt_users
		WHERE
			user_login = ?
	";
	$stmt = $wdb->prepare($query);
	$stmt->bind_param('s', $user_login);
	$stmt->execute();
	$ID = $wdb->get_var($stmt);
	return $ID;
}

/*
 * Desc : 닉네임 사용 여부 체크
 */
function lps_check_display_name( $display_name ) {
	global $wdb;

	$query = "
		SELECT
			ID
		FROM
			bt_users
		WHERE
			display_name = ?
	";
	$stmt = $wdb->prepare($query);
	$stmt->bind_param('s', $display_name);
	$stmt->execute();
	$ID = $wdb->get_var($stmt);
	return $ID;
}

function wps_get_user( $user_id ) {
	global $wdb;
	
	$query = "
			SELECT
				*
			FROM
				bt_users
			WHERE
				ID = ?
	";
	
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'i', $user_id );
	$stmt->execute();
	return $wdb->get_row($stmt);
}

function wps_get_user_by( $field, $value ) {
	global $wdb;

	$value = $wdb->real_escape_string($value);

	$query = "
			SELECT
				*
			FROM
				bt_users
			WHERE
				$field = '$value'
	";
	$obj = $wdb->query( $query );
	return $wdb->get_row($obj);
}

function wps_get_password( $plain_text ) {
	global $wdb;
	
	$query = "SELECT PASSWORD(?)";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 's', $plain_text );
	$stmt->execute();
	return $wdb->get_var($stmt);
}

function wps_set_password( $password, $user_id = 0 ) {
	global $wdb;

	$user_pass = wps_get_password( $password );
	$ID = empty($user_id) ? wps_get_current_user_id() : $user_id;
	
	$query = "
			UPDATE
				bt_users
			SET
				user_pass = ?
			WHERE
				ID = ?
	";
	$stmt = $wdb->prepare($query);
	$stmt->bind_param( 'si', $user_pass, $ID );
	return $stmt->execute();
}

function wps_get_user_meta( $user_id, $meta_key = NULL ) {
	global $wdb;

	if ( $meta_key ) {
		$query = "
				SELECT
					meta_value
				FROM
					bt_users_meta
				WHERE
					user_id = ? AND
					meta_key = ?
		";
		$stmt = $wdb->prepare( $query );
		$stmt->bind_param( 'is', $user_id, $meta_key );
		$stmt->execute();
		return $wdb->get_var($stmt);
	} else {
		$query = "
				SELECT
					meta_key,
					meta_value
				FROM
					bt_users_meta
				WHERE
					user_id = ?
		";
		$stmt = $wdb->prepare( $query );
		$stmt->bind_param( 'i', $user_id );
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
 * Desc : 전체 회원 수
 */
function wps_count_users( $date = NULL ) {
	global $wdb;
	
	if ( !empty($date) ) {
		$cond_clause = "DATE(user_registered) = '$date' ";
	} else {
		$cond_clause = 1;
	}
	
	$query = "
			SELECT
				COUNT(*)
			FROM
				bt_users
			WHERE
				$cond_clause
	";
	$stmt = $wdb->prepare( $query );
	$stmt->execute();
	return $wdb->get_var($stmt);
}

/*
 * Desc : 회원 권한별 회원 수
 */
function wps_get_user_count_by_level() {
	global $wdb;

	$query = "
			SELECT
				m.meta_value AS user_level,
				COUNT(*) AS user_level_count
			FROM
				bt_users AS u
			LEFT JOIN
				bt_users_meta AS m
			ON
				u.ID = m.user_id
			WHERE
				m.meta_key = 'wps_user_level'
			GROUP BY
				1
	";
	$stmt = $wdb->prepare( $query );
	$stmt->execute();
	return $wdb->get_results($stmt);
}

function wps_update_user_status( $user_id, $value ) {
	global $wdb;

	$query = "
			UPDATE
				bt_users
			SET
				user_status = ?
			WHERE
				ID = ?
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'ii', $value, $user_id );
	return $stmt->execute();
}

function wps_update_user_meta( $user_id, $meta_key, $meta_value ) {
	global $wdb;

	$query = "
			SELECT
				umeta_id
			FROM
				bt_users_meta
			WHERE
				user_id = ? AND
				meta_key = ?
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'is', $user_id, $meta_key );
	$stmt->execute();
	$meta_id = $wdb->get_var($stmt);

	if ( empty($meta_id) ) {		// INSERT
		$query = "
				INSERT INTO
					bt_users_meta
					(
						umeta_id,
						user_id,
						meta_key,
						meta_value
					)
				VALUES
					(
						0, ?, ?, ?
					)
		";
		$stmt = $wdb->prepare( $query );
		$stmt->bind_param( 'iss', $user_id, $meta_key, $meta_value );
		$stmt->execute();

		return $wdb->insert_id;

	} else {		// UPDATE
		$query = "
			UPDATE
				bt_users_meta
			SET
				meta_value = ?
			WHERE
				user_id = ? AND
				meta_key = ?
		";
		$stmt = $wdb->prepare( $query );
		$stmt->bind_param( 'sis', $meta_value, $user_id, $meta_key );
		return $stmt->execute();
	}
}

function wps_delete_user( $user_id ) {
	global $wdb;

	$query = "
			SELECT
				ID
			FROM
				wps_posts
			WHERE
				post_user_id = ?
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'i', $user_id );
	$stmt->execute();
	$posts = $wdb->get_results($stmt);
	if ( !empty($posts) ) {
		foreach ( $posts as $key => $val ) {
			wps_delete_post( $val['ID'] );
		}
	}

	wps_delete_user_meta( $user_id );

	$query = "
			DELETE FROM
				bt_users
			WHERE
				ID = ?
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'i', $user_id );
	return $stmt->execute();
}

function wps_delete_user_meta( $user_id, $meta_key = NULL ) {
	global $wdb;

	if ( $meta_key ) {
		$query = "
				DELETE FROM
					bt_users_meta
				WHERE
					user_id = ? AND
					meta_key = ?
		";
		$stmt = $wdb->prepare( $query );
		$stmt->bind_param( 'is', $user_id, $meta_key );
		$stmt->execute();
	} else {
		$query = "
				DELETE FROM
					bt_users_meta
				WHERE
					user_id = ?
		";
		$stmt = $wdb->prepare( $query );
		$stmt->bind_param( 'i', $user_id );
		$stmt->execute();
	}
}

function wps_get_users( $qs ) {
	global $wdb;
	
	$query = "
			SELECT
				*
			FROM
				bt_users
			WHERE
				user_login LIKE ? OR
				user_email LIKE ? OR
				display_name LIKE ?
			ORDER BY
				display_name ASC
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'sss', $qs, $qs, $qs );
	
	$qs = '%' . $qs . '%';
	
	$stmt->execute();
	return $wdb->get_results($stmt);
}

/*
 * Desc : 회원등급 변경
 */
function lps_update_user_level() {
	global $wdb;
	
	$level = $_POST['change_level'];
	$ids = "'" . implode("','", $_POST['user_list']) . "'";
	
	$query = "
			UPDATE
				bt_users
			SET
				user_level = ?
			WHERE
				ID IN ($ids)
	";
	$stmt = $wdb->prepare($query);
	$stmt->bind_param( 's', $level );
	$stmt->execute();
	$affected_rows = $wdb->affected_rows;
	
	if ($affected_rows) {
		foreach ($_POST['user_list'] as $key => $val) {
			wps_update_user_meta( $val, 'wps_user_level', $level );
			wps_update_user_meta( $val, 'wps_modified_dt', date("Y-m-d H:i:s") );
		}
	}
	
	return $affected_rows;
}

/*
 * 회원 추가
 */
function lps_add_user() {
	global $wdb;
	
	$user_login = trim($_POST['user_login']);
	$user_name = trim($_POST['user_name']);
	$user_pass = wps_get_password($_POST['user_pass']);
	
	$display_name = empty($_POST['display_name']) ? '': trim($_POST['display_name']);
	$user_email = $user_login;
// 	$user_email = empty($_POST['user_email']) ? '' : $_POST['user_email'];
	$mobile = empty($_POST['mobile']) ? '' : $_POST['mobile'];
	$birthday = empty($_POST['birthday']) ? '' : $_POST['birthday'];
	$gender = empty($_POST['gender']) ? '' : $_POST['gender'];
	$join_path = empty($_POST['join_path']) ? '' : $_POST['join_path'];
	$residence = empty($_POST['residence']) ? '' : $_POST['residence'];
	$last_school = empty($_POST['last_school']) ? '' : $_POST['last_school'];
	
	$user_status = empty($_POST['user_status']) ? '0' : $_POST['user_status'];
	$user_level = empty($_POST['user_level']) ? '1' : $_POST['user_level'];
	
	$query = "
			INSERT INTO
				bt_users
				(
					ID,
					user_login,
					user_pass,
					user_name,
					user_email,
					display_name,
					user_registered,
					user_status,
					user_level,
					mobile,
					birthday,
					gender,
					residence,
					last_school,
					join_path,
					last_login_dt
				)
			VALUES
				(
					NULL, ?, ?, ?, ?,
					?, NOW(), ?, ?, ?,
					?, ?, ?, ?, ?,
					NOW()
				)
	";
	$stmt = $wdb->prepare($query);
	$stmt->bind_param( 'sssssiisssiis',
			$user_login, $user_pass, $user_name, $user_email, 
			$display_name, $user_status, $user_level, $mobile, 
			$birthday, $gender, $residence, $last_school, $join_path
	);
	$stmt->execute();
	$user_id = $wdb->insert_id;
	
	if ( $user_id ) {
		wps_update_user_meta( $user_id, 'wps_user_level', $user_level );
	}
	return $user_id;
}

function lps_send_mail( $to, $subject, $body, $from ) {
	
	if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
		return false;
	} else {

		if ( strpos($from, '<') === false ) {
			$sender_email = $from;
		} else {
			$from_exp = explode('<', $from);
			$sender_name = $from_exp[0];
			$pattern = '/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b/i';
			preg_match($pattern, $from_exp[1], $matches);
			$sender_email = $matches[0];
		}
				
		$charset = 'utf-8';
		$to_subject ='=?utf-8?B?' . base64_encode($subject) . '?=';
		$to_subject = iconv( 'utf-8', 'euc-kr', $to_subject);
		$mail_msg = base64_encode($body);
		
		$headers	= array();
		$headers[]	= 'MIME-Version: 1.0';
		$headers[]	= 'Content-type: text/html; charset=' . $charset;
		$headers[]	= 'Content-Transfer-Encoding: base64';
		
		if ( empty($sender_name) ) {
			$headers[]	= 'From: '. $sender_email;
		} else {
			$headers[]	= 'From: =?' . $charset . '?B?' . base64_encode($sender_name) . '?= <' . $sender_email . '>';
		}
		
// 		$headers[]	= 'From: =?' . $charset . '?B?' . $from;
// 		$headers[]	= empty($from_name) ? 'From: =?' . $charset . '?B?' . $from : 'From: =?' . $charset . '?B?' . base64_encode($from_name) . '?= <' . $from . '>';
		$headers[]	= 'X-Mailer: PHP/' . phpversion();
		
// 		ini_set( "SMTP", "mail.server-domain.com" );
		$mail_result = mail( $to, $to_subject, $mail_msg, implode("\r\n", $headers) );
		return $mail_result;
	}
}

function lps_send_mail_to_ids( $users, $subject, $body, $from ) {
	global $wdb;
	
	$sent_count = 0;
	
	$query = "
			SELECT
				ID,
				user_login,
				user_name
			FROM
				bt_users
			WHERE
				ID IN ($users)
	";
	$stmt = $wdb->prepare( $query );
	$stmt->execute();
	$user_rows = $wdb->get_results($stmt);
	
	foreach ($user_rows as $key => $val) {
		$user_id = $val['ID'];
		$email = $val['user_login'];
		if (lps_send_mail($email, $subject, $body, $from)) {
			$sent_count++;
			
			$serialize = serialize(array('subject' => $subject, 'message' => $body));
			lps_add_mail_logs($user_id, $serialize, 'dormancy_notice');
		}
	}
	
	return $sent_count;
}

/*
 * Desc : 콤마로 구분된 사용자의 이메일 주소(로그인 계정)
 */
function lps_get_user_emails( $user_ids ) {
	global $wdb;

	$query = "
			SELECT
				user_name,
				user_login
			FROM
				bt_users
			WHERE
				ID IN ($user_ids)
	";

	$stmt = $wdb->prepare( $query );
	$stmt->execute();
	return $wdb->get_results($stmt);
}

function lps_add_mail_logs( $user_id, $msg, $type ) {
	global $wdb;
	
	$query = "
			INSERT INTO
				bt_mail_logs
				(
					ID,
					user_id,
					message,
					mail_type,
					sent_dt
				)
			VALUES
				(
					NULL, ?, ?, ?, NOW()
				)
	";
	$stmt = $wdb->prepare($query);
	$stmt->bind_param('iss', $user_id, $msg, $type);
	$stmt->execute();
	return $wdb->insert_id;
}

function lps_get_user_avatar( $user_id = NULL ) {
	if (empty($user_id)) {
		$user_id = wps_get_current_user_id();
	}
	$user_avatar = wps_get_user_meta($user_id, 'wps_user_avatar');
	
	$avatar = empty($user_avatar) ? IMG_URL . '/common/photo-default.png' : $user_avatar;
	
	return $avatar;
}

/*
 * 회원 가입
 */
function lps_join_user() {
	global $wdb;

	$user_login = trim($_SESSION['join']['user_login']);
	$user_name = trim($_SESSION['join']['user_name']);
	$user_pass = wps_get_password($_SESSION['join']['user_pass']);

	$display_name = empty($_SESSION['join']['display_name']) ? '': trim($_SESSION['join']['display_name']);
	$user_email = $user_login;
	$mobile = empty($_SESSION['join']['mobile']) ? '' : lps_easy_to_read_mobile($_SESSION['join']['mobile']);
	$birthday = empty($_SESSION['join']['yy']) ? '' : $_SESSION['join']['yy'] . '-' . str_pad($_SESSION['join']['mm'], 2, '0', STR_PAD_LEFT) . '-' . str_pad($_SESSION['join']['dd'], 2, '0', STR_PAD_LEFT);
	$gender = empty($_SESSION['join']['gender']) ? '' : $_SESSION['join']['gender'];
	$join_path = 'mobile';
	$residence = empty($_SESSION['join']['residence']) ? '' : $_SESSION['join']['residence'];
	$last_school = empty($_SESSION['join']['last_school']) ? '' : $_SESSION['join']['last_school'];

	$user_status = empty($_SESSION['join']['user_status']) ? '0' : $_SESSION['join']['user_status'];
	$user_level = empty($_SESSION['join']['user_level']) ? '1' : $_SESSION['join']['user_level'];

	$query = "
			INSERT INTO
				bt_users
				(
					ID,
					user_login,
					user_pass,
					user_name,
					user_email,
					display_name,
					user_registered,
					user_status,
					user_level,
					mobile,
					birthday,
					gender,
					residence,
					last_school,
					join_path,
					last_login_dt
				)
			VALUES
				(
					NULL, ?, ?, ?, ?,
					?, NOW(), ?, ?, ?,
					?, ?, ?, ?, ?,
					NOW()
				)
	";
	$stmt = $wdb->prepare($query);
	$stmt->bind_param( 'sssssiisssiis',
			$user_login, $user_pass, $user_name, $user_email,
			$display_name, $user_status, $user_level, $mobile,
			$birthday, $gender, $residence, $last_school, $join_path
			);
	$stmt->execute();
	$user_id = $wdb->insert_id;

	if ( $user_id ) {
		wps_update_user_meta( $user_id, 'wps_user_level', $user_level );
	}
	return $user_id;
}

function lps_search_user( $q, $level = NULL ) {
	global $wdb;

	$sql = empty($level) ? "" : "AND user_level = '$level'"; 
	
	$query = "
			SELECT
				*
			FROM
				bt_users
			WHERE
				user_name LIKE ? OR
				display_name LIKE ?
				$sql
			ORDER BY
				user_name ASC
	";
	$stmt = $wdb->prepare( $query );
	$qs = '%'. $q . '%';
	$stmt->bind_param( 'ss', $qs, $qs );
	$stmt->execute();
	return $wdb->get_results($stmt);
}

/*
 * Desc : Web main 검색 > 책 정보중에서 출판사
 */
function lps_search_publisher_by_keyword( $q ) {
	global $wdb;

	$query = "
		SELECT
			u.ID,
			u.user_name
		FROM
			bt_users AS u
		INNER JOIN
			bt_books AS b
		WHERE
			-- u.user_level = '7' AND
			b.publisher LIKE ? AND
			u.ID = b.user_id
		GROUP BY
			u.ID
		ORDER BY
			u.user_name ASC
	";
	$stmt = $wdb->prepare( $query );
	$qs = '%' . $q . '%';
	$stmt->bind_param('s', $qs);
	$stmt->execute();
	return $wdb->get_results($stmt);
}

/*
 * Desc : Web main 검색 > 독자
 */
function lps_search_author_by_keyword( $q ) {
	global $wdb;

	$query = "
		SELECT
			u.ID,
			u.user_name
		FROM
			bt_users AS u
		INNER JOIN
			bt_books AS b
		WHERE
			-- u.user_level IN (3, 6) AND
			b.author LIKE ? AND
			u.ID = b.user_id
		GROUP BY
			u.ID
		ORDER BY
			u.user_name ASC
	";
	$stmt = $wdb->prepare( $query );
	$qs = '%' . $q . '%';
	$stmt->bind_param('s', $qs);
	$stmt->execute();
	return $wdb->get_results($stmt);
}

function lps_get_users_by( $field, $value ) {
	global $wdb;

	$value = $wdb->real_escape_string($value);

	$query = "
			SELECT
				*
			FROM
				bt_users
			WHERE
				$field = '$value'
	";
	$obj = $wdb->query( $query );
	return $wdb->get_results($obj);
}

/*
 * Desc : Web > 회원정보 수정
 */
function lps_update_user_optional() {
	global $wdb;
	
	$user_id = wps_get_current_user_id();
	
	$mobile = empty($_POST['mobile']) ? '' : $_POST['mobile'];
	$birthday = empty($_POST['birth_y']) || empty($_POST['birth_m']) || empty($_POST['birth_d']) ? '' : $_POST['birth_y'] . '-' . $_POST['birth_m'] . '-' . $_POST['birth_d'];
	$gender = empty($_POST['gender']) ? '' : $_POST['gender'];
	$residence = empty($_POST['residence']) ? '' : $_POST['residence'];
	$last_school = empty($_POST['last_school']) ? '' : $_POST['last_school'];
	
	$query = "
			UPDATE
				bt_users
			SET
				mobile = ?,
				birthday = ?,
				gender = ?,
				residence = ?,
				last_school = ?
			WHERE
				ID = ?
	";
	$stmt = $wdb->prepare($query);
	$stmt->bind_param( 'sssssi', $mobile, $birthday, $gender, $residence, $last_school, $user_id );
	return $stmt->execute();
}

/*
 * Desc : SNS ID를 meta value로부터 조회
 */
function lps_get_user_id_by_key_val( $mkey, $mval ) {
	global $wdb;
	
	$query = "
			SELECT
				user_id
			FROM
				bt_users_meta
			WHERE
				meta_key = ? AND
				meta_value = ?
	";
	$stmt = $wdb->prepare($query);
	$stmt->bind_param('ss', $mkey, $mval);
	$stmt->execute();
	return $wdb->get_var($stmt);
}

function lps_get_user_profile( $user_id ) {
	$umeta = wps_get_user_meta( $user_id );
	
	$profile_msg = @$umeta['wps_user_profile_msg'];
	$profile_avatar = @$umeta['wps_user_avatar'];
	if (empty($profile_avatar)) {
		$profile_avatar = IMG_URL . '/common/photo-default.png';
	}
	
	return compact('profile_avatar', 'profile_msg');
}

/*
 * Desc : 찜리스트에 추가
 * 		$book_id : int
 */
function lps_add_book_wishlist( $book_id, $uid = NULL ) {
	$user_id = empty($uid) ? wps_get_current_user_id() : $uid;
	$meta_key = 'lps_user_wishlist';

	$wishlist = wps_get_user_meta($user_id, $meta_key);
	
	if (empty($wishlist)) {		// lps_user_wishlist 값이 없을 때
		$unserial = unserialize($wishlist);
		if (empty($unserial)) {
			$new_book = serialize(array($book_id));
			$result = wps_update_user_meta($user_id, $meta_key, $new_book);
		} else {	// a:0{}	
			array_push($unserial, $book_id);
			$serialized = serialize($unserial);
			$result = wps_update_user_meta($user_id, $meta_key, $serialized);
		}
	} else {
		$unserial = unserialize($wishlist);
		if ( !in_array($book_id, $unserial) ) {
			array_push($unserial, $book_id);
			$serialized = serialize($unserial);
			$result = wps_update_user_meta($user_id, $meta_key, $serialized);
		} else {
			$result = 'exists';
		}
	}
	return $result;
}

/*
 * Desc : 장바구니에 추가
 * 		$book_id는 array
 */
function lps_add_book_cart( $book_id, $uid = NULL ) {
	$user_id = empty($uid) ? wps_get_current_user_id() : $uid;
	$meta_key = 'lps_user_cart';

	$cartlist = wps_get_user_meta($user_id, $meta_key);
	
	if (empty($cartlist)) {		// lps_user_cartlist 값이 없을 때
		$unserial = unserialize($cartlist);
		if (empty($unserial)) {
			$new_book = serialize($book_id);
			$result = wps_update_user_meta($user_id, $meta_key, $new_book);
		} else {	// a:0{}	
			array_push($unserial, $book_id);
			$serialized = serialize($unserial);
			$result = wps_update_user_meta($user_id, $meta_key, $serialized);
		}
	} else {
		$unserial = unserialize($cartlist);
		$result = 1;
		
		foreach ($book_id as $key => $val) {
			if ( !in_array($val, $unserial) ) {
				array_push($unserial, $val);
// 				$serialized = serialize($unserial);
// 				wps_update_user_meta($user_id, $meta_key, $serialized);
			}
		}
		$serialized = serialize($unserial);
		wps_update_user_meta($user_id, $meta_key, $serialized);
	}
	return $result;
}

/*
 * Desc : 구매 대기 리스트
 * 		$book_id는 array
 */
function lps_add_book_pay( $book_id, $uid = NULL ) {
	$user_id = empty($uid) ? wps_get_current_user_id() : $uid;
	$meta_key = 'lps_user_book_pay';

	$new_book = serialize($book_id);
	$result = wps_update_user_meta($user_id, $meta_key, $new_book);
	
	return $result;
}

/*
 * Desc : 로그인 날짜 업데이트
 */
function lps_update_user_login_dt( $user_id ) {
	global $wdb;
	
	$query = "
			UPDATE
				bt_users
			SET
				last_login_dt = NOW()
			WHERE
				ID = ?
	";
	$stmt = $wdb->prepare($query);
	$stmt->bind_param( 'i', $user_id );
	$result = $stmt->execute();
}

/*
 * Desc : SMS 인증 번호 등록
 */
function lps_add_mobile_auth( $mobile, $code ) {
	global $wdb;
	
	$query = "
			INSERT INTO
				bt_app_mobile_auth
				(
					ID,
					mobile,
					authcode,
					created_dt
				)
			VALUES
				(
					NULL, ?, ?, NOW()
				)
	";
	$stmt = $wdb->prepare($query);
	$stmt->bind_param( 'ss', $mobile, $code );
	$stmt->execute();
	return $wdb->insert_id;
}

/*
 * Desc : SMS 인증 번호 비교
 */
function lps_check_mobile_auth( $mobile, $code ) {
	global $wdb;
	
	$query = "
			SELECT
				ID
			FROM
				bt_app_mobile_auth
			WHERE
				mobile = ? AND
				authcode = ?
			ORDER BY
				ID DESC
			LIMIT
				0, 1
	";
	$stmt = $wdb->prepare($query);
	$stmt->bind_param( 'ss', $mobile, $code );
	$stmt->execute();
	return $wdb->get_var($stmt);
}

/*
 * Desc : 내가 차단한 회원 리스트
 */
function lps_get_banned_users( $user_id ) {
	global $wdb;
	
// 	$user_id = wps_get_current_user_id();
	
	$query = "
			SELECT
				banned_user_id
			FROM
				bt_banned_users
			WHERE
				user_id = ?
	";
	$stmt = $wdb->prepare($query);
	$stmt->bind_param( 'i', $user_id );
	$stmt->execute();
	return $wdb->get_results($stmt);
}

/*
 * Desc : 차단한 회원 아이디 : Array
 */
function lps_get_banned_user_ids( $user_id ) {
	$banned_users = lps_get_banned_users( $user_id );
	$banned_user_id = [];
	if (!empty($banned_users)) {
		foreach ($banned_users as $key => $val) {
			array_push($banned_user_id, $val['banned_user_id']);
		}
	}
	return $banned_user_id;
}

/*
 * Desc : SNS 리스트에 추가
 * 		$book_id : int
 */
function lps_share_book_sns( $user_id, $book_id ) {
	$meta_key = 'lps_sns_shared_book';

	$shared_book = wps_get_user_meta($user_id, $meta_key);

	if (empty($shared_book)) {		// lps_sns_shared_book 값이 없을 때
		$unserial = unserialize($shared_book);
		if (empty($unserial)) {
			$new_book = serialize(array($book_id));
			$result = wps_update_user_meta($user_id, $meta_key, $new_book);
		} else {	// a:0{}
			array_push($unserial, $book_id);
			$serialized = serialize($unserial);
			$result = wps_update_user_meta($user_id, $meta_key, $serialized);
		}
	} else {
		$unserial = unserialize($shared_book);
		if ( !in_array($book_id, $unserial) ) {
			array_push($unserial, $book_id);
			$serialized = serialize($unserial);
			$result = wps_update_user_meta($user_id, $meta_key, $serialized);
		} else {
			$result = 'exists';
		}
	}
	return $result;
}

/*
 * Desc : 최근에 읽은 책 3권
 */
function lps_get_last_read_3books( $user_id ) {
	global $wdb;

	$query = "
			SELECT
				b.ID,
				b.book_title,
				b.cover_img
			FROM
				bt_books AS b
			INNER JOIN
				bt_user_book_read AS br
			WHERE
				br.user_id = ? AND
				b.ID = br.book_id
			ORDER BY
				br.ID DESC
			LIMIT
				0, 3
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param('i', $user_id);
	$stmt->execute();
	return $wdb->get_results($stmt);
}

/*
 * Desc : 내 친구 리스트
 */
function lps_get_my_friends( $user_id ) {
	global $wdb;
	
	$user_ids = [];
	
	$query = "
			SELECT
				u.ID
			FROM
				bt_friends AS f
			INNER JOIN
				bt_users AS u
			WHERE
				f.initiator_user_id = ? AND
				f.is_confirmed = '1' AND
				u.ID = f.friend_user_id
			UNION
			SELECT
				u.ID
			FROM
				bt_friends AS f
			INNER JOIN
				bt_users AS u
			WHERE
				f.friend_user_id = ? AND
				f.is_confirmed = '1' AND
				u.ID = f.initiator_user_id 
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'ii', $user_id, $user_id );
	$stmt->execute();
	$results = $wdb->get_results($stmt);
	
	if ( !empty($results) ) {
		foreach ($results as $key => $val) {
			array_push($user_ids, $val['ID']);
		}
	}
	
	return $user_ids;
}

/*
 * Desc : 세션 아이디 리턴
 */
function lps_get_user_login_token( $user_id ) {
	if (!empty($user_id)) {
		$token = wps_get_user_meta( $user_id, 'wps_session_id' );
	} else {
		$token = '';
	}
	return $token;
}

/*
 * Desc : 세션 유효기간이 남아 있는 지 확인
 */
function lps_has_user_valid_token( $token ) {
	global $wdb;
	
	$query = "
			SELECT
				sess_expiry,
				sess_data
			FROM
				bt_session
			WHERE
				sess_id = ?
	";
	$stmt = $wdb->prepare($query);
	$stmt->bind_param('s', $token);
	$stmt->execute();
	$row = $wdb->get_row($stmt);
	
	if (!empty($row)) {
		$expiry = $row['sess_expiry'];
		$sdata = $row['sess_data'];
		if ($expiry >= time() && !empty($sdata)) {
			return true;
		}
	}
	return false;	
}

function wps_get_user_by_level( $ulevel ) {
	global $wdb;
	
	$query = "
			SELECT
				*
			FROM
				bt_users
			WHERE
				user_level = ?
	";
	
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'i', $ulevel );
	$stmt->execute();
	return $wdb->get_results($stmt);
}

// Desc : Dashboard, SNS 공유 수, Master만
function lps_get_share_count() {
	global $wdb;
	
	$query = "
			SELECT
				meta_value
			FROM
				bt_users_meta
			WHERE
				meta_key = 'lps_sns_shared_book'
	";
	$stmt = $wdb->prepare( $query );
	$stmt->execute();
	$results = $wdb->get_results($stmt);
	
	$share_count = 0;
	if (!empty($results)) {
		foreach ($results as $key => $val) {
			$share_count += count(unserialize($val['meta_value']));
		}
	}
	return $share_count;
}

/*
 * Desc : 오늘 가입한 회원 수
 */
function lps_get_today_join_user() {
	global $wdb;
	
	$query = "
			SELECT
				COUNT(*)
			FROM
				bt_users
			WHERE
				user_registered BETWEEN CURDATE() AND CURDATE() + 1
	";
	$stmt = $wdb->prepare( $query );
	$stmt->execute();
	return $wdb->get_var($stmt);
}

/*
 * Desc : 수신거부
 * 
 */
function lps_unsubscribe_email( $email ) {
	global $wdb;
	
	$query = "
			UPDATE
				bt_users
			SET
				get_email = 'N'
			WHERE
				user_email = ?
	";
	$stmt = $wdb->prepare($query);
	$stmt->bind_param( 's', $email );
	return $stmt->execute();
}

?>
