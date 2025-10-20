<?php
/*
 * 2015.06.15		softsyw
 * Desc : 카테고리 저장 Process
 */
require_once '../../../wps-config.php';
require_once FUNC_PATH . '/functions-term.php';

$code = 0;
$msg = '';

parse_str( $_POST['param'], $out );

if ( empty($out['tkey']) ) {
	$code = 400;
	$msg = '구분이 필요합니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}
if ( empty($out['tag_name']) ) {
	$code = 401;
	$msg = '이름을 입력해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( !empty($out['tag_url']) ) {
// 	if ( filter_var($out['tag_url'], FILTER_VALIDATE_URL) === false ) {
// 		$code = 412;
// 		$msg = "URL 형식을 확인해 주십시오.\n(예: http://www.yourdomain.com/go.html?id=3)";
// 		$json = compact('code', 'msg');
// 		exit( json_encode($json) );
// 	}
}

$tag_name = $out['tag_name'];
$tag_url = empty($out['tag_url']) ? '' : $out['tag_url'];
$taxonomy = empty($out['tkey']) ? 'wps_category' : $out['tkey'];
$mode = $out['mode'];
$tid = $out['tid'];
$parent = empty($out['parent']) ? 0 : $out['parent'];

if ( !strcmp($mode, 'NEW') ) {
	
	/*
	 * Desc : 3단계 초과 카테고리 생성 금지
	 * 	선택한 카테고리(parent)의 2단계 위의 parent를 찾을 때
	 */ 
	$grand_parent = lps_get_grand_parent_id($parent);
	if ( !empty($grand_parent) ) {
		$code = 501;
		$msg = '3단계를 초과하는 카테고리는 생성할 수 없습니다';
		$json = compact('code', 'msg');
		exit( json_encode($json) );
	}
	
	if ( !wps_add_term_category( $tag_name, $taxonomy, $parent, $tag_url ) ) {
		$code = 414;
		$msg = '카테고리를 등록하지 못했습니다';
		$json = compact('code', 'msg');
		exit( json_encode($json) );
	}
} else if ( !strcmp($mode, 'EDIT') ) {
	if ( !wps_edit_term_category( $tid, $taxonomy, $tag_name, $tag_url ) ) {
		$code = 414;
		$msg = '저장하지 못했습니다';
		$json = compact('code', 'msg');
		exit( json_encode($json) );
	}
}

$json = compact('code', 'msg');
echo json_encode( $json );

?>