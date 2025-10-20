<?php
/*
 * 2016.10.26		softsyw
 * Desc : 담벼락 공지사항 등록
 * 		Master, 춮판사 이용 가능
 * 		post_type : notice_new
 * 		post_secondary : community
 * 		post_status : all -> 모든 책에 등록
 * 		post_status : open -> 선택 책에 등록 : bt_posts_meta -> wps_notice_books
 */
require_once '../../../wps-config.php';

$code = 0;
$msg = '';

if ( !wps_is_agent() ) {
	$code = 510;
	$msg = '관리자만 사용할 수 있습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( empty($_POST['post_type']) ) {
	$code = 410;
	$msg = '글 종류를 선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( empty($_POST['post_title']) ) {
	$code = 411;
	$msg = '제목을 입력해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if (empty($_POST['post_status'])) {
	if ( empty($_POST['notice_books']) ) {
		$code = 412;
		$msg = '공지사항을 등록할 책을  선택해 주십시오.';
		$json = compact('code', 'msg');
		exit( json_encode($json) );
	}
}

if ( empty($_POST['post_content']) ) {
	$code = 413;
	$msg = '내용을 입력해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$post_id = wps_add_post();

if ( empty($post_id) ) {
	$code = 501;
	$msg = '등록하지 못했습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if (!empty($_POST['notice_books'])) {
	/*
	 * Desc : serializeObject() bug ?
	 * 	배열 변수는 두번을 계산한다.
	 */
	$_POST['notice_books'] = array_unique($_POST['notice_books']);
	
	$meta_value = serialize($_POST['notice_books']);
	wps_update_post_meta($post_id, 'wps_notice_books', $meta_value);
	
// 	lps_update_book_meta();
}

$json = compact('code', 'msg');
echo json_encode( $json );

?>