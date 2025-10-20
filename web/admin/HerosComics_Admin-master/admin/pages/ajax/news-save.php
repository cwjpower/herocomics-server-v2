<?php

error_reporting(0);
require_once '../../../wps-config.php';
require_once FUNC_PATH . '/functions-news.php';

$code = 0;
$msg = '';



//if ( !wps_is_admin() ) {
//    $code = 510;
//    $msg = '관리자만 사용할 수 있습니다.';
//    $json = compact('code', 'msg');
//    exit( json_encode($json) );
//}

//if ( empty($_POST['banner_title']) ) {
//    $code = 410;
//    $msg = '배너 제목을 입력해 주십시오.';
//    $json = compact('code', 'msg');
//    exit( json_encode($json) );
//}
//
//if ( empty($_POST['file_path']) ) {
//    $code = 412;
//    $msg = '배너 이미지를 등록해 주십시오.';
//    $json = compact('code', 'msg');
//    exit( json_encode($json) );
//}


/**
 * 새 뉴스이면 등록, 기존 뉴스이면 업데이트 처리
 */
$is_new = $_POST['is_new'];


if ($is_new =='Y'){
    $result = add_news();
} else {
    $result = update_news($_POST['news_id']);
}

if ( empty($result) ) {
    $code = 501;
    $msg = '등록하지 못했습니다.';
    $json = compact('code', 'msg');
    exit( json_encode($json) );
}

//
$json = compact('code', 'msg', 'result');
echo json_encode( $json );


