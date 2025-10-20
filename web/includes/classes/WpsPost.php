<?php

/*
 * Disabled
 */

exit;

/**
 * Name : Post Class
 * Desciption : Post Manage
 *
 * 		post_type > wps_board : guid > 게시판 Type
 *
 */
class WpsPost
{
	public $ID;
	public $post_author = 0;
	public $post_date = '0000-00-00 00:00:00';
	public $post_date_gmt = '0000-00-00 00:00:00';
	public $post_content = '';
	public $post_title = '';
	public $post_excerpt = '';
	public $post_status = 'publish';
	public $comment_status = 'open';
	public $ping_status = 'open';
	public $post_password = '';
	public $post_name = '';
	public $to_ping = '';
	public $pinged = '';
	public $post_modified = '0000-00-00 00:00:00';
	public $post_modified_gmt = '0000-00-00 00:00:00';
	public $post_content_filtered = '';
	public $post_parent = 0;
	public $guid = '';
	public $menu_order = 0;
	public $post_type = 'post';
	public $post_mime_type = '';
	public $comment_count = 0;
	public $attachment = '';
	public $attachment_meta_key = '';

	public function __construct() {}

	public function wps_insert_post( $postarr ) {
		global $wdb;

		$this->post_author = wps_get_current_user_id();
		$this->post_date = empty( $postarr['post_date'] ) ? date('Y-m-d H:i:s') : $postarr['post_date'];
		$this->post_content = empty( $postarr['post_content'] ) ? '' : $postarr['post_content'];
		$this->post_title = empty( $postarr['post_title'] ) ? '' : $postarr['post_title'];
		$this->post_excerpt = '';
		$this->post_status = 'publish';		// publish, draft, pending, private, trash, auto-draft, inherit
		$this->comment_status = 'open';
		$this->ping_status = 'open';
		$this->post_password = empty( $postarr['post_password'] ) ? '' : $postarr['post_password'];
		$this->post_name = empty( $postarr['post_name'] ) ? '' : $postarr['post_name'];
		$this->to_ping = empty( $postarr['to_ping'] ) ? '' : $postarr['to_ping'];
		$this->pinged = empty( $postarr['pinged'] ) ? '' : $postarr['pinged'];
		$this->post_content_filtered = empty( $postarr['post_content_filtered'] ) ? '' : $postarr['post_content_filtered'];
		$this->post_parent = empty( $postarr['post_parent'] ) ? 0 : $postarr['post_parent'];
		$this->guid = empty( $postarr['guid'] ) ? '' : $postarr['guid'];
		$this->menu_order = empty( $postarr['menu_order'] ) ? 0 : $postarr['menu_order'];
		$this->post_type = empty( $postarr['post_type'] ) ? '' : $postarr['post_type'];		// post, page, nav_menu_item, attachment, revision ( wps_board )
		$this->post_mime_type = empty( $postarr['post_mime_type'] ) ? '' : $postarr['post_mime_type'];
		$this->comment_count = 0;
// 		$this->attachment = empty( $postarr['attachment_path'] ) ? '' : $postarr['attachment_path'];
// 		$this->attachment_meta_key = empty( $postarr['attachment_meta_key'] ) ? 'unknown' : $postarr['attachment_meta_key'];

		$ym = date('Ym');

		$query = "
				INSERT INTO
					wps_posts
					(
						ID,
						post_author,
						post_date,
						post_date_gmt,
						post_content,
						post_title,
						post_excerpt,
						post_status,
						comment_status,
						ping_status,
						post_password,
						post_name,
						to_ping,
						pinged,
						post_modified,
						post_modified_gmt,
						post_content_filtered,
						post_parent,
						guid,
						menu_order,
						post_type,
						post_mime_type,
						comment_count
					)
				VALUES
					(
						NULL, ?, ?, UTC_TIMESTAMP(), ?,
						?, ?, ?, ?, ?,
						?, ?, ?, ?, NULL,
						NULL, ?, ?, ?, ?,
						?, ?, ?
					)
		";
		$stmt = $wdb->prepare( $query );

		if ( !empty($stmt) ) {
			// CKEditor
			preg_match_all('/(src)=("[^"]*")/i', $this->post_content, $images, PREG_SET_ORDER);

			if ( !empty($images) ) {
				$images_tmp = array();		// Temporary directory path
				$images_real = array();		// Real directory path

				foreach ( $images  as $key => $val ) {
					if ( stripos($val[2], UPLOAD_URL) !== false ) {
						$images_tmp[] = $val[2];
					}
				}

				if ( !empty($images_tmp) ) {
					// Image
					$upload_tmp['path'] = UPLOAD_PATH . '/tmp';
					$upload_real['url'] = UPLOAD_URL . '/ckeditor/uploads/' . $ym;
					$upload_real['path'] = UPLOAD_PATH . '/ckeditor/uploads/' . $ym;

					if ( !is_dir($upload_real['path']) ) {
						mkdir($upload_real['path'], 0777, true);
					}

					foreach ( $images_tmp as $key => $val ) {
						$img_url = str_replace( '"', '', $val );	// 큰 따옴표가 있는 것에 주의!
						$changed_tmp = str_replace( UPLOAD_URL, UPLOAD_PATH, $img_url );
						$changed_real = str_replace( $upload_tmp['path'], $upload_real['path'], $changed_tmp );

						if ( rename($changed_tmp, $changed_real) ) {
							$this->post_content = str_replace( $img_url, UPLOAD_URL . '/ckeditor/uploads/' . $ym . '/' . basename($img_url), $this->post_content );

							$images_real[] = $changed_real;
						}
					}
				}
			}

			$stmt->bind_param( 'issssssssssssisissi',
					$this->post_author,
					$this->post_date,
					$this->post_content,
					$this->post_title,
					$this->post_excerpt,
					$this->post_status,
					$this->comment_status,
					$this->ping_status,
					$this->post_password,
					$this->post_name,
					$this->to_ping,
					$this->pinged,
					$this->post_content_filtered,
					$this->post_parent,
					$this->guid,
					$this->menu_order,
					$this->post_type,
					$this->post_mime_type,
					$this->comment_count
			);

			if ( !$stmt->execute() ) {
				return false;
			}

			$post_ID = $wdb->insert_id;

			// CKEditor에 업로드 파일
			if ( !empty($images_real) ) {
				$serialized = serialize($images_real);
				wps_update_post_meta( $post_ID, 'wps_ckeditor_attached_files', $serialized );
			}

			/*
			 * input type="file" name="attachement[]"
			 * QnA에서 사용함.
			 */
			if ( !empty($_FILES['attachment']['tmp_name'][0]) ) {
				$upload_dir = UPLOAD_PATH . '/board/' . $ym;
				
				if ( !is_dir($upload_dir) ) {
					mkdir($upload_dir, 0777, true);
				}
				$fname = $_FILES['attachment']['name'][0];
				$tmp_file = $_FILES['attachment']['tmp_name'][0];
				$fpath = UPLOAD_PATH . '/board/' . $ym . '/' . wps_make_rand();

				$result = move_uploaded_file( $tmp_file, $fpath );
				
				if ( $result ) {
					$carray = compact( 'fname', 'fpath' );
					$meta_val = serialize($carray);
					wps_update_post_meta( $post_ID, 'wps_post_attachment', $meta_val );
				}
			}
			return $post_ID;
		} else {
			return false;
		}
	}

	public function wps_update_post( $postarr ) {
		global $wdb;

		$this->ID = $postarr['ID'];
		$this->post_author = wps_get_current_user_id();
		$this->post_content = empty( $postarr['post_content'] ) ? '' : $postarr['post_content'];
		$this->post_title = empty( $postarr['post_title'] ) ? '' : $postarr['post_title'];
		$this->guid = empty( $postarr['guid'] ) ? '' : $postarr['guid'];

		if ( !empty($postarr['attachment_path']) && stripos($postarr['attachment_path'], '/tmp/') !== false ) {
			$this->attachment = $postarr['attachment_path'];
		} else {
			$this->attachment = '';
		}
		$this->attachment_meta_key = empty( $postarr['attachment_meta_key'] ) ? 'unknown' : $postarr['attachment_meta_key'];

		$ym = date('Ym');

		$query = "
				UPDATE
					wps_posts
				SET
					post_author = ?,
					post_content = ?,
					post_title = ?,
					post_modified = NOW(),
					post_modified_gmt = UTC_TIMESTAMP(),
					guid = ?
				WHERE
					ID = ?
		";
		$stmt = $wdb->prepare( $query );

		if ( !empty($stmt) ) {
			// CKEditor
			preg_match_all('/(src)=("[^"]*")/i', $this->post_content, $images, PREG_SET_ORDER);

			if ( !empty($images) ) {
				$images_tmp = array();		// Temporary directory path
				$images_real = array();		// Real directory path

				foreach ( $images  as $key => $val ) {
					if ( stripos($val[2], UPLOAD_URL) !== false ) {
						$images_tmp[] = $val[2];
					}
				}

				if ( !empty($images_tmp) ) {
					// Image
					$upload_tmp['path'] = UPLOAD_PATH . '/tmp';
					$upload_real['url'] = UPLOAD_URL . '/ckeditor/uploads/' . $ym;
					$upload_real['path'] = UPLOAD_PATH . '/ckeditor/uploads/' . $ym;

					if ( !is_dir($upload_real['path']) ) {
						mkdir($upload_real['path'], 0777, true);
					}

					foreach ( $images_tmp as $key => $val ) {
						$img_url = str_replace( '"', '', $val );	// 큰 따옴표가 있는 것에 주의!
						$changed_tmp = str_replace( UPLOAD_URL, UPLOAD_PATH, $img_url );
						$changed_real = str_replace( $upload_tmp['path'], $upload_real['path'], $changed_tmp );

						if ( rename($changed_tmp, $changed_real) ) {
							$this->post_content = str_replace( $img_url, UPLOAD_URL . '/ckeditor/uploads/' . $ym . '/' . basename($img_url), $this->post_content );

							$images_real[] = $changed_real;
						}
					}
				}
			}

			$stmt->bind_param( 'isssi',
					$this->post_author,
					$this->post_content,
					$this->post_title,
					$this->guid,
					$this->ID
			);

			if ( !$stmt->execute() ) {
				return false;
			}

			// CKEditor에 업로드 파일
			if ( !empty($images_real) ) {
				$serialized = serialize($images_real);
				wps_update_post_meta( $this->ID, 'wps_ckeditor_attached_files', $serialized );
			}

			// 임시 등록된 파일을 저장할 경로로 이동
			if ( !empty( $this->attachment ) ) {

				$unserialize = unserialize( $this->attachment );
				$attachment_file = $unserialize['path'];
				$attachment_name = $unserialize['name'];

				if ( is_file( $attachment_file ) ) {

					$filename = basename( $attachment_file );

					$upload_real['url'] = UPLOAD_URL . '/board/' . $ym;
					$upload_real['path'] = UPLOAD_PATH . '/board/' . $ym;

					if ( !is_dir($upload_real['path']) ) {
						mkdir($upload_real['path'], 0777, true);
					}

					$result = rename( $attachment_file, $upload_real['path'] . '/' . $filename );

					if ( $result ) {
						$meta_val['url'] = $upload_real['url'] . '/' . $filename;
						$meta_val['path'] = $upload_real['path'] . '/' . $filename;
						$meta_val['name'] = $attachment_name;
						$serialized = serialize($meta_val);
						wps_update_post_meta( $this->ID, $this->attachment_meta_key, $serialized );
					}
				}
			}
			return true;
		} else {
			return false;
		}
	}
}

?>