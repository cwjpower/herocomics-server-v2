<?php
/*
 * Desc : 친구 요청
 */
function lps_request_friend( $user_id, $friend_uid ) {
	global $wdb;
	
	// 친구 요청 등록
	$query = "
		INSERT INTO
			bt_friends
			(
				ID,
				initiator_user_id,
				friend_user_id,
				is_confirmed,
				is_limited,
				is_favorite,
				date_created
			)
		VALUES
			(
				NULL, ?, ?, 0, 0, 0, NOW()
			)
";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'ii', $user_id, $friend_uid );
	$stmt->execute();
	return $wdb->affected_rows;
}


/*
 * Desc : 신청내역 있는 지 확인한다.
 */
function lps_is_friend_status( $user_id, $friend_uid ) {
	global $wdb;

	// 신청내역 있는 지 확인한다.
	$query = "
		SELECT
			ID
		FROM
			bt_friends
		WHERE
			initiator_user_id = ? AND
			friend_user_id = ? OR
			initiator_user_id = ? AND
			friend_user_id = ?
";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'iiii', $user_id, $friend_uid, $friend_uid, $user_id );
	$stmt->execute();
	return $wdb->get_var($stmt);
}

/*
 * Desc : 친구삭제
 * 		friend_uid : 23,45,67 (string)
 */
function lps_delete_friends( $user_id, $friend_uid) {
	global $wdb;

	$query = "
			DELETE FROM
				bt_friends
			WHERE
				initiator_user_id = ? AND
				friend_user_id IN ($friend_uid)
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'i', $user_id );
	$stmt->execute();
	
	// 반대의 경우 삭제
	$fr_arr = explode(',', $friend_uid);
	foreach ( $fr_arr as $key => $val) {
		$query = "
				DELETE FROM
					bt_friends
				WHERE
					initiator_user_id = ? AND
					friend_user_id = ?
		";
		$stmt = $wdb->prepare( $query );
		$stmt->bind_param( 'ii', $val, $user_id );
		$stmt->execute();
	}
	return true;
}

/*
 * Desc : 친구 요청 수락
 */
function lps_accept_friends( $user_id, $friend_uid) {
	global $wdb;

	$query = "
			UPDATE
				bt_friends
			SET
				is_confirmed = 1
			WHERE
				initiator_user_id = ? AND
				friend_user_id = ?
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'ii', $user_id, $friend_uid );
	$stmt->execute();
	return $wdb->affected_rows;
}

/*
 * Desc : 친구 즐겨찾기 추가
 */
function lps_add_favorite_friend( $user_id, $friend_uid) {
	global $wdb;

	$query = "
			UPDATE
				bt_friends
			SET
				is_favorite = 1
			WHERE
				initiator_user_id = ? AND
				friend_user_id = ? OR
				initiator_user_id = ? AND
				friend_user_id = ? AND
				is_confirmed = '1'
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'iiii', $user_id, $friend_uid, $friend_uid, $user_id );
	$stmt->execute();
	return $wdb->affected_rows;
}

/*
 * Desc : 친구 즐겨찾기 삭제
 */
function lps_delete_favorite_friend( $user_id, $friend_uid) {
	global $wdb;

	$query = "
			UPDATE
				bt_friends
			SET
				is_favorite = 0
			WHERE
				initiator_user_id = ? AND
				friend_user_id = ? OR
				initiator_user_id = ? AND
				friend_user_id = ?
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'iiii', $user_id, $friend_uid, $friend_uid, $user_id );
	$stmt->execute();
	return $wdb->affected_rows;
}

/*
 * Desc : 서로 친구인지 확인
 */
function lps_is_accepted_friend( $user_id, $friend_uid ) {
	global $wdb;

	$query = "
		SELECT
			ID
		FROM
			bt_friends
		WHERE
			is_confirmed = '1' AND
			initiator_user_id = ? AND
			friend_user_id = ? OR
			initiator_user_id = ? AND
			friend_user_id = ?
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'iiii', $user_id, $friend_uid, $friend_uid, $user_id );
	$stmt->execute();
	return $wdb->get_var($stmt);
}

/*
 * Desc : 차단한 친구인지 확인
 */
function lps_is_blocked_friend( $user_id, $friend_uid ) {
	global $wdb;

	$query = "
		SELECT
			ID
		FROM
			bt_banned_users
		WHERE
			user_id = ? AND
			banned_user_id = ?
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'ii', $user_id, $friend_uid );
	$stmt->execute();
	return $wdb->get_var($stmt);
}

/*
 * Desc : 친구 요청 or 수락 상태 확인
 */
function lps_get_friend_status( $user_id, $friend_uid ) {
	global $wdb;

	$query = "
		SELECT
			is_confirmed
		FROM
			bt_friends
		WHERE
			initiator_user_id = ? AND
			friend_user_id = ? OR
			initiator_user_id = ? AND
			friend_user_id = ?
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'iiii', $user_id, $friend_uid, $friend_uid, $user_id );
	$stmt->execute();
	return $wdb->get_var($stmt);
}

?>