<?php
/*
 * 2016.08.22		softsyw
 * Desc : epub file 다운로드
 */
require_once '../../wps-config.php';
require_once FUNC_PATH . '/functions-book.php';

if ( empty($_GET['id']) ) {
	lps_alert_back('도서 아이디 대한 정보가 필요합니다.');
}

$book_id = $_GET['id'];

$attachment = unserialize(wps_get_book_meta( $book_id, 'lps_book_epub_file' ));

$file_path = $attachment['file_path'];
$file_name = $attachment['file_name'];

if ( !is_file($file_path) ) {
	lps_js_back('파일이 존재하지 않습니다.');
}

$length = filesize($file_path);
$file_name = rawurlencode($file_name);		// firefox에서는 한글 인코딩됨.

// Start Download
header('Content-Description: File Transfer');
header('Content-Disposition: attachment; filename="'. $file_name . '"');
header('Content-Type: application/zip');
header('Content-Transfer-Encoding: binary');
if ( $length > 0 ) {
	header('Content-Length: ' . $length);
}

set_time_limit(0);

$file = fopen($file_path, "rb");

if ( $file ) {
	while( !feof($file) ) {
		print( fread($file, 1024 * 8) );
		flush();
		usleep(20000);		// *** 남은 다운로드 시간을 보여준다.
	}
	@fclose( $file );
}

?>