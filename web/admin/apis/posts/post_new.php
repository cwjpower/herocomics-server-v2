<?php
/*
 * Desc : API 담벼락 > 게시글 등록
 * 	method : POST
 */
require_once '../../wps-config.php';
require_once FUNC_PATH . '/functions-activity.php';

$code = 0;
$msg = '';

if ( empty($_POST['uid']) ) {
	$code = 400;
	$msg = '로그인 후 이용해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( empty($_POST['book_id']) ) {
	$code = 402;
	$msg = '책을 선택해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( empty($_POST['title']) ) {
	$code = 403;
	$msg = '제목을 입력해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

if ( empty($_POST['content']) ) {
	$code = 404;
	$msg = '내용을 입력해 주십시오.';
	$json = compact('code', 'msg');
	exit( json_encode($json) );
}

$object_type = empty($_POST['object_type']) ? 'activity' : $_POST['object_type'];

if (!strcmp($object_type, 'activity')) {
	
	$user_id = $_POST['uid'];
	$book_id = $_POST['book_id'];
	$act_title = $_POST['title'];
	$act_content = $_POST['content'];
	
	$component = 'activity';
	$type = empty($_POST['type']) ? 'activity_update' : $_POST['type'];
	$item_id = empty($_POST['item_id']) ? 0 : $_POST['item_id'];
	$secondary_item_id = empty($_POST['secondary_item_id']) ? 0 : $_POST['secondary_item_id'];
	$hide_sitewide = empty($_POST['hide_sitewide']) ? 0 : $_POST['hide_sitewide'];
	
	// 게시글 등록
	$query = "
			INSERT INTO
				bt_activity
				(
					id,
					user_id,
					book_id,
					component,
					type,
					subject,
					content,
					item_id,
					secondary_item_id,
					hide_sitewide,
					created_dt,
					count_hit,
					count_like,
					count_comment
							
				)
			VALUES
				(
					NULL, ?, ?, ?, ?,
					?, ?, ?, ?, ?,
					NOW(), 0, 0, 0
				)
	";
	$stmt = $wdb->prepare( $query );
	$stmt->bind_param( 'iissssiii',
			$user_id, $book_id, $component, $type, $act_title, $act_content, $item_id, $secondary_item_id, $hide_sitewide
	);
	$stmt->execute();
	
	$ID = $wdb->insert_id;
	
	// File Attachment
	if ( $ID && !empty($_FILES['attachment']['tmp_name']) ) {
		$yyyymm = date('Ym');
		$upload_dir = UPLOAD_PATH . '/community/' . $yyyymm . '/' . $book_id;
		$upload_url = UPLOAD_URL . '/community/' . $yyyymm . '/' . $book_id;
		$meta_value = array();
	
		if ( !is_dir($upload_dir) ) {
			mkdir($upload_dir, 0777, true);
		}
	
		foreach ( $_FILES['attachment']['name'] as $key => $val ) {
			if ($val) {
				$file_ext = strtolower(pathinfo( $_FILES['attachment']['name'][$key], PATHINFO_EXTENSION ));
				$file_name = basename($_FILES['attachment']['name'][$key]);
				
				if ( in_array($file_ext, unserialize(WPS_IMAGE_EXT)) ) {
					$new_file_name = wps_make_rand() . '.' . $file_ext;
				} else {
					$new_file_name = wps_make_rand();
				}
				
				$upload_path = $upload_dir . '/' . $new_file_name;
				$result = move_uploaded_file( $_FILES['attachment']['tmp_name'][$key], $upload_path );
				
				if ( $result ) {
					$new_val['file_path'] = $upload_dir . '/' . $new_file_name;
					$new_val['file_url'] = $upload_url . '/' . $new_file_name;
					$new_val['file_name'] = $file_name;
					array_push($meta_value, $new_val);
				}
			}
		}
		
		$meta_value = serialize( $meta_value );
		lps_update_activity_meta( $ID, 'wps-community-attachment', $meta_value );
	}

} else {
	// 공지사항 등록
	$_POST['post_user_id'] = $_POST['uid'];
	
	$user_row = wps_get_user( $_POST['post_user_id'] );
	$display_name = $user_row['display_name'];
	$_POST['post_name'] = $display_name;
	
	$_POST['post_type'] = 'notice_new';
	$_POST['post_title'] = $_POST['title'];
	$_POST['post_content'] = $_POST['content'];
	$notice_book = $_POST['book_id'];

	$result = wps_add_post();
	$post_id = $result;

	if (!empty($post_id)) {
		$meta_value = serialize(compact('notice_book'));
		wps_update_post_meta($post_id, 'wps_notice_books', $meta_value);

		// File Attachment
		if ( !empty($_FILES['attachment']['tmp_name']) ) {
			$yyyymm = date('Ym');
			$upload_dir = UPLOAD_PATH . '/board/' . $post_id . '/' . $yyyymm;
			$upload_url = UPLOAD_URL . '/board/' . $post_id . '/' . $yyyymm;
			$meta_value = array();
		
			if ( !is_dir($upload_dir) ) {
				mkdir($upload_dir, 0777, true);
			}
		
			foreach ( $_FILES['attachment']['name'] as $key => $val ) {
				if ($val) {
					$file_ext = strtolower(pathinfo( $val, PATHINFO_EXTENSION ));
					$file_name = basename($_FILES['attachment']['name'][$key]);
		
					if ( in_array($file_ext, unserialize(WPS_IMAGE_EXT)) ) {
						$new_file_name = wps_make_rand() . '.' . $file_ext;
					} else {
						$new_file_name = wps_make_rand();
					}
					
					$upload_path = $upload_dir . '/' . $new_file_name;
					move_uploaded_file( $_FILES['attachment']['tmp_name'][$key], $upload_path );
						
					$new_val['file_path'] = $upload_dir . '/' . $new_file_name;
					$new_val['file_url'] = $upload_url . '/' . $new_file_name;
					$new_val['file_name'] = $file_name;
					array_push($meta_value, $new_val);
				}
			}
		
			$meta_value = serialize( $meta_value );
			wps_update_post_meta( $post_id, 'wps-post-attachment', $meta_value );
		}
	}
	
	$ID = $post_id;	// Notice ID
}

$json = compact( 'code', 'msg', 'ID' );
echo json_encode( $json );

?>