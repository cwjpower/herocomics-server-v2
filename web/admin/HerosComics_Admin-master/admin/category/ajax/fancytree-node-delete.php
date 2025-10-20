<?php
/*
 * 2015.06.15		softsyw
 * Desc : 카테고리 삭제 Process
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

$term_id = $_POST['termID'];

wps_delete_children_node( $term_id );
wps_delete_term_category( $term_id );

$json = compact('code', 'msg');
echo json_encode( $json );

?>