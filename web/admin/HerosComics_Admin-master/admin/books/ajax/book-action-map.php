<?php
/**
 * 1. 넘겨 받은 정보를 이용해서 Action View 정보를 DB 에 저장한다
 * 2. 넘겨 받은 정보를 이용해서 .avf 파일을 생성한다.
 *
 *
 * User: endstern
 * Date: 18. 1. 31.
 * Time: 오전 4:37
 */

//header('Content-Type: application/json');

require_once '../../../wps-config.php';
require_once FUNC_PATH . '/functions-book.php';

$code = 0;
$msg = '';

$book_id = $_POST['book-id'];
$page = intval($_POST['Page']);
$page_name = $_POST['Page'];
$width = intval($_POST['Width']);
$height = intval($_POST['Height']);
$pannels = $_POST['p'];

$pannels_info=[];

foreach($pannels as $pannel){
    array_push($pannels_info, json_decode($pannel, true));
}

// make pannel info array[] : 항목 정보 스펠링 주의 !
$pannels_info_final =['Page'=>$page, 'Name'=>$page_name, 'Width'=>$width, 'Height'=>$height, 'Frames'=>$pannels_info];

// 저장할 json 포맷 생성  pannel info array > JSON Format
$pannels_info_json = json_encode($pannels_info_final);




/**
 * 이미 등록되어 있는 패널 정보가 있다면
 * 새로 넘겨 받은 패널 정보로 업데이트 한다.
 * 등록되어 있는 정보가 없으면 신규 등록
 */


// 해당 페이지 등록 정보 조회
$is_book_action_page = lps_get_book_action_page($book_id, $page_name);

//var_dump($is_book_action_page);
//die('book page action info');


if(isset($is_book_action_page['id'])) {
// 패널 정보를 json 형태로 저장 한다.
    lps_update_book_action($pannels_info_json);
    error_log('============== 페이지 액션 뷰 업데이트  ======================');
} else {
    lps_add_book_action($pannels_info_json);
    error_log('============== 페이지 액션 뷰 신규  ======================');
}

// 액션 뷰 등록 정보 조회
$book_action_rows = lps_get_book_action($book_id);


?>

<!doctype html>
<html>
<head>
    <title>액션뷰 정보 관리 </title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">
    <!--	<link rel="stylesheet" href="--><?php //echo ADMIN_URL ?><!--/css/font-awesome.min.css">-->
    <script defer src="https://use.fontawesome.com/releases/v5.0.6/js/all.js"></script>
    <link rel="stylesheet" href="<?php echo INC_URL ?>/css/bootstrap.min.css">
    <link rel="stylesheet" href="easy-mapper.css">
    <!--	<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>-->
    <script src="<?php echo ADMIN_URL ?>/js/jquery-2.2.3.min.js"></script>
    <script src="easy-mapper-1.2.0.js"></script>

</head>
<body>

<div class="container">
    <div class="row">
        <div class="col-lg-8 col-lg-offset-2">
            <h3>액션뷰 정보 생성 완료 </h3>
            <div class="alert alert-success" role="alert">
                정상적으로 저장되었습니다! <a href="javascript:history.go(-1);" class="btn"> 페이지 추가 하기 </a>
            </div>

            <?php
            $pannels_info=[] ;
            foreach($book_action_rows as $row){
                array_push($pannels_info , json_decode($row['pannel_info'], true) ) ;
            }
            ?>
            <pre>
				<?php echo json_encode($pannels_info, JSON_PRETTY_PRINT);?>
			</pre>

        </div>
    </div>
</div>

</body>
