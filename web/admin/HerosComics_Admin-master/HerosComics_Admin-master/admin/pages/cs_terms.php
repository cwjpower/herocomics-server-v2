<?php
/*
 * 2016.10.13	softsyw
 * Desc : 이용약관/개인정보취급방침
 * 
 */
require_once '../../wps-config.php';
require_once INC_PATH . '/classes/WpsPaginator.php';

$post_type = 'terms_of_use';

// page number
$page = empty($_GET['page']) ? 1 : $_GET['page'];

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
	$sql .= " AND p.post_type_area LIKE ?";
	array_push( $sparam, '%'.$sts.'%' );
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
		ID,
		post_name,
		post_date,
		post_title,
		post_parent,
		post_status,
		post_user_id,
		post_email,
		post_order,
		post_type,
		post_type_secondary,
		post_type_area,
		post_modified
	FROM
		bt_posts
	WHERE
		post_type = 'terms_of_use' OR 
		post_type = 'terms_of_privacy' 
		$sql
	ORDER BY
		ID DESC
";
$paginator = new WpsPaginator($wdb, $page);
$rows = $paginator->ls_init_pagination( $query, $sparam );
$total_count = $paginator->ls_get_total_rows();

require_once ADMIN_PATH . '/admin-header.php';

require_once './pages-lnb.php';
?>

			<link rel="stylesheet" href="<?php echo INC_URL ?>/css/jquery-ui.css">

			<!-- Content Wrapper. Contains page content -->
			<div class="content-wrapper">
				<!-- Content Header (Page header) -->
				<div class="content-header">
					<h1>
						이용약관/개인정보취급방침 &nbsp;
						<small><button type="button" id="btn-new-item" class="btn btn-info">등록</button></small>
					</h1>
					<ol class="breadcrumb">
						<li><a href="<?php echo ADMIN_URL ?>/admin.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li><a href="<?php echo ADMIN_URL ?>/pages/cs_faq_list.php">페이지 관리</a></li>
						<li class="active"><b>이용약관/개인정보취급방침</b></li>
					</ol>
				</div>

				<!-- Main content -->
				<div class="content body">
					<form id="item-new-form" class="hide">
						<div class="box box-warning">
							<div class="box-header">
								<label>
									<span class="btn btn-info btn-sm">
										<input type="radio" name="post_type" value="terms_of_use" checked> 이용약관 
									</span>
								</label>
								<label>
									<span class="btn btn-info btn-sm">
										<input type="radio" name="post_type" value="terms_of_privacy"> 개인정보취급방침 
									</span>
								</label>
							</div><!-- /.box-header -->
							<div class="box-body pad">
								<textarea id="post_content" name="post_content" class="ckeditor_content"></textarea>
							</div>
							<div class="box-footer">
								<button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> 추가합니다</button>
							</div>
						</div><!-- /.box -->
					</form>

					<form id="post-list-form">	
						<div class="box box-primary">
							<div class="box-header">
								<i class="fa fa-circle-o text-yellow"></i> Total: <b><?php echo number_format($total_count) ?></b>
							</div>
							<div class="box-body">
								<table class="table table-striped table-hover">
									<thead>
										<tr>
											<th><input type="checkbox" id="switch-all"></th>
											<th>#</th>
											<th>제목</th>
											<th>분류</th>
											<th>작성자</th>
											<th>등록일</th>
										</tr>
									</thead>
									<tbody>
						<?php
						if ( !empty($rows) ) {
							$list_no = $page == 1 ? $total_count : $total_count - (($page - 1) * $paginator->rows_per_page);
		
							foreach ( $rows as $key => $val ) {
								$post_id = $val['ID'];
								$post_title = htmlspecialchars($val['post_title']);
								$post_date = $val['post_date'];
								$post_name = $val['post_name'];
								$post_type_secondary = $val['post_type_secondary'];
						?>
										<tr>
											<td><input type="checkbox" class="post_list" name="post_list[]" value="<?php echo $post_id ?>"></td>
											<td><?php echo $list_no ?></td>
											<td><?php echo $post_title ?></td>
											<td><?php echo $post_type_secondary ?></td>
											<td><?php echo $post_name ?></td>
											<td><?php echo $post_date ?></td>
										</tr>
						<?php
								$list_no--;
							}
						}
						?>
									</tbody>
								</table>
							</div>
							<div class="box-footer text-center">
								<div class="pull-left">
									<button type="button" id="btn-delete" class="btn btn-danger btn-sm">삭제</button>
								</div>
								<?php echo $paginator->ls_bootstrap_pagination_link(); ?>
							</div>
						</div><!-- /.box -->
					</form>

				</div><!-- /.content -->
			</div><!-- /.content-wrapper -->
			
			<script src="<?php echo INC_URL ?>/js/ls-util.js"></script>
			<script src="<?php echo INC_URL ?>/js/jquery/jquery.oLoader.min.js"></script>
			<script src="<?php echo INC_URL ?>/js/jquery-ui.min.js"></script>
			<!-- CkEditor -->
			<script src="<?php echo INC_URL ?>/js/ckeditor-4.5.10/ckeditor.js"></script>
			<script src="<?php echo INC_URL ?>/js/ckeditor-4.5.10/adapters/jquery.js"></script>
			<script>
			$(function() {
				$("#btn-new-item").click(function() {
					if($("#item-new-form").hasClass("hide")) {
						$("#item-new-form").removeClass("hide");
						$(this).html("등록취소");
					} else {
						$("#item-new-form").addClass("hide");
						$(this).html("등록");
					}
				});

				$("#item-new-form").submit(function(e) {
					e.preventDefault();

					if ( $.trim($("#post_content").val()) == "" ) {
						alert("내용을 입력해 주십시오.");
						return false;
					}
					
					showLoader();

					$.ajax({
						type : "POST",
						url : "./ajax/cs-terms-new.php",
						data : $(this).serialize(),
						dataType : "json",
						success : function(res) {
							hideLoader();
							if ( res.code == "0" ) {
								location.reload();
							} else {
								alert( res.msg );
							}
						}
					});
				});

				// 선택 삭제
				$("#btn-delete").click(function() {
					var chkLength = $(".post_list:checked").length;

					if (chkLength == 0) {
						alert("삭제할 게시글을 선택해 주십시오.");
						return;
					}

					showLoader();
					
					$.ajax({
						type : "POST",
						url : "./ajax/post-delete-selected.php",
						data : $("#post-list-form").serialize(),
						dataType : "json",
						success : function(res) {
							if (res.code == "0") {
								location.reload();
							} else {
								hideLoader();
								alert(res.msg);
							}
						}
					});
				});

				// CKEditor
				var config = {
						height: 100,
						extraPlugins: 'autogrow',
						autoGrow_bottomSpace: 100,
						toolbar:
						[
							['FontSize', 'TextColor', 'Bold', 'Italic', 'Underline', '-', 'NumberedList', 'BulletedList', 'Blockquote', 'Table', '-', 'Undo', 'Redo', '-', 'SelectAll'],
							['UIColor']
						]
				}; 
				$(".ckeditor_content").ckeditor(config);
			});
			</script>

<?php 
require_once ADMIN_PATH . '/admin-footer.php';
?>