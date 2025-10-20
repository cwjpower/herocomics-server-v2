<?php
/*
 * 2016.02.16		softsyw
 * Desc : wps_postmeta 파일 다운로드
 */
require_once '../../wps-config.php';

if ( empty($_GET['pid']) ) {
	lps_alert_back('다운로드 파일에 대한 정보가 필요합니다.');
}
if ( !isset($_GET['key']) ) {
	lps_alert_back('다운로드 파일에 대한 정보가 필요합니다.');
}

$post_id = $_GET['pid'];
$key = $_GET['key'];

$attachment = unserialize(wps_get_post_meta( $post_id, 'wps-post-attachment' ));

$file_path = $attachment[$key]['file_path'];
$file_name = $attachment[$key]['file_name'];
// $file_size = $attachment[$key]['file_size'];

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