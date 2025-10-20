<?php
require_once '../../wps-config.php';
require_once FUNC_PATH . '/functions-fancytree.php';

// 책 카테고리
$book_cate_first = wps_fancytree_root_node_by_name('wps_category_books');

if (wps_is_admin()) {
    require_once ADMIN_PATH . '/admin-header.php';
} else {
    require_once ADMIN_PATH . '/agent-header.php';
}

require_once './books-lnb.php';

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <h1>
            북 트레일러 등록
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo ADMIN_URL ?>/admin.php"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="<?php ECHO ADMIN_URL ?>/books/">책 관리</a></li>
            <li class="active"><b>북 트레일러</b></li>
        </ol>
    </div>

    <!-- book tariler Form -->

    <div class="container-fluid">

        <div class="row">
            <div class="col-lg-6">
                <div class="panel panel-default" style="margin-top:3rem;">
                    <!-- Default panel contents -->
                    <div class="panel-heading"><i class="fa fa-film"></i> 북트레일러 등록 </div>
                    <div class="panel-body">
                        <form id="form-trailer" name="form-trailer" action="" method="POST">
                            <input type="hidden" name="is_new" value="Y">
                            <div class="form-group">
                                <label for="exampleInputEmail1">북트레일러 URL</label>
                                <input name="trailer_url" type="text" class="form-control" id="exampleInputEmail1" placeholder="URL">
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
                                <label for="exampleInputPassword1">북트레일러 제목</label>
                                <input name="trailer_title" type="text" class="form-control" id="exampleInputPassword1" placeholder="제목">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputPassword1">북트레일러 내용 설명 </label>
                                <textarea name = "trailer_desc" class="form-control" rows="7"></textarea>
                            </div>
<!--                            <div class="form-group">-->
<!--                                <label for="exampleInputFile">이미지</label>-->
<!--                                <input type="file" id="exampleInputFile">-->
<!--                                <p class="help-block"> 리스트에 노출되는 이미지 입니다.</p>-->
<!--                            </div>-->
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="open_yn" value="Y"> 프론트에 공개합니다.
                                </label>
                            </div>
                            <hr>
<!--                            <div class="form-group">-->
<!--                                <label for="exampleInputPassword1">등록일자</label>-->
<!--                                <input type="text" class="form-control" id="exampleInputPassword1" placeholder="yyyy-mm-dd">-->
<!--                            </div>-->
<!--                            <div class="form-group">-->
<!--                                <label for="exampleInputPassword1">수정일자</label>-->
<!--                                <input type="text" class="form-control" id="exampleInputPassword1" placeholder="yyyy-mm-dd">-->
<!--                            </div>-->
                            <hr>
                            <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> 등록합니다</button>
                            <a href="book_trailer_list.php" class="btn btn-default" style="margin-left:1rem">리스트</a>
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
    $(function(){
        $("#form-trailer").submit(function(e) {
            e.preventDefault();

            //alert("test");
            showLoader();

            //ajaxSubmit
            $("#form-trailer").ajaxSubmit({
                type : "POST",
                url : "./ajax/book-trailer-save.php",
                data : $(this).serialize(),
                //data : $(this).serializeObject(),
                dataType : "json",
                success : function(res) {
                    if (res.code == "0") {
                        location.href = "book_trailer_list.php";
                    } else {
                        //hideLoader();
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






