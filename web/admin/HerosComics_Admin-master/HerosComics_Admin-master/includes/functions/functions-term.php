<?php
/**
 * Desc : Terms 관련
 */
function wps_add_term_category( $name, $taxonomy, $parent = 0, $url = NULL ) {
	global $wdb;

	$slug = wps_sanitize_text( $name );

	$query = "
			INSERT INTO
				bt_terms
				(
					term_id,
					name,
					slug,
					term_group
				)
			VALUES
				(
					NULL, ?, ?, 0
				)
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'ss', $name, $slug );
	$stmt->execute();
	$obj_id = $wdb->insert_id;

	if ( !empty($obj_id) ) {
		$query = "
				INSERT INTO
					bt_term_taxonomy
					(
						term_taxonomy_id,
						term_id,
						taxonomy,
						description,
						parent,
						count
					)
				VALUES
					(
						NULL, ?, ?, ?, ?,
						0
					)
		";
		$stmt = $wdb->prepare( $query );
		$stmt->bind_param( 'issi', $obj_id, $taxonomy, $description, $parent );

		$meta_val = array( 'url' => $url );
		$description = serialize($meta_val);

		if ( $stmt->execute() ) {
			return $obj_id;
		}
	}
	return false;
}

function wps_edit_term_category( $term_id, $taxonomy, $name, $url = NULL ) {
	global $wdb;

	$slug = wps_sanitize_text( $name );

	$query = "
			UPDATE
				bt_terms
			SET
				name = ?,
				slug = ?
			WHERE
				term_id = ?
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'ssi', $name, $slug, $term_id );
	$result = $stmt->execute();

	if ( !empty($url) ) {
		$query = "
				UPDATE
					bt_term_taxonomy
				SET
					description = ?
				WHERE
					term_id	= ? AND
					taxonomy = ?
		";
		$stmt = $wdb->prepare( $query );
		$stmt->bind_param( 'sis', $description, $term_id, $taxonomy );

		$meta_val = array( 'url' => $url );
		$description = serialize($meta_val);

		$result = $stmt->execute();
	}

	return $result;
}

function wps_edit_term_explanation( $term_id, $exp ) {
	global $wdb;
	
	$term_taxonomy_rows = wps_get_term_taxonomy( $term_id, 'wps_item_category' );
	$description = unserialize($term_taxonomy_rows['description']);
	
	foreach ($description as $key => $val ) {
		$array[$key] = stripcslashes($val);
	}
	$array['explanation'] = stripslashes($exp);
	$serial = serialize($array);
	
	$query = "
				UPDATE
					bt_term_taxonomy
				SET
					description = ?
				WHERE
					term_id	= ? AND
					taxonomy = 'wps_item_category'
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'si', $serial, $term_id );
	return $stmt->execute();
}

function wps_delete_term_category( $term_id ) {
	global $wdb;

	$query = "
			DELETE FROM
				bt_term_taxonomy
			WHERE
				term_id = ?
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'i', $term_id );
	if ( $stmt->execute() ) {
		$query = "
				DELETE FROM
					bt_terms
				WHERE
					term_id = ?
		";
		$stmt = $wdb->prepare( $query );
		$stmt->bind_param( 'i', $term_id );
		return $stmt->execute();
	}
	return false;
}

function wps_delete_children_node( $parent_id ) {
	global $wdb;

	$query = "
			SELECT
				term_id,
				parent
			FROM
				bt_term_taxonomy
			WHERE
				parent = ?
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'i', $parent_id );
	$stmt->execute();
	$result = $wdb->get_results($stmt);

	if ( !empty($result) ) {
		foreach ( $result as $key => $val ) {
			if ( !empty($val['parent']) ) {
				$term_id = $val['term_id'];
				wps_delete_term_category( $term_id );
				wps_delete_children_node( $term_id );
			}
		}
	}
}

function wps_move_node( $term_id, $parent_id ) {
	global $wdb;

	$query = "
			UPDATE
				bt_term_taxonomy
			SET
				parent = ?
			WHERE
				term_id = ?
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'ii', $parent_id, $term_id );
	return $stmt->execute();
}

/*
 * 순서 업데이트, term_group을 order key로 사용함.
 */
function wps_update_order_node( $nodes, $term_id, $parent_id, $taxonomy ) {
	global $wdb;

	// term_id 의 parent 값을 parent_id의 parent 값으로 업데이트한다.
	$sibling = wps_get_term_taxonomy( $parent_id, $taxonomy );

	$query = "
			UPDATE
				bt_term_taxonomy
			SET
				parent = ?
			WHERE
				term_id =? AND
				taxonomy = ?
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'iis', $parent, $term_id, $taxonomy );
	$parent = $sibling['parent'];
	$stmt->execute();

	$query = "
			UPDATE
				bt_terms
			SET
				term_group = ?
			WHERE
				term_id = ?
	";
	$stmt = $wdb->prepare( $query );

	foreach ( $nodes as $key => $val ) {
		$stmt->bind_param( 'ii', $key, $val );
		$stmt->execute();
	}
	return true;
}

function wps_get_term( $term_id ) {
	global $wdb;

	if ( empty($term_id) ) {
		return '';
	}

	$query = "
			SELECT
				*
			FROM
				bt_terms
			WHERE
				term_id = ?
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'i', $term_id );
	$stmt->execute();
	return $wdb->get_row($stmt);
}

function wps_get_term_name( $term_id ) {
	global $wdb;

	$query = "
			SELECT
				name
			FROM
				bt_terms
			WHERE
				term_id = ?
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'i', $term_id );
	$stmt->execute();
	return $wdb->get_var($stmt);
}

function wps_get_term_taxonomy( $term_id, $taxonomy ) {
	global $wdb;

	$query = "
			SELECT
				*
			FROM
				bt_term_taxonomy
			WHERE
				term_id = ? AND
				taxonomy = ?
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'is', $term_id, $taxonomy );
	$stmt->execute();
	return $wdb->get_row($stmt);
}

function wps_get_term_by_taxonomy( $taxonomy ) {
	global $wdb;
	
	$query = "
			SELECT
				t.term_id,
				t.name,
				tt.parent,
				tt.description
			FROM
				bt_terms AS t
			INNER JOIN
				bt_term_taxonomy AS tt
			WHERE
				tt.taxonomy = ? AND
				t.term_id = tt.term_id
			ORDER BY
				t.term_group ASC
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 's', $taxonomy );
	$stmt->execute();
	return $wdb->get_results($stmt);
}

/*
 * Desc : child node
 */
function lps_get_term_by_id( $term_id ) {
	global $wdb;

	$query = "
			SELECT
				t.term_id,
				t.name,
				tt.parent,
				tt.description
			FROM
				bt_terms AS t
			INNER JOIN
				bt_term_taxonomy AS tt
			WHERE
				tt.parent = ? AND
				t.term_id = tt.term_id
			ORDER BY
				t.term_group ASC
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'i', $term_id );
	$stmt->execute();
	return $wdb->get_results($stmt);
}

function lps_get_grand_parent_id( $term_id ) {
	global $wdb;
	
	$query = "
			SELECT
				parent
			FROM
				bt_term_taxonomy
			WHERE
				term_id = (SELECT parent FROM bt_term_taxonomy WHERE term_id = ?)
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'i', $term_id );
	$stmt->execute();
	return $wdb->get_var($stmt);
}

/*
 * Desc : 각 회원등급별로 관리자 메뉴의 접근 권한 설정
 */
function lps_edit_user_level_terms() {
	global $wdb;
	
	$user_level = $_POST['level'];
	$menu_items = empty($_POST['items']) ? '' : serialize($_POST['items']);
	
	$created = date('Y-m-d H:i:s');
	$user_by = wps_get_current_user_id();
	$serialized = serialize(compact('created', 'user_by'));
	
	$query = "
			INSERT INTO
				bt_user_level_terms
				(
					ID,
					user_level,
					term_items,
					meta_value
				)
			VALUES
				(
					NULL, ?, ?, ?
				)
	";
	$stmt = $wdb->prepare($query);
	$stmt->bind_param('iss', $user_level, $menu_items, $serialized);
	$stmt->execute();
	return $wdb->insert_id;
}

?>