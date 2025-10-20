<?php
/**
 * Desc : Fancytree 관련
 */
function wps_fancytree_init( $taxonomy ) {
	global $wdb;

	$json = array();

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
	$categories = $wdb->get_results($stmt);

	if ( !empty($categories) ) {
		foreach ( $categories as $key => $val ) {
			if ( $val['parent'] == '0' ) {
				$term_id = $val['term_id'];
				$title = $val['name'];
				$children = wps_fancytree_children($term_id);

				$unserialized = unserialize($val['description']);
				$url = empty( $unserialized ) ? '' : $unserialized['url'];
				
				$title_desc = empty($url) ? $title : $title . ' <span style="color: #999;">(' . $url . ')</span>';
				
				$json[] = array(
						'title' => $title_desc,
						'key' => $term_id,
						'expanded' => true,
						'folder' => true,
						'children' => $children,
						'url' => $url,
						'titleOnly'	=> $title,
						'tooltip' => 'root'
				);
			}
		}
	}
	return json_encode($json);
}

function wps_fancytree_children( $term_id ) {
	global $wdb;

	$json = array();

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
	$categories = $wdb->get_results($stmt);

	if ( !empty($categories) ) {
		foreach ( $categories as $key => $val ) {
			$term_id = $val['term_id'];
			$title = $val['name'];
			$children = wps_fancytree_children($term_id);
			$folder = true;

			$unserialized = unserialize($val['description']);
			$url = empty( $unserialized ) ? '' : $unserialized['url'];
			
			$title_desc = empty($url) ? $title : $title . ' <span style="color: #999;">(' . $url . ')</span>';

			$json[] = array(
					'title' => $title_desc,
					'key' => $val['term_id'],
					'expanded' => true,
					'folder' => $folder,
					'children' => $children,
					'url' => $url,
					'titleOnly'	=> $title
			);
		}
	}
	return $json;
}

/*
 * Desc : children node를 가져온다.
 */
function wps_fancytree_node_by_id( $parent ) {
	global $wdb;

	$json = array();

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
	$stmt->bind_param( 'i', $parent );
	$stmt->execute();
	return $wdb->get_results($stmt);
}

/*
 * Desc : taxonomy의 root node를 가져온다.
 */
function wps_fancytree_root_node_by_name( $taxonomy ) {
	global $wdb;

	$json = array();

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
				tt.parent = 0 AND
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
 * Desc : 선택된 상태 root
 */
function wps_fancytree_init_selection( $taxonomy, $level ) {
	global $wdb;

	$json = array();

	// 등급별 허용 메뉴
	$query = "
			SELECT
				term_items
			FROM
				bt_user_level_terms
			WHERE
				user_level = ?
			ORDER BY
				ID DESC
			LIMIT
				0, 1
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'i', $level );
	$stmt->execute();
	$srow = $wdb->get_var($stmt);
	$term_items = unserialize($srow);
	
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
	$categories = $wdb->get_results($stmt);

	if ( !empty($categories) ) {
		foreach ( $categories as $key => $val ) {
			if ( $val['parent'] == '0' ) {
				$term_id = $val['term_id'];
				$selected = empty($term_items) ? false : in_array($term_id, $term_items);
				$children = wps_fancytree_children_selection($term_id, $term_items);

				$unserialized = unserialize($val['description']);
				$url = empty( $unserialized ) ? '' : $unserialized['url'];
				
				$json[] = array(
						'title' => $val['name'],
						'key' => $term_id,
						'selected'	=> $selected,	// selected
						'expanded' => true,
						'folder' => true,
						'children' => $children,
						'url' => $url,
						'tooltip' => 'root'
				);
			}
		}
	}
	return json_encode($json);
}

/*
 * Desc: 선택된 상태 children
 */
function wps_fancytree_children_selection( $term_id, $term_items ) {
	global $wdb;

	$json = array();

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
	$categories = $wdb->get_results($stmt);

	if ( !empty($categories) ) {
		foreach ( $categories as $key => $val ) {
			$term_id = $val['term_id'];
			$selected = empty($term_items) ? false : in_array($term_id, $term_items);
			
			$children = wps_fancytree_children_selection($term_id, $term_items);
			$folder = true;

			$unserialized = unserialize($val['description']);
			$url = empty( $unserialized ) ? '' : $unserialized['url'];

			$json[] = array(
					'title' => $val['name'],
					'key' => $val['term_id'],
					'selected'	=> $selected,	// selected
					'expanded' => true,
					'folder' => $folder,
					'children' => $children,
					'url' => $url
			);
		}
	}
	return $json;
}
?>