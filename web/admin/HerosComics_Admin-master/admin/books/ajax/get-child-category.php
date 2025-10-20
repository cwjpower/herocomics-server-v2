<?php
/*
 * 2016.08.15		softsyw
 * Desc : 하위 디렉토리 조회
 */
require_once '../../../wps-config.php';
require_once FUNC_PATH . '/functions-term.php';

$code = 0;
$msg = '';

if ( empty($_POST['id']) ) {
	$code = 410;
	$msg = '카테고리를 선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$id = $_POST['id'];
$level = $_POST['level'];
$lists = '';
if (!strcmp($level, 'category_first')) {
	$target = 'category_second';
} else if (!strcmp($level, 'category_second')) {
	$target = 'category_third';
} else {
	$target = ''; 
}

$category = lps_get_term_by_id($id);

if (!empty($category)) {
	foreach ($category as $key => $val) {
		$tid = $val['term_id'];
		$tname = $val['name'];
		$lists .= '<li class="list-group-item" id="tid-' . $tid . '">' . $tname . '</li>';
	}
}

$json = compact('code', 'msg', 'lists', 'target');
echo json_encode( $json );

?>