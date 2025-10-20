<?php

require_once '../../wps-config.php';
require_once FUNC_PATH . '/functions-book.php';
require_once FUNC_PATH . '/functions-term.php';
require_once FUNC_PATH . '/functions-fancytree.php';

require_once INC_PATH . '/classes/WpsPaginator.php';

if (wps_is_admin()) {
    require_once ADMIN_PATH . '/admin-header.php';
} else {
    require_once ADMIN_PATH . '/agent-header.php';
}

$book_id = $_GET['book_id'];
$page = empty($_GET['page']) ? 1 : $_GET['page'];

$book_rows = lps_get_book($book_id);

$book_title = $book_rows['book_title'];
$author = $book_rows['author'];
$publisher = $book_rows['publisher'];
$isbn = $book_rows['isbn'];
$cover_img = $book_rows['cover_img'];

$sparam = [];
//$rows = lps_get_preview_list($book_id);
$query = "
	SELECT
		*
	FROM
		bt_books_preview
	WHERE
		book_id = $book_id
	ORDER BY
		preview_seq ASC
";

$paginator = new WpsPaginator($wdb, $page, 20);
$rows = $paginator->ls_init_pagination( $query, $sparam );
$total_count = $paginator->ls_get_total_rows();

require_once './books-lnb.php';

?>

<link rel="stylesheet" href="<?php echo INC_URL ?>/css/jquery-ui.css">

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <h1>
            미리보기 관리
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo ADMIN_URL ?>/admin.php"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="<?php ECHO ADMIN_URL ?>/books/">책 관리</a></li>
            <li class="active"><b>프리뷰 관리</b></li>
        </ol>
    </div> <!--./end of Header title -->


    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
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
                <hr>
            </div>
        </div> <!-- ./end of book info -->


        <div class="panel panel-default">
            <!-- Default panel contents -->
            <div class="panel-heading"> <i class="fa fa-cog" aria-hidden="true"></i> 미리보기 : 20페이지 내외의 미리보기 이미지들을 관리합니다. </div>
            <div class="panel-body">
                    <a href="book_preview_new.php?book_id=<?=$book_id ?>" class="btn btn-default">미리보기등록</a>
                <a href="index.php" class="btn btn-default">책 리스트</a>
<!--                        <i class="fa fa-youtube-play"></i> -->


            </div>

        </div> <!--/. end of Search form -->

        <div class="row" style="margin-top: 4rem">
            <?php
                if ( !empty($rows) ) {

                foreach ( $rows as $key => $val ) {

                $id= $val['id'];
                $preview_seq = $val['preview_seq'];
                $preview_img_url = $val['preview_img_url'];


                $created_dt = $val['created_dt'];

            ?>


                <div class="col-lg-2 col-md-2 col-sm-3 col-xs-6">
                    <div class="thumbnail ">
                        <img src="<?php echo $preview_img_url; ?>" tyle="width:120px;" alt="...">

                        <p style="text-align: center; margin-top:1rem;">
                            <span class="badge"><?php echo $preview_seq; ?></span>
                            <a href="#" class="btn btn-default btn-xs btn-preview-delete" data-id="<?php echo $id; ?>">삭제</a>
                        </p>
                    </div>
                </div>

            <?php
                }
            }
            ?>
        </div>


            </div>
        </div>

    </div> <!-- end of container fluid -->

</div><!-- /.content-wrapper -->

<script src="<?php echo INC_URL ?>/js/ls-util.js"></script>
<script src="<?php echo INC_URL ?>/js/jquery/jquery.oLoader.min.js"></script>
<script src="<?php echo INC_URL ?>/js/jquery-ui.min.js"></script>

<script>
    // 삭제
    $(".btn-preview-delete").click(function() {
        if (!confirm("삭제하시겠습니까?")) {
            return;
        }

        var preview_id = $(this).attr("data-id");
        showLoader();

        $.ajax({
            type : "POST",
            url : "./ajax/book-preview-del.php",
            data : {
                "id" : preview_id
            },
            dataType : "json",
            success : function(res) {
                hideLoader();
                if (res.code == "0") {
                    location.href = "./book_preview_list.php?book_id=" + <?php echo $book_id; ?>
                } else {
                    alert(res.msg);
                }
            }
        });
    });
</script>

<?php
require_once ADMIN_PATH . '/admin-footer.php';
?>
