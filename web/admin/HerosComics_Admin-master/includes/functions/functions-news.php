<?php

require_once INC_PATH . '/classes/WpsThumbnail.php';

/**
 * @return mixed
 * 마블 디씨 이미지 코믹스 뉴스 등록
 */
function add_news() {
    global $wdb;

    $news_title = $_POST['news_title'];
    $news_sub_title = $_POST['news_sub_title'];
    $news_content = $_POST['news_content'];
    $comics_brand = empty($_POST['comics_brand']) ? '1' : $_POST['comics_brand'];
    $open_yn = empty($_POST['open_yn']) ? 'N' : $_POST['open_yn'];
    $main_view_yn = empty($_POST['main_view_yn']) ? 'N' : $_POST['main_view_yn'];

    $create_id = wps_get_current_user_id();
    $update_id = wps_get_current_user_id();

//    $created_dt = date('Y-m-d H:i:s');
//    $updated_dt = date('Y-m-d H:i:s');

    $query = "
			INSERT INTO
				bt_news
				(
					ID,
					news_title,
					news_sub_title,
					news_content,
					comics_brand,
					open_yn,
					main_view_yn,
					create_id,
					update_id,
					created_dt,
					updated_dt

				)
			VALUES
				(
					NULL,
					?,
					?,
					?,
					?,
					?,
					?,
					?,
					?,
					NOW(),
					NOW()
				)
	";
    $stmt = $wdb->prepare( $query );
    $stmt->bind_param( 'ssssssii',
        $news_title,
        $news_sub_title,
        $news_content,
        $comics_brand,
        $open_yn,
        $main_view_yn,
        $create_id,
        $update_id
    );

    $stmt->execute();

    $ID = $wdb->insert_id;

    // File Attachment
    if ( $ID && !empty($_FILES['attachment']['name']) ) {

        $yyyymm = date('Ym');
        $upload_dir = UPLOAD_PATH . '/news/' . $yyyymm .'/' ;
        $upload_url = UPLOAD_URL . '/news/'. $yyyymm .'/' ;

        /**
         * thumb 디렉토리 분리 안하고 같은 디렉토리로 사용
         * $upload_dir_thumb = $upload_dir . 'thumb';
         * $upload_url_thumb = $upload_url . 'thumb';
         *
         * if ( !is_dir($upload_dir_thumb) ) {
         *  mkdir($upload_dir_thumb, 0777, true);
         * }
         *
         */

        //$meta_value = array();


        if ( !is_dir($upload_dir) ) {
            mkdir($upload_dir, 0777, true);
        }

        $wps_thumbnail = new WpsThumbnail();

        foreach ( $_FILES['attachment']['name'] as $key => $val ) {
            $file_ext = strtolower(pathinfo( $val, PATHINFO_EXTENSION ));

            if ( in_array($file_ext, unserialize(WPS_IMAGE_EXT)) ) {
                $new_file_name = wps_make_rand() . '.' . $file_ext;
            } else {
                $new_file_name = wps_make_rand();
            }


            $new_val['file_path'] = $upload_dir .  $new_file_name;
            $new_val['file_url'] = $upload_url .  $new_file_name;
            $new_val['file_name'] = $val;
            $new_val['file_size'] = $_FILES['attachment']['size'][$key];
            $new_val['file_type'] = $_FILES['attachment']['type'][$key];
            $result = move_uploaded_file( $_FILES['attachment']['tmp_name'][$key], $new_val['file_path'] );
            // 이 아래에 추가
            if (!$result) {
                error_log("Upload failed: " . $_FILES['attachment']['tmp_name'][$key] . " -> " . $new_val['file_path']);
                error_log("Upload error: " . print_r(error_get_last(), true));
            }

            $thumb_suffix = '-thumb';
            $thumb_width = 360 ;
            //$thumb_height = isset($_POST['theight']) ? $_POST['theight'] : 0;	// Null이 가능함
            $thumb_name = $wps_thumbnail->resize_image( $new_val['file_path'], $thumb_suffix, $thumb_width );
            $thumb_path[$key] = $upload_dir .  $thumb_name;
            $thumb_url[$key] = $upload_url .  $thumb_name;

            $size = getimagesize($thumb_path[$key]);
            $thumb_w = $size[0];
            $thumb_h = $size[1];

            //array_push($meta_value, $new_val);

            /**
             * 파일을 업로드 하고 관련된 정보를 업데이트 한다.
             */
            $query = "
					UPDATE
						bt_news
					SET
						img_name = ?,
						img_url = ?,
						img_thumb_url =?,
						img_thumb_w =?,
						img_thumb_h = ?
					WHERE
						ID = ?
			";
            $stmt = $wdb->prepare( $query );
            $stmt->bind_param( 'sssiii',
                $new_file_name,
                $new_val['file_url'],
                $thumb_url[$key],
                $thumb_w,
                $thumb_h,
                $ID
            );
            $stmt->execute();


        }
    //$meta_value = serialize( $meta_value );
    //wps_update_post_meta( $ID, 'wps-post-attachment', $meta_value );
    }

    return $ID;
}


function get_news($news_id)
{
    global $wdb;

    $query = " SELECT * FROM bt_news WHERE id = ? ";
    $stmt = $wdb->prepare( $query );
    $stmt->bind_param( 'i', $news_id );
    $stmt->execute();

    return $wdb->get_row($stmt);
}
/**
 * @return mixed
 * 마블 디씨 이미지 코믹스 뉴스 수정
 */
function update_news($news_id)
{
    global $wdb;

    $news_title = $_POST['news_title'];
    $news_sub_title = $_POST['news_sub_title'];
    $news_content = $_POST['news_content'];
    $comics_brand = empty($_POST['comics_brand']) ? '1' : $_POST['comics_brand'];
    $open_yn = empty($_POST['open_yn']) ? 'N' : $_POST['open_yn'];
    $main_view_yn = empty($_POST['main_view_yn']) ? 'N' : $_POST['main_view_yn'];

    $update_id = wps_get_current_user_id();

    $query = "
			UPDATE bt_news
				SET
					news_title = ?,
					news_sub_title  = ?,
					news_content = ?,
					comics_brand = ?,
					open_yn = ?,
					main_view_yn = ?,
					update_id = ?,
					updated_dt = NOW()
			WHERE id =?
	";
    $stmt = $wdb->prepare( $query );
    $stmt->bind_param( 'ssssssii',
        $news_title,
        $news_sub_title,
        $news_content,
        $comics_brand,
        $open_yn,
        $main_view_yn,
        $update_id,
        $news_id
    );

    $stmt->execute();

    // File Attachment
    // todo : 대표 이미지 파일이 있다면 삭제처리 추가
    if ( !empty($_FILES['attachment']['name']) ) {

        $yyyymm = date('Ym');
        $upload_dir = UPLOAD_PATH . '/news/' . $yyyymm .'/' ;
        $upload_url = UPLOAD_URL . '/news/'. $yyyymm .'/' ;

        if ( !is_dir($upload_dir) ) {
            mkdir($upload_dir, 0777, true);
        }

        $wps_thumbnail = new WpsThumbnail();

        foreach ( $_FILES['attachment']['name'] as $key => $val ) {
            $file_ext = strtolower(pathinfo( $val, PATHINFO_EXTENSION ));

            if ( in_array($file_ext, unserialize(WPS_IMAGE_EXT)) ) {
                $new_file_name = wps_make_rand() . '.' . $file_ext;
            } else {
                $new_file_name = wps_make_rand();
            }


            $new_val['file_path'] = $upload_dir .  $new_file_name;
            $new_val['file_url'] = $upload_url .  $new_file_name;
            $new_val['file_name'] = $val;
            $new_val['file_size'] = $_FILES['attachment']['size'][$key];
            $new_val['file_type'] = $_FILES['attachment']['type'][$key];
            $result = move_uploaded_file( $_FILES['attachment']['tmp_name'][$key], $new_val['file_path'] );

            $thumb_suffix = '-thumb';
            $thumb_width = 360 ;
            //$thumb_height = isset($_POST['theight']) ? $_POST['theight'] : 0;	// Null이 가능함
            $thumb_name = $wps_thumbnail->resize_image( $new_val['file_path'], $thumb_suffix, $thumb_width );
            $thumb_path[$key] = $upload_dir .  $thumb_name;
            $thumb_url[$key] = $upload_url .  $thumb_name;

            $size = getimagesize($thumb_path[$key]);
            $thumb_w = $size[0];
            $thumb_h = $size[1];

            //array_push($meta_value, $new_val);

            /**
             * 파일을 업로드 하고 관련된 정보를 업데이트 한다.
             */
            $query = "
					UPDATE
						bt_news
					SET
						img_name = ?,
						img_url = ?,
						img_thumb_url =?,
						img_thumb_w =?,
						img_thumb_h = ?
					WHERE
						ID = ?
			";
            $stmt = $wdb->prepare( $query );
            $stmt->bind_param( 'sssiii',
                $new_file_name,
                $new_val['file_url'],
                $thumb_url[$key],
                $thumb_w,
                $thumb_h,
                $news_id
            );
            $stmt->execute();
        }
        //$meta_value = serialize( $meta_value );
        //wps_update_post_meta( $ID, 'wps-post-attachment', $meta_value );
    }

    return $wdb->affected_rows;
}

/**
 * @return mixed
 * 마블 디씨 이미지 코믹스 뉴스 삭제
 */
function delete_news($news_id)
{
    global $wdb;

    // todo: 해당 아이디의 뉴스에 연결된 이미지 모두 삭제
    // todo: 해당 아이디의 뉴스에 걸린 댓글 삭제

    // 해당 아이디의 뉴스 삭제
    $query = "
				DELETE FROM
					bt_news
				WHERE
					ID = ?
			";
    $stmt = $wdb->prepare( $query );
    $stmt->bind_param( 'i', $news_id );
    $stmt->execute();

    return $wdb->affected_rows;
}

