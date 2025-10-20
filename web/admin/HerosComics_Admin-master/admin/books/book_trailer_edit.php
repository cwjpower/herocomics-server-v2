<?php
require_once '../../wps-config.php';
require_once FUNC_PATH . '/functions-book.php';

if ( empty($_GET['trailer_id'] )) {
    lps_alert_back( '트레일러 아이디가 존재하지 않습니다.' );
}
$trailer_id = $_GET['trailer_id'];

// 수정할 트레일러 정보 조회
$trailer_row = lps_get_book_trailer($trailer_id );

$trailer_title = htmlspecialchars( $trailer_row['trailer_title'] );
$trailer_desc = !empty($trailer_row['trailer_desc']) ? htmlspecialchars($trailer_row['trailer_desc']) : '';
$trailer_url = $trailer_row['trailer_url'];
$comics_brand = $trailer_row['comics_brand'];
$open_yn = $trailer_row['open_yn'];
$user_id = empty($trailer_row['user_id']) ? wps_get_current_user_id() : $trailer_row['user_id'] ;

$created_dt = $trailer_row['created_dt'];
$updated_dt = $trailer_row['updated_dt'];

// 사용자 정보 조회
$user_rows = wps_get_user( $user_id );
$user_login = !empty($user_rows['user_login']) ? $user_rows['user_login'] : 'Unknown';
$user_name = !empty($user_rows['user_name']) ? $user_rows['user_name'] : 'Unknown';

require_once ADMIN_PATH . '/admin-header.php';
require_once './books-lnb.php';
?>

    <link rel="stylesheet" href="<?php echo INC_URL ?>/css/jquery-ui.css">

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <h1>
                트레일러 정보 수정
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

            <div class="box box-primary">
                <div class="box-body">
                <div class="col-lg-8">
                    <form enctype="multipart/form-data" id="form-trailer" method="POST">
                        <input type="hidden" name="is_new" value="N">
                        <input type="hidden" name="trailer_id" value="<?php echo $trailer_id; ?>">

                        <div class="form-group">
                            <label for="exampleInputEmail1">트레일러 제목</label>
                            <input type="text" value="<?php echo $trailer_title; ?> " class="form-control" id="trailer-title" name="trailer_title" placeholder="트레일러 제목" required>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1">트레일러 내용</label>
                            <textarea class="form-control" id="news-sub-title" name="trailer_desc" placeholder="트레일러 내용" rows="8"><?php echo trim($trailer_desc); ?></textarea>
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
                            <label for="attachement-file">트레일러 url</label>
                            <input type="text" value="<?php echo $trailer_url; ?> " class="form-control" id="trailer-url" name="trailer_url" placeholder="트레일러 제목"
                        </div>

                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="open_yn" value="Y" <?php echo($open_yn =='Y' ? 'checked' : '');?>> 프론트에 공개합니다.
                            </label>
                        </div>



                        <hr>
                        <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> 등록합니다</button>

                        <a href="book_trailer_list.php" class="btn btn-default" style="margin-left:1rem">리스트</a>
                    </form>
                    </div>
                </div>
                </div>
            </div>
        </div>


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


            $("#form-trailer").submit(function(e) {
                e.preventDefault();

                console.log('=====');

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

                $("#form-trailer").ajaxSubmit({
                    type : "POST",
                    url : "./ajax/book-trailer-save.php",
 						data : $(this).serialize(),
//                    data : $(this).serializeObject(),
                    dataType : "json",
                    success: function(xhr) {
                        hideLoader();
                        if ( xhr.code == "0" ) {
                            location.href = "book_trailer_list.php";
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