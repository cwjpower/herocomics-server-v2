<?php
/*
 * Desc : 행사 알림 등록
 */
function lps_add_promotion() {
	global $wdb;

	$user_id = wps_get_current_user_id();
	$prom_title = empty($_POST['prom_title']) ? '' : $_POST['prom_title'];
	$prom_content = empty($_POST['prom_content']) ? '' : $_POST['prom_content'];
	$prom_type = empty($_POST['prom_type']) ? '' : $_POST['prom_type'];
	$user_count = $_POST['user_count'];
	
	$query = "
			INSERT INTO
				bt_promotion
				(
					ID,
					prom_title,
					prom_content,
					prom_type,
					user_count,
					created_dt,
					user_id
				)
			VALUES
				(
					NULL, ?, ?, ?, ?,
					NOW(), ?
				)
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'ssssi', 
					$prom_title, $prom_content, $prom_type, $user_count, $user_id
			);
	$stmt->execute();
	return $wdb->affected_rows;
}

function lps_get_promotion_by_id( $id ) {
	global $wdb;
	
	$query = "
			SELECT
				*
			FROM
				bt_promotion
			WHERE
				ID = ?
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'i', $id );
	$stmt->execute();
	return $wdb->get_row( $stmt );
}

?>