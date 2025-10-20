<?php
require_once '../../wps-config.php';
require_once FUNC_PATH . '/functions-book.php';

if ( empty($_GET['trailer_id'] )) {
    lps_alert_back( '뉴스 아이디가 존재하지 않습니다.' );
}
$trailer_id = $_GET['trailer_id'];

// new 정보 조회
$trailer_row = lps_get_book_trailer( intval($trailer_id ));

$trailer_title = htmlspecialchars( $trailer_row['trailer_title'] );
$trailer_desc = htmlspecialchars( $trailer_row['trailer_desc'] );
$trailer_url = $trailer_row['trailer_url'];
$comics_brand = $trailer_row['comics_brand'];
$open_yn = $trailer_row['open_yn'];
$user_id = empty($trailer_row['user_id']) ? wps_get_current_user_id() : $trailer_row['user_id'] ;

$created_dt = $trailer_row['created_dt'];
$updated_dt = $trailer_row['updated_dt'];

// 사용자 정보 조회
$user_rows = wps_get_user( $user_id );
$user_login = $user_rows['user_login'];
$user_name = $user_rows['user_name'];


require_once ADMIN_PATH . '/admin-header.php';

require_once './books-lnb.php';
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
                <li><a href="<?php echo ADMIN_URL ?>/books/">책 관리</a></li>
                <li><a href="<?php echo ADMIN_URL ?>/books/book_trailer_list.php">북 트레일러 관리</a></li>
                <li class="active"><b>북 트레일러 상세 보기 </b></li>
            </ol>
        </div>

        <!-- Main content -->
        <div class="content body">

            <div class="box box-primary">
                <div class="box-header">
                    <div class="pull-right">
                        <a href="book_trailer_edit.php?trailer_id=<?php echo $trailer_id ?>" class="btn btn-info margin">수정</a>
                        <a href="book_trailer_edit.php?trailer_id=<?php echo $trailer_id ?>" id="btn-trailer-delete" class="btn btn-default margin">
                            <i class="fa fa-minus"></i> 삭제</a>
                    </div>
                </div>

                <div class="box-body">


                    <div class="row">
                    <div class="col-lg-8">
                        <table class="table table-bordered">
                            <!--                        <colgroup>-->
                            <!--                            <col style="">-->
                            <!--                            <col style="width: 35%;">-->
                            <!--                            <col style="width: 15%; min-width: 100px;">-->
                            <!--                            <col>-->
                            <!--                        </colgroup>-->
                            <tbody>
                            <tr>
                                <th class="item-label" style="width:150px;">트레일러 제목</th>
                                <td colspan="3"><?php echo $trailer_title ?></td>
                            </tr>
                            <tr>
                                <th class="item-label">트레일러 내용 </th>
                                <td colspan="3"><?php echo $trailer_desc ?></td>
                            </tr>
                            <tr>
                                <th>코믹스 브랜드</th>
                                <td>
                                    <div class="book-tag tag-<?=$wps_comics_brand_css[$comics_brand] ?>" style="width:50px;">
                                        <?php echo $wps_comics_brand[$comics_brand] ?>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>작성자</th>
                                <td> <?php echo $user_name; ?></td>
                            </tr>
                            <tr>
                                <th>작성일(수정일)</th>
                                <td> <?php echo $created_dt; ?> (<?php echo $updated_dt ?>)</td>
                            </tr>
                            <tr>
                                <th>트레일러 동영상</th>
                                <td colspan="3">

                                    <iframe width="500px" height="300px" src="<?php echo $trailer_url; ?>"></iframe>
                                </td>
                            </tr>

                            </tbody>
                        </table>
                    </div>
                    </div>


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
            $("#btn-trailer-delete").click(function() {
                if (!confirm("삭제하시겠습니까?")) {
                    return;
                }

                showLoader();

                $.ajax({
                    type : "POST",
                    url : "./ajax/book-trailer-delete.php",
                    data : {
                        "id" : "<?php echo $trailer_id ?>"
                    },
                    dataType : "json",
                    success : function(res) {
                        hideLoader();
                        if (res.code == "0") {
                            location.href = "book_trailer_list.php";
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