<?php
/**
 * Created by PhpStorm.
 * User: endstern
 * Date: 18. 3. 19.
 * Time: 오전 3:21
 */

require_once '../../../wps-config.php';
require_once FUNC_PATH . '/functions-news.php';

$code = 0;
$msg = '';

if ( !wps_is_admin() ) {
    $code = 510;
    $msg = '관리자만 사용할 수 있습니다.';
    $json = compact('code', 'msg');
    exit( json_encode($json) );
}

error_log($_POST['id']);

if ( empty($_POST['news_list']) ) {
//    $code = 410;
//    $msg = '게시글을 선택해 주십시오.';
//    $json = compact('code', 'msg');
//    exit( json_encode($json) );
}

$count = 0;

//error_log(print_r($_POST['news_list'], true));


if (!empty($_POST['id']))
    delete_news($_POST['id']);

if (!empty($_POST['news_list'])) {
    foreach ($_POST['news_list'] as $key => $val) {
        if(delete_news($val)) {
            $count++;
        }
    }
}

$json = compact('code', 'msg', 'count');
echo json_encode( $json );

?>