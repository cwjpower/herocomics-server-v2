<?php
/*
 * 2016.08.16		softsyw
 * Desc : tmp 디렉토리의 파일 삭제, file path
 * 
 */
require_once '../../wps-config.php';

$code = 0;
$msg = '';

if ( empty($_POST['filePath']) ) {
	$code = 401;
	$msg = '삭제할 파일 정보가 존재하지 않습니다.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$file_path = $_POST['filePath'];

if ( !is_file($file_path) ) {
	$code = 404;
	$msg = '파일이 삭제되었거나 존재하지 않습니다.';
	$json = compact('code', 'msg', 'file_to_del');
	exit( json_encode($json) );
}

if ( !@unlink($file_path) ) {
	$code = 501;
	$msg = '파일을 삭제할 수 없습니다. 관리자에게 문의해 주십시오.';
}

// Thumbnail
$thumb_suffix = '-thumb';
$thumb_file = wps_get_thumb_file_name($file_path, $thumb_suffix);
@unlink( $thumb_file );

$json = compact('code', 'msg', 'file_path');
echo json_encode( $json );

?>