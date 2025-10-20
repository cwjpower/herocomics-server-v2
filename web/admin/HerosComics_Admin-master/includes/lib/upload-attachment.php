<?php
/*
 * 2016.08.16	softsyw
 * Desc : 이미지 or 일반 파일 업로드, 이미지 파일일 경우만 썸네일 기능(optional) 작동.
 *
 * 		doThumb 값이 존재하면 썸네일을 생성한다.
 *
 */
require_once '../../wps-config.php';
require_once INC_PATH . '/classes/WpsThumbnail.php';

$code = 0;
$msg = '';

if (empty($_POST['eleName'])) {
	$file_element = 'attachment';
} else {
	$file_element = str_replace(array('[', ']'), '', $_POST['eleName']);		// attachment[] -> attachment
}

//error_log($file_element);
$upload_dir = UPLOAD_PATH . '/tmp';
if ( !is_dir($upload_dir) ) {
	mkdir($upload_dir, 0777, true);
}

$img_files = array( 'jpg', 'jpeg', 'gif', 'png', 'epub' ); // epub 추가
$able_thumb = 1;

$wps_thumbnail = new WpsThumbnail();

error_log(print_r($_FILES[$file_element], true));

//error_log("upload path ===>".$upload_dir );
foreach ( $_FILES[$file_element]['name'] as $key => $val ) {
	$file_ext = strtolower(pathinfo( $_FILES[$file_element]['name'][$key], PATHINFO_EXTENSION ));

	if ( in_array($file_ext, $img_files) ) {
		$new_file_name = wps_make_rand() . '.' . $file_ext;
		$able_thumb = 1;
	} else {
		$new_file_name = wps_make_rand();
	}

	$upload_path = $upload_dir . '/' . $new_file_name;

    $result = move_uploaded_file( $_FILES[$file_element]['tmp_name'][$key], $upload_path );

    if (!$result ){
        error_log("Not uploaded because of error =======================> #".$_FILES[$file_element]['error']);
    }



	if ( $result ) {
		$file_url[$key] = UPLOAD_URL . '/tmp/' . $new_file_name;
		$file_path[$key] = UPLOAD_PATH . '/tmp/' . $new_file_name;
		$file_name[$key] = $_FILES[$file_element]['name'][$key];

		if ( !empty($_POST['doThumb']) && $able_thumb ) {
			// Thumbnail
			$thumb_suffix = '-thumb';
			$thumb_width = empty($_POST['twidth']) ? THUMB_WIDTH : $_POST['twidth'];
			$thumb_height = isset($_POST['theight']) ? $_POST['theight'] : 0;	// Null이 가능함
			$thumb_name = $wps_thumbnail->resize_image( $file_path[$key], $thumb_suffix, $thumb_width, $thumb_height );
			$thumb_path[$key] = UPLOAD_PATH . '/tmp/' . $thumb_name;
			$thumb_url[$key] = UPLOAD_URL . '/tmp/' . $thumb_name;
		} else {
			$thumb_path[$key] = UPLOAD_PATH . '/tmp/' . $new_file_name;
			$thumb_url[$key] = UPLOAD_URL . '/tmp/' . $new_file_name;
		}

	} else {
		$code = 505;
		$msg = '파일을 업로드할 수 없습니다. 관리자에게 문의해 주십시오.' .print_r($_FILES[$file_element]['error']);
		error_log("Not uploaded because of error #". print_r($_FILES[$file_element]['error'], true));
	}
}

$json = compact('code', 'msg', 'file_url', 'file_path', 'file_name', 'file_url', 'thumb_path', 'thumb_url');
echo json_encode( $json );

?>