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


// page number
$page = empty($_GET['page']) ? 1 : $_GET['page'];

$trailer_title = empty($_GET['trailer_title']) ? '' : $_GET['trailer_title'];

// search
$sts = empty($_GET['status']) ? '' : $_GET['status'];
$qa = empty($_GET['qa']) ? '' : trim($_GET['qa']);
$q = empty($_GET['q']) ? '' : trim($_GET['q']);
$sql = '';
$pph = '';
$sparam = [];

// Simple search
if ( empty($qa) ) {
    if ( !empty($q) ) {
        $sql = " AND ( p.post_content LIKE ? OR p.post_title LIKE ? OR p.post_name LIKE ? ) ";
        array_push( $sparam, '%'.$q.'%', '%'.$q.'%', '%'.$q.'%' );
    }
} else {
    if ( !empty($q) ) {
        if ( !strcmp($qa, 'isbn') ) {
            $sql = " AND $qa = ?";
            array_push( $sparam, $q );
        } else {
            $sql = " AND $qa LIKE ?";
            array_push( $sparam, '%'.$q.'%' );
        }
    }
}

if ($sts) {
    $sql .= " AND post_type_area LIKE ?";
    array_push( $sparam, '%'.$sts.'%' );
}

if ($trailer_title) {
    $sql .= " AND trailer_title LIKE ?";
    array_push( $sparam, '%'.$trailer_title.'%' );
}

// Positional placeholder ?
if ( !empty($sql) ) {
    $pph_count = substr_count($sql, '?');
    for ( $i = 0; $i < $pph_count; $i++ ) {
        $pph .= 's';
    }
}

if (!empty($pph)) {
    array_unshift($sparam, $pph);
}

$query = "
	SELECT
          *
	FROM
		bt_book_trailers
	WHERE
		1=1
		$sql
	ORDER BY
		created_dt DESC ";

$paginator = new WpsPaginator($wdb, $page);
$rows = $paginator->ls_init_pagination( $query, $sparam );

$total_count = $paginator->ls_get_total_rows();
$total_records = $paginator->ls_get_total_records();

require_once './books-lnb.php';
?>

<link rel="stylesheet" href="<?php echo INC_URL ?>/css/jquery-ui.css">

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <h1>
            북 트레일러 관리
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo ADMIN_URL ?>/admin.php"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="<?php ECHO ADMIN_URL ?>/books/">책 관리</a></li>
            <li class="active"><b>북 트레일러</b></li>
        </ol>
        <p style="margin-top:1rem;">
            <span>
                마블 / 디씨 / 이미지 코믹스와 관련된 북트레일러를 관리합니다. BOOKS 메뉴의 메인과 리스트에 노출됩니다.
            </span>
        </p>


    </div>

    <div class="container-fluid">

        <div class="panel panel-default" style="">
            <!-- Default panel contents -->
            <div class="panel-heading"> <i class="fa fa-cog" aria-hidden="true"></i> 북트레일러 관리</div>
            <div class="panel-body">
                <form class="form-inline" id="search-form">
                    <div class="form-group">
                        <label class="sr-only" for="exampleInputAmount">트레일러 제목</label>
                        <div class="input-group">
                            <div class="input-group-addon">트레일러 제목</div>
                            <input type="text" class="form-control" id="exampleInputAmount" name="trailer_title" placeholder="제목" style="width:300px;">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-default "><i class="fa fa-search"></i> 검색</button>
                    <button type="button" id="reset-btn" class="btn btn-default">초기화</button>
                    <div style="float:right; margin-right:2rem;">
                        <a href="book_trailer_new.php" class="btn btn-primary" style="margin-left:3rem">
                            <i class="fa fa-youtube-play"></i> 링크 등록</a>
                        <button type="button" id="btn-delete" class="btn btn-default "><i class="fa fa-minus-circle"></i> 트레일러 삭제</button>
                    </div>
                </form>
            </div>
        </div> <!--/. end of Search form -->

        <div class="panel panel-default" style="margin-top:3rem;">
            <div class="panel-body">
                <form id="trailer-list-form">
            <table class="table table-striped table-hover">
                <caption>Total : <?php echo number_format($total_records) ?> , 검색 : <?php echo number_format($total_count) ?> </caption>
                <thead>
                    <tr>
                        <th><input type="checkbox" id="switch-all"></th>
                        <th>#</th>
                        <th>브랜드</th>
                        <th>트레일러 제목</th>
                        <th>트레일러 url</th>
<!--                        <th>트레일러 설명</th>-->
                        <th>공개여부</th>
                        <th>등록일자</th>
                        <th>관리</th>
                    </tr>
                </thead>
                <tbody>
                        <?php
                            if ( !empty($rows) ) {
                            $list_no = $page == 1 ? $total_count : $total_count - (($page - 1) * $paginator->rows_per_page);

                            foreach ( $rows as $key => $val ) {

                                $trailer_id = $val['ID'];
                                $comics_brand = $val['comics_brand'];

                                $trailer_title = htmlspecialchars($val['trailer_title']);
                                $trailer_desc = $val['trailer_desc'];
                                $trailer_url = $val['trailer_url'];
                                $open_yn = $val['open_yn'];
                                $user_id = $val['user_id'];
                                $created_dt = $val['created_dt'];


                                if ( !empty($val['user_id']) ) {
                                    $user_data = wps_get_user_by( 'ID', $val['user_id'] );
                                    $post_name = $user_data['user_name'];
                                }
                        ?>

                    <tr>
                        <td><input type="checkbox" class="trailer_list" name="trailer_list[]" value="<?php echo $trailer_id ?>"></td>
                        <td style=" vertical-align: middle;"><?php echo $trailer_id; ?></td>
                        <td align="">
                            <div style="width:60px;" class="book-tag tag-<?=$wps_comics_brand_css[$comics_brand] ?>">
                                <?php echo $wps_comics_brand[$comics_brand] ?>
                            </div>
                        </td>
                        <td>
                            <a href="book_trailer_view.php?trailer_id=<?php echo $trailer_id ?>">
                                <?php echo $trailer_title; ?>
                            </a>
                         </td>

                        <td><?php echo $trailer_url; ?></td>
<!--                        <td>--><?php //echo $trailer_desc; ?><!--</td>-->
                        <td><?php echo $open_yn; ?></td>
                        <td><?php echo $created_dt; ?></td>
                        <td>
                            <a href="/admin/books/book_trailer_edit.php?trailer_id=<?php echo $trailer_id; ?>" class="btn btn-default btn-sm">수정</a>
                       </td>
                    </tr>
                    <?php
                            $list_no--;
                        }
                    }
                    ?>

                </tbody>
            </table>
                    </form>


                <div class="box-footer text-center">
                    <div class="pull-left">
                        <!--                            <button type="button" id="btn-delete" class="btn btn-danger btn-sm">삭제</button>-->
                    </div>
                    <?php echo $paginator->ls_bootstrap_pagination_link(); ?>
                </div>


            </div>
        </div>

    </div>

</div><!-- /.content-wrapper -->


<div class="container-fluid">

</div>

<!-- jQuery Form plugin -->
<script src="<?php echo INC_URL ?>/js/jquery/jquery.form.min.js"></script>
<script src="<?php echo INC_URL ?>/js/jquery/jquery.serializeObject.min.js"></script>

<script src="<?php echo INC_URL ?>/js/jquery-ui.min.js"></script>
<script src="<?php echo INC_URL ?>/js/ls-util.js"></script>
<script src="<?php echo INC_URL ?>/js/jquery/jquery.oLoader.min.js"></script>


<script>
    $(function() {
        $("#reset-btn").click(function() {
            $("#search-form :input").val("");
        });


        //select all
        $("#switch-all").click(function() {
            var chk = $(this).prop("checked");
            $(".trailer_list").prop("checked", chk);
        });

        // 선택한 뉴스를 모두 삭제한다.
        $("#btn-delete").click(function() {
            var chkLength = $(".trailer_list:checked").length;

            if (chkLength == 0) {
                alert("삭제할 뉴스를 선택해 주십시오.");
                return;
            }

            showLoader();

            $.ajax({
                type : "POST",
                url : "./ajax/book-trailer-delete.php",
                data : $("#trailer-list-form").serialize(),
                dataType : "json",
                success : function(res) {
                    if (res.code == "0") {
                        location.href = "book_trailer_list.php";
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
