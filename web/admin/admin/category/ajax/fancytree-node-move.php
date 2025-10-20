<?php
/*
 * 2015.06.16		softsyw
 * Desc : 카테고리 이동 Process (Drag & Drop)
 */
require_once '../../../wps-config.php';
require_once FUNC_PATH . '/functions-term.php';

$code = 0;
$msg = '';

if ( empty($_POST['termID']) ) {
	$code = 400;
	$msg = '카테고리 정보가 존재하지 않습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( empty($_POST['parentID']) ) {
	$code = 400;
	$msg = '이동할 카테고리 정보가 존재하지 않습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$term_id = $_POST['termID'];
$parent_id = $_POST['parentID'];
$siblings = $_POST['nodeKeys'];
$taxonomy = $_POST['tkey'];

if ( in_array( $term_id, $siblings ) ) {		// siblings
	$result = wps_update_order_node( $_POST['nodeKeys'], $term_id, $parent_id, $taxonomy );
} else {	// child
	$result = wps_move_node( $term_id, $parent_id );
}

$json = compact('code', 'msg', 'result');
echo json_encode( $json );

?>