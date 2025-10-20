<?php
require_once '../../wps-config.php';
require_once FUNC_PATH . '/functions-fancytree.php';
require_once FUNC_PATH . '/functions-book.php';

// 책 카테고리
$book_cate_first = wps_fancytree_root_node_by_name('wps_category_books');

if (wps_is_admin()) {
    require_once ADMIN_PATH . '/admin-header.php';
} else {
    require_once ADMIN_PATH . '/agent-header.php';
}

$book_id = $_GET['book_id'];

$book_rows = lps_get_book($book_id);

$book_title = $book_rows['book_title'];
$author = $book_rows['author'];
$publisher = $book_rows['publisher'];
$isbn = $book_rows['isbn'];
$cover_img = $book_rows['cover_img'];

require_once './books-lnb.php';

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <h1>
            북 프리뷰 등록
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo ADMIN_URL ?>/admin.php"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="<?php ECHO ADMIN_URL ?>/books/">책 관리</a></li>
            <li class="active"><b>북 트레일러</b></li>
        </ol>
    </div>

    <!-- book tariler Form -->

    <div class="container-fluid">

        <div class="row" style="margin-top:1rem;">
            <div class="col-lg-6">
                <h4><i class="fa fa-book"></i> 책 정보</h4>
                <div class="media well">
                    <div class="media-left">
                        <a href="#">
                            <img class="media-object" src="<?php echo $cover_img ?>" alt="cover img" style="height:100px;">
                        </a>
                    </div>
                    <div class="media-body">
                        <h4 class="media-heading"><?php echo $book_title;?></h4>
                        <?php echo $author;?><br>
                        <?php echo $publisher;?><br>
                        <?php echo $isbn;?>
                    </div>
                </div>

            </div>
        </div> <!-- ./end of book info -->

        <div class="row">
            <div class="col-lg-6">

                <div>
                    <li>전자책 상세 정보에 노출되는 미리보기 파일을 등록합니다.</li>
                    <li>미리보기는 10페이지 내외의 이미지 파일을 등록합니다.</li>
                </div>

                <div class="panel panel-default" style="margin-top:3rem;">

                    <!-- Default panel contents -->
                    <div class="panel-heading">미리보기 등록</div>
                    <div class="panel-body">
                        <form enctype="multipart/form-data" id="form-preview" method="POST">
                            <input type="hidden" name = "book_id" value="<?php echo $book_id; ?>">
                            <div class="form-group">
                                <label for="exampleInputFile">미리보기 이미지</label>
                                <input type="file" name="preview-img[]" id="preview-img">
                                <p class="help-block"> 리스트에 노출되는 이미지 입니다.</p>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">미리보기 페이지</label>
                                <input type="text" class="form-control" name="preview_seq" id="preview-seq" placeholder="페이지 번호">
                            </div>

<!--                            <div class="checkbox">-->
<!--                                <label>-->
<!--                                    <input type="checkbox" name="open_yn"> 프론트에 공개합니다.-->
<!--                                </label>-->
<!--                            </div>-->
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
                            <button type="submit" class="btn btn-default">저장</button>
<!--                            <button type="submit" class="btn btn-default">수정</button>-->
<!--                            <button type="submit" class="btn btn-default">삭제</button>-->
                            <a href="book_preview_list.php?book_id=<?=$book_id?>" class="btn btn-primary" style="margin-left:3rem">리스트</a>
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
<!-- Form(file) -->
<script src="<?php echo INC_URL ?>/js/jquery/jquery.form.min.js"></script>


<script>
    $('#preview-img').change(function(e){
        filename = e.target.files[0].name;
        var page_no =filename.split('.')[0];
        // 파일명을 seq 로 사용한다.
        $('#preview-seq').val(page_no);
    });

    // save preview info
    $("#form-preview").submit(function(e) {
        e.preventDefault();
        showLoader();

        var form = $(this);
        console.log($('#preview-img'));

        //ajaxSubmit
        $("#form-preview").ajaxSubmit({
        //$.ajax({
            type : "POST",
            url : "./ajax/book-preview-save.php",
            data : form.serialize(),
            dataType : "json",
            success : function(res) {
                if (res.code == "0") {
                    location.href = "./book_preview_list.php?book_id="+<?php echo $book_id ?>;
                } else {
                    hideLoader();
                    alert(res.msg);
                }
            }
        });
    });
</script>


<?php
require_once ADMIN_PATH . '/admin-footer.php';
?>






