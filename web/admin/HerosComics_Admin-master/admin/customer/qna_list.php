<?php
/*
 * 2016.12.21	softsyw
 * Desc : 1:1 문의
 * 
 */
require_once '../../wps-config.php';
require_once FUNC_PATH . '/functions-term.php';
require_once INC_PATH . '/classes/WpsPaginator.php';

// 질문유형
$faq_type_groups = wps_get_term_by_taxonomy('wps_category_faq');

$post_type = 'qna_new';

// page number
$page = empty($_GET['page']) ? 1 : $_GET['page'];

// search
$sts = empty($_GET['status']) ? '' : $_GET['status'];
$qa = empty($_GET['qa']) ? '' : trim($_GET['qa']);
$q = empty($_GET['q']) ? '' : trim($_GET['q']);
$sql = '';
$pph = '';
$sparam = [];

if ( !empty($q) ) {
	$sql = " AND ( post_content LIKE ? OR post_title LIKE ? ) ";
	array_push( $sparam, '%' . $q . '%', '%' . $q . '%' );
}

if ($sts) {
	$sql .= " AND post_term_id = ?";
	array_push( $sparam, $sts );
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
			bt_posts_qnas AS qa
		LEFT JOIN
			bt_terms AS t
		ON
			qa.post_term_id = t.term_id
		WHERE
			post_type = '$post_type'
			$sql
		ORDER BY
			ID DESC
";
$paginator = new WpsPaginator($wdb, $page);
$rows = $paginator->ls_init_pagination( $query, $sparam );
$total_count = $paginator->ls_get_total_rows();

require_once ADMIN_PATH . '/admin-header.php';

require_once './customer-lnb.php';
?>

			<link rel="stylesheet" href="<?php echo INC_URL ?>/css/jquery-ui.css">

			<!-- Content Wrapper. Contains page content -->
			<div class="content-wrapper">
				<!-- Content Header (Page header) -->
				<div class="content-header">
					<h1>
						1:1 문의
					</h1>
					<ol class="breadcrumb">
						<li><a href="<?php echo ADMIN_URL ?>/admin.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li>문의관리</li>
						<li class="active"><b>1:1 문의</b></li>
					</ol>
				</div>

				<!-- Main content -->
				<div class="content body">
					<div class="box box-default">
						<div class="box-body">
							<form id="search-form" class="form-inline">
								<div class="row">
									<div class="col-sm-6">
										<div class="form-group">
											<a href="<?php echo $_SERVER['PHP_SELF'] ?>" class="btn <?php echo empty($sts) ? 'btn-success' : 'btn-info'; ?> btn-flat">전체</a>
							<?php 
							if (!empty($faq_type_groups)) {
								foreach ($faq_type_groups as $key => $val) {
									$term_id = $val['term_id'];
									$tname = $val['name'];
							?>
											<a href="?status=<?php echo $term_id ?>" class="btn <?php echo $sts == $term_id ? 'btn-success' : 'btn-info'; ?> btn-flat"><?php echo $tname ?></a>
							<?php
								}
							}
							?>
										</div>
									</div>
									<div class="col-sm-6 text-right">
										<div class="form-group">
											<select name="qa" class="form-control">
												<optgroup label="키워드 검색">
													<option value="">-전체-</option>
													<option value="post_title" <?php echo strcmp($qa, 'post_title') ? '' : 'selected'; ?>>제목</option>
													<option value="post_content" <?php echo strcmp($qa, 'post_content') ? '' : 'selected'; ?>>내용</option>
												</optgroup>
											</select>
										</div>
										<div class="form-group">
											<div class="input-group input-group-sm">
												<input type="text" name="q" value="<?php echo $q ?>" class="form-control">
												<span class="input-group-btn">
													<button type="submit" class="btn btn-primary btn-flat">검색</button>
												</span>
											</div>
										</div>
									</div>
									<div class="col-sm-2"></div>
								</div><!-- /.row -->
							</form>
						</div><!-- /.box -->
					</div>

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
											<th>첨부파일</th>
											<th>작성자</th>
											<th>등록날짜</th>
											<th>답변날짜</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
						<?php
						if ( !empty($rows) ) {
							$list_no = $page == 1 ? $total_count : $total_count - (($page - 1) * $paginator->rows_per_page);
	
							foreach ( $rows as $key => $val ) {
								$post_id = $val['ID'];
								$post_name = $val['post_name'];
								$post_title = htmlspecialchars($val['post_title']);
								$post_date = $val['post_date'];
								$post_ans_date = $val['post_ans_date'];
								$post_status = $val['post_status'];
								$term_name = $val['name'];
								
								$attachment = @$val['post_attachment'];
								$attach_icon = empty($attachment) ? '' : '<i class="fa fa-fw fa-download"></i>';
								
								if ( !strcmp($post_status, 'waiting') ) {
									$reply_icon = '<span class="label label-warning">대기중</span>';
								} else {
									$reply_icon = '<span class="label label-success">답변완료</span>';
								}
						?>
										<tr id="post-id-<?php echo $post_id ?>">
											<td><input type="checkbox" class="post_list" name="post_list[]" value="<?php echo $post_id ?>"></td>
											<td><?php echo $list_no ?></td>
											<td>
												<span class="label label-default"><?php echo $term_name ?></span> 
												<?php echo $post_title ?> 
											</td>
											<td><?php echo $attach_icon ?></td>
											<td><?php echo $post_name ?></td>
											<td><?php echo $post_date ?></td>
											<td><?php echo $post_ans_date ?></td>
											<td>
												<?php echo $reply_icon ?>
												<a href="qna_reply.php?pid=<?php echo $post_id ?>" class="btn btn-primary btn-xs"><i class="fa fa-fw fa-edit"></i> 답변</a> &nbsp;
											</td>
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
			
			<script>
			$(function() {
				$("#switch-all").click(function() {
					var chk = $(this).prop("checked");
					$(".post_list").prop("checked", chk);
				});

				// 선택 삭제
				$("#btn-delete").click(function() {
					var chkLength = $(".post_list:checked").length;

					if (chkLength == 0) {
						alert("삭제할 문의를 선택해 주십시오.");
						return;
					}

					showLoader();
					
					$.ajax({
						type : "POST",
						url : "./ajax/qna-delete-selected.php",
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
			});
			</script>

<?php 
require_once ADMIN_PATH . '/admin-footer.php';
?>