<?php
require_once '../../wps-config.php';
require_once FUNC_PATH . '/functions-fancytree.php';

if (wps_is_admin()) {
    require_once ADMIN_PATH . '/admin-header.php';
} else {
    require_once ADMIN_PATH . '/agent-header.php';
}

require_once './pages-lnb.php';

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <h1>
            브랜드 뉴스 등록
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo ADMIN_URL ?>/admin.php"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="<?php echo ADMIN_URL ?>/pages/">페이지 관리</a></li>
            <li><a href="<?php echo ADMIN_URL ?>/pages/news_list.php">뉴스관리</a></li>
            <li class="active"><b>글쓰기</b></li>
        </ol>
    </div>

    <!-- book tariler Form -->

    <div class="container-fluid">

        <div class="row">
            <div class="col-lg-8">
                <div class="panel panel-default" style="margin-top:3rem;">
                    <!-- Default panel contents -->
                    <div class="panel-heading"><!--<i class="fa fa-film"></i>--> 뉴스 등록 / 수정 </div>
                    <div class="panel-body">
                        <form enctype="multipart/form-data" id="form-news" method="POST">
                            <input type="hidden" name="is_new" value="Y">

                            <div class="form-group">
                                <label for="exampleInputEmail1">뉴스 제목</label>
                                <input type="text" class="form-control" id="news-title" name="news_title" placeholder="뉴스제목" required>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputPassword1">뉴스 소제목</label>
                                <input type="text" class="form-control" id="news-sub-title" name="news_sub_title" placeholder="뉴스 소제목" required>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="comics_brand" id="marvel" value="1" checked>
                                    마블
                                </label>
                                <label>
                                    <input type="radio" name="comics_brand" id="dc" value="2">
                                    디씨
                                </label>
                                <label>
                                    <input type="radio" name="comics_brand" id="image" value="3">
                                    이미지
                                </label>

                            </div>
                            <div class="form-group">
                                <label for="attachement-file">이미지</label>
                                <input type="file" name="attachment[]" id="attachement-file">

                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="open_yn" value="Y"> 프론트에 공개합니다.
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="main_view_yn" value="Y"> 메인에 노출 합니다.
                                </label>
                            </div>
                            <div class="form-group">
                                <label for="news-content">뉴스내용 </label>
                                <textarea id="news-content" name="news_content" class="form-control ckeditor_content" required></textarea>
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

                            <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> 등록합니다</button>

                            <a href="news_list.php" class="btn btn-default " style="margin-left:3rem">리스트로 가기</a>
                        </form>
                    </div>

                </div> <!--/. end of Search form -->

            </div>
        </div>

    </div>


</div> <!-- ./end of content wrapper -->

<script src="<?php echo INC_URL ?>/js/jquery-ui.min.js"></script>
<script src="<?php echo INC_URL ?>/js/ls-util.js"></script>
<script src="<?php echo INC_URL ?>/js/jquery/jquery.oLoader.min.js"></script>
<!-- InputMask -->
<script src="<?php echo ADMIN_URL ?>/js/jquery/input-mask/jquery.inputmask.js"></script>
<script src="<?php echo ADMIN_URL ?>/js/jquery/input-mask/jquery.inputmask.date.extensions.js"></script>
<script src="<?php echo ADMIN_URL ?>/js/jquery/input-mask/jquery.inputmask.extensions.js"></script>
<!-- Number -->
<script src="<?php echo INC_URL ?>/js/jquery/jquery.number.min.js"></script>
<!-- ISBN -->
<script src="<?php echo INC_URL ?>/js/jquery/jquery.numeric.hyphen.min.js"></script>
<!-- CkEditor -->
<script src="<?php echo INC_URL ?>/js/ckeditor-4.5.10/ckeditor.js"></script>
<script src="<?php echo INC_URL ?>/js/ckeditor-4.5.10/adapters/jquery.js"></script>
<!-- Form(file) -->
<script src="<?php echo INC_URL ?>/js/jquery/jquery.form.min.js"></script>



<script>
    // CK Editor init
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
            showLoader();
            //ajaxSubmit
            $("#form-news").ajaxSubmit({
                type : "POST",
                url : "./ajax/news-save.php",
                data : $(this).serialize(),
                dataType : "json",
                success : function(res) {
                    if (res.code == "0") {
                        location.href = "./news_list.php";
                    } else {
                        hideLoader();
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