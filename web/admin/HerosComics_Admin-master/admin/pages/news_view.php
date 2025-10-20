<?php
require_once '../../wps-config.php';
require_once FUNC_PATH . '/functions-news.php';

if ( empty($_GET['id'] )) {
    lps_alert_back( '뉴스 아이디가 존재하지 않습니다.' );
}
$news_id = $_GET['id'];

// new 정보 조회
$news_row = get_news( intval($news_id ));

//var_dump($news_row);

$news_title = htmlspecialchars( $news_row['news_title'] );
$news_sub_title = htmlspecialchars( $news_row['news_sub_title'] );
$news_content = $news_row['news_content'];

$comics_brand = $news_row['comics_brand'];
$share_cnt = empty($news_row['share_cnt']) ? 0 : number_format($news_row['share_cnt']);
$read_cnt = empty($news_row['read_cnt']) ? 0 : number_format($news_row['read_cnt']);
$comments_cnt = empty($news_row['comments_cnt']) ? 0 : number_format($news_row['comments_cnt']);

$open_yn = $news_row['open_yn'];
$main_view_yn = $news_row['main_view_yn'];

$img_thum_url = $news_row['img_thumb_url'];
$img_url = $news_row['img_url'];
$img_name = $news_row['img_name'];

$create_id = $news_row['create_id'];
$update_id = $news_row['update_id'];

$created_dt = $news_row['created_dt'];
$updated_dt = $news_row['updated_dt'];
// 사용자 정보 조회
$user_rows = wps_get_user( $create_id );
$user_login = !empty($user_rows['user_login']) ? $user_rows['user_login'] : 'Unknown';
$user_name = !empty($user_rows['user_name']) ? $user_rows['user_name'] : 'Unknown';


require_once ADMIN_PATH . '/admin-header.php';

require_once './pages-lnb.php';
?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <h1>
                상세보기
            </h1>
            <ol class="breadcrumb">
                <li><a href="<?php echo ADMIN_URL ?>/admin.php"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="<?php echo ADMIN_URL ?>/pages/">페이지 관리</a></li>
                <li><a href="<?php echo ADMIN_URL ?>/pages/news_list.php">뉴스관리</a></li>
                <li class="active"><b>뉴스 확인</b></li>
            </ol>
        </div>

        <!-- Main content -->
        <div class="content body">

            <div class="box box-primary">
                <div class="box-header">
                    <div class="pull-right">
                        <a href="news_edit.php?id=<?php echo $news_id ?>" class="btn btn-info btn-flat margin">수정</a>
                        <button type="button" id="btn-news-delete" class="btn btn-danger btn-flat">삭제</button>
                    </div>
                </div>
                <div class="box-body">

                    <table class="table table-bordered">
                        <colgroup>
                            <col style="width: 15%; min-width: 100px;">
                            <col style="width: 35%;">
                            <col style="width: 15%; min-width: 100px;">
                            <col>
                        </colgroup>
                        <tbody>
                        <tr>
                            <th class="item-label">제목</th>
                            <td colspan="3"><?php echo $news_title ?></td>
                        </tr>
                        <tr>
                            <th class="item-label">소제목</th>
                            <td colspan="3"><?php echo $news_sub_title ?></td>
                        </tr>
                        <tr>
                            <th>작성자</th>
                            <td><?php echo $user_name ?></td>
                            <th>공개</th>
                            <td>공개여부 : <?php echo $open_yn ?>  &nbsp;&nbsp; 메인 노출 :  <?php echo $main_view_yn ?></td>
                        </tr>
                        <tr>
                            <th>코믹스 브랜드</th>
                            <td>
                                <div class="book-tag tag-<?=$wps_comics_brand_css[$comics_brand] ?>" style="width:50px;">
                                    <?php echo $wps_comics_brand[$comics_brand] ?>
                                </div>
                            </td>
                            <th>조회수</th>
                            <td><?php echo number_format($read_cnt) ?></td>
                        </tr>
                        <tr>
                            <th>작성일(수정일)</th>
                            <td> <?php echo $created_dt; ?> (<?php echo $updated_dt ?>)</td>
                            <th>댓글 수</th>
                            <td><?php echo $comments_cnt; ?></td>
                        </tr>
                        <tr>
                            <th>뉴스 이미지 (width:360px)</th>
                            <td colspan="3"><img src="<?php echo $img_thum_url; ?>" alt=""></td>
                        </tr>
                        <tr>
                            <th>내용</th>
                            <td colspan="3"><?php echo $news_content ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div><!-- /.box-body -->
                <div class="box-footer text-center">
                    <button id="cancel-btn" type="button" class="btn btn-success"><i class="fa fa-times"></i> 확인</button>
                </div><!-- /.box-footer -->
            </div><!-- /. box -->
        </div><!-- /.content -->
    </div><!-- /.content-wrapper -->

    <script src="<?php echo INC_URL ?>/js/ls-util.js"></script>
    <script src="<?php echo INC_URL ?>/js/jquery/jquery.oLoader.min.js"></script>

    <script>
        $(function() {
            $("#cancel-btn").click(function() {
                history.back();
            });

            // 삭제
            $("#btn-news-delete").click(function() {
                if (!confirm("삭제하시겠습니까?")) {
                    return;
                }

                showLoader();

                $.ajax({
                    type : "POST",
                    url : "./ajax/news-delete.php",
                    data : {
                        "id" : "<?php echo $news_id ?>"
                    },
                    dataType : "json",
                    success : function(res) {
                        hideLoader();
                        if (res.code == "0") {
                            location.href = "./news_list.php";
                        } else {
                            alert(res.msg);
                        }
                    }
                });
            });
        });
    </script>
<?php
require_once ADMIN_PATH . '/admin-footer.php';
?>
