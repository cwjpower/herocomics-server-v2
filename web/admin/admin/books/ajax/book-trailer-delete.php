<?php
/**
 * Created by PhpStorm.
 * User: endstern
 * Date: 18. 3. 19.
 * Time: 오전 3:21
 */

require_once '../../../wps-config.php';
require_once FUNC_PATH . '/functions-book.php';

$code = 0;
$msg = '';

if ( !wps_is_admin() ) {
    $code = 510;
    $msg = '관리자만 사용할 수 있습니다.';
    $json = compact('code', 'msg');
    exit( json_encode($json) );
}


if ( empty($_POST['trailer_list']) ) {
//    $code = 410;
//    $msg = '게시글을 선택해 주십시오.';
//    $json = compact('code', 'msg');
//    exit( json_encode($json) );
}

$count = 0;


/**
 * 한 건에 대한 삭제 요청, 여러 개 한꺼번에 삭제 요청 분리 해서 처리
 */
if ( !empty($_POST['trailer_id']) )
    lps_delete_book_trailer( $_POST['id'] );

if ( !empty($_POST['trailer_list']) ) {
    foreach ($_POST['trailer_list'] as $key => $val ) {
        if ( lps_delete_book_trailer($val) ) {
            $count++;
        }
    }
}

$json = compact('code', 'msg', 'count');
echo json_encode( $json );

?>