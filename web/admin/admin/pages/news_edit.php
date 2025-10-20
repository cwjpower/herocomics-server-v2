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
$user_login = $user_rows['user_login'];
$user_name = $user_rows['user_name'];

require_once ADMIN_PATH . '/admin-header.php';

require_once './pages-lnb.php';
?>

    <link rel="stylesheet" href="<?php echo INC_URL ?>/css/jquery-ui.css">

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <h1>
                뉴 수정
            </h1>
            <ol class="breadcrumb">
                <li><a href="<?php echo ADMIN_URL ?>/admin.php"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="<?php echo ADMIN_URL ?>/pages/">페이지 관리</a></li>
                <li><a href="<?php echo ADMIN_URL ?>/pages/news_list.php">뉴스관리 </a></li>
                <li class="active"><b>수정</b></li>
            </ol>
        </div>

        <!-- Main content -->
        <div class="content body">

                    <form enctype="multipart/form-data" id="form-news" method="POST">
                        <input type="hidden" name="is_new" value="N">
                        <input type="hidden" name="news_id" value="<?php echo $news_id; ?>">
                        <div class="box box-primary">
                            <div class="box-body">
                        <div class="form-group">
                            <label for="exampleInputEmail1">뉴스 제목</label>
                            <input type="text" value="<?php echo $news_title; ?> " class="form-control" id="news-title" name="news_title" placeholder="뉴스제목" required>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1">뉴스 소제목</label>
                            <input type="text" value="<?php echo $news_sub_title; ?>"class="form-control" id="news-sub-title" name="news_sub_title" placeholder="뉴스 소제목">
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" name="comics_brand" id="marvel" value="1" <?php echo($comics_brand == '1' ?  'checked' : ''); ?>>
                                마블
                            </label>
                            <label>
                                <input type="radio" name="comics_brand" id="dc" value="2" <?php echo($comics_brand == '2' ?  'checked' : ''); ?>>
                                디씨
                            </label>
                            <label>
                                <input type="radio" name="comics_brand" id="image" value="3" <?php echo($comics_brand == '3' ?  'checked' : ''); ?>>
                                이미지
                            </label>

                        </div>
                        <div class="form-group">
                            <label for="news-content">뉴스내용 </label>
                            <textarea id="news-content" name="news_content" class="form-control ckeditor_content" required>
                                <?php echo $news_content; ?>
                            </textarea>
                        </div>
                        <div class="form-group">
                            <label for="attachement-file">이미지</label>
                            <input type="file" name="attachment[]" id="attachement-file">

                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="open_yn" value="Y" <?php echo($open_yn =='Y' ? 'checked' : '');?>> 프론트에 공개합니다.
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="main_view_yn" value="Y" <?php echo($main_view_yn =='Y' ? 'checked' : '');?>> 메인에 노출 합니다.
                            </label>
                        </div>
                        <hr>
                        <!--                            <div class="form-group">-->
                        <!--                                <label for="">등록일자</label>-->
                        <!--                                <input type="text" class="form-control" id="created-dt" name="created_dt" placeholder="yyyy-mm-dd">-->
                        <!--                            </div>-->
                        <!--                            <div class="form-group">-->
                        <!--                                <label for="exampleInputPassword1">수정일자</label>-->
                        <!--                                <input type="text" class="form-control" id="updated-dt" name="updated_dt" placeholder="yyyy-mm-dd">-->
                        <!--                            </div>-->
                        <hr>
                        <button type="submit" class="btn btn-default">저장</button>

                        <a href="news_list.php" class="btn btn-primary" style="margin-left:3rem">리스트</a>
                </div><!-- /.box-footer -->
            </div><!-- /. box -->
                    </form>


        </div><!-- /.content -->
    </div><!-- /.content-wrapper -->

    <!-- jQuery Form plugin -->
    <script src="<?php echo INC_URL ?>/js/jquery/jquery.form.min.js"></script>
    <script src="<?php echo INC_URL ?>/js/jquery/jquery.serializeObject.min.js"></script>

    <script src="<?php echo INC_URL ?>/js/jquery-ui.min.js"></script>
    <script src="<?php echo INC_URL ?>/js/ls-util.js"></script>
    <script src="<?php echo INC_URL ?>/js/jquery/jquery.oLoader.min.js"></script>
    <!-- CkEditor -->
    <script src="<?php echo INC_URL ?>/js/ckeditor-4.5.10/ckeditor.js"></script>
    <script src="<?php echo INC_URL ?>/js/ckeditor-4.5.10/adapters/jquery.js"></script>

    <script>
        $(function() {
            // CKEditor
            var config = {
                height: 300,
                extraPlugins: 'autogrow',
                autoGrow_bottomSpace: 50,
                toolbar:
                    [
                        ['FontSize', 'TextColor', 'Bold', 'Italic', 'Underline', '-', 'NumberedList', 'BulletedList', 'Blockquote', 'Table', '-', 'Undo', 'Redo', '-', 'SelectAll'],
                        ['UIColor']
                    ]
            };
            //config.extraCss += "body{background:#hexcolor;}";

            // ckeditor init
            $(".ckeditor_content").ckeditor(config);


            $("#form-news").submit(function(e) {
                e.preventDefault();

//                if ( $.trim($("#news_title").val()) == "" ) {
//                    alert("제목을 입력해 주십시오.");
//                    $("#news_title").focus();
//                    return false;
//                }
//
//                if ( $.trim($("#news_content").val()) == "" ) {
//                    alert("내용을 입력해 주십시오.");
//                    return false;
//                }

                showLoader();

                $("#form-news").ajaxSubmit({
                    type : "POST",
                    url : "./ajax/news-save.php",
// 						data : $(this).serialize(),
                    data : $(this).serializeObject(),
                    dataType : "json",
                    success: function(xhr) {
                        hideLoader();
                        if ( xhr.code == "0" ) {
                            location.href = "./news_list.php";
                        } else {
                            alert( xhr.msg );
                        }
                    }
                });
            });

            //$("#post_content").ckeditor({});

            $('a[id*="delete-uploaded-file-"]').click(function(e) {
                e.preventDefault();
                var key = $(this).attr("id").replace(/\D/g, "");

                $.ajax({
                    type : "POST",
                    url : "./ajax/delete-uploaded-file.php",
                    data : {
                        "pid" : $("#post_id").val(),
                        "key" : key
                    },
                    dataType : "json",
                    success : function(res) {
                        if (res.code == "0") {
                            $("#uploaded-file-lists").remove();
                        } else {
                            alert(res.msg);
                        }
                    }
                });
            });

        }); //$
    </script>

<?php
require_once ADMIN_PATH . '/admin-footer.php';
?>