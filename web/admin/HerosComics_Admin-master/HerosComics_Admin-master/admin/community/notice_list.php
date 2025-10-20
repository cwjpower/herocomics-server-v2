<?php
/*
 * 2016.10.26	softsyw
 * Desc : 커뮤니티 관리 > 공지사항
 * 
 */
require_once '../../wps-config.php';
require_once FUNC_PATH . '/functions-book.php';
require_once INC_PATH . '/classes/WpsPaginator.php';

$post_type = 'notice_new';

// page number
$page = empty($_GET['page']) ? 1 : $_GET['page'];

$ob = '';

// 정렬순서
if (empty($_GET['ob'])) {
	$orderby = 'p.post_modified DESC';
} else {
	$ob = $_GET['ob'];
	if (!strcmp($_GET['ob'], 'hit')) {
		$orderby = 'post_view_count DESC';
	} else {
		$orderby = 'p.post_modified DESC';
	}
}

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

if (wps_is_admin()) {
	$query = "
		SELECT
			p.ID,
			p.post_name,
			p.post_date,
			p.post_title,
			p.post_parent,
			p.post_status,
			p.post_user_id,
			p.post_email,
			p.post_order,
			p.post_type,
			p.post_type_secondary,
			p.post_type_area,
			p.post_modified,
			m.meta_value AS post_view_count
		FROM
			bt_posts AS p
		LEFT JOIN
			bt_posts_meta AS m
		ON
			p.ID = m.post_id AND
			m.meta_key = 'post_view_count'
		WHERE
			p.post_type = '$post_type' AND
			p.post_type_secondary = 'community'
			$sql
		ORDER BY
			$orderby
	";
} else {
	$user_id = wps_get_current_user_id();
	
	$query = "
		SELECT
			p.ID,
			p.post_name,
			p.post_date,
			p.post_title,
			p.post_parent,
			p.post_status,
			p.post_user_id,
			p.post_email,
			p.post_order,
			p.post_type,
			p.post_type_secondary,
			p.post_type_area,
			p.post_modified,
			m.meta_value AS post_view_count
		FROM
			bt_posts AS p
		LEFT JOIN
			bt_posts_meta AS m
		ON
			p.ID = m.post_id AND
			m.meta_key = 'post_view_count'
		WHERE
			p.post_user_id = '$user_id' AND
			p.post_type = '$post_type' AND
			p.post_type_secondary = 'community'
			$sql
		ORDER BY
			$orderby
	";
}
$paginator = new WpsPaginator($wdb, $page);
$rows = $paginator->ls_init_pagination( $query, $sparam );
$total_count = $paginator->ls_get_total_rows();

if (wps_is_admin()) {
	require_once ADMIN_PATH . '/admin-header.php';
} else {
	require_once ADMIN_PATH . '/agent-header.php';
}

require_once './community-lnb.php';
?>

			<link rel="stylesheet" href="<?php echo INC_URL ?>/css/jquery-ui.css">

			<!-- Content Wrapper. Contains page content -->
			<div class="content-wrapper">
				<!-- Content Header (Page header) -->
				<div class="content-header">
					<h1>
						<?php echo $wps_post_type[$post_type] ?> &nbsp;
						<a href="./notice_new.php?pt=notice_new" class="btn btn-info btn-sm">글쓰기</a>
					</h1>
					<ol class="breadcrumb">
						<li><a href="<?php echo ADMIN_URL ?>/admin.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li><a href="<?php ECHO ADMIN_URL ?>/community/">커뮤니티 관리</a></li>
						<li><a href="<?php ECHO ADMIN_URL ?>/community/">담벼락</a></li>
						<li class="active"><b>공지사항 관리</b></li>
					</ol>
				</div>

				<!-- Main content -->
				<div class="content body">
					<div class="box box-default">
						<div class="box-body">
							<form id="search-form" class="form-inline">
								<input type="hidden" name="status" value="<?php echo $sts ?>">
								<div class="row">
									<div class="col-sm-12 text-right">
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
								<!-- i class="fa fa-circle-o text-yellow"></i> Total: <b><?php echo number_format($total_count) ?></b> -->
								<div class="row">
									<div class="col-sm-3">
										<select name="ob" id="order-by" class="form-control">
											<option value="last" <?php echo strcmp($ob, 'last') ? '' : 'selected'; ?>>최근 작성일 순</option>
											<option value="hit" <?php echo strcmp($ob, 'hit') ? '' : 'selected'; ?>>조회수 순</option>
										</select>
									</div>
								</div>
							</div>
							<div class="box-body">
								<table class="table table-striped table-hover">
									<thead>
										<tr>
											<th><input type="checkbox" id="switch-all"></th>
											<th>#</th>
											<th>게시글 제목</th>
											<th>작성자</th>
											<th>작성일</th>
											<th>미리보기</th>
											<th>등록범위</th>
											<th>조회수</th>
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
								$post_type_secondary = $val['post_type_secondary'];
								$post_view_count = number_format($val['post_view_count']);
								$post_order = $val['post_order'];
								$post_status = $val['post_status'];
								
								if (!strcmp($post_status, 'all')) {
									$post_area = '전체';
								} else {
									$notice_books = wps_get_post_meta($post_id, 'wps_notice_books');
									$unserial = unserialize($notice_books);
									$book_rows = lps_get_book($unserial[0]);
									$book_title = @$book_rows['book_title'];
									$exclude_count = count($unserial) - 1;
									$post_area = $book_title . '외 ' . $exclude_count . '권';
								}
								
								if ( !empty($val['post_user_id']) ) {
									$user_data = wps_get_user_by( 'ID', $val['post_user_id'] );
									$post_name = $user_data['user_name'];
								}
								
								// 필독(Top) 여부
								$notice_label = empty($post_order) ? '' : '<span class="label label-warning">필독</span>';
								
						?>
										<tr>
											<td><input type="checkbox" class="post_list" name="post_list[]" value="<?php echo $post_id ?>"></td>
											<td><?php echo $list_no ?></td>
											<td><?php echo $notice_label ?> <a href="notice_view.php?pid=<?php echo $post_id ?>"><?php echo $post_title ?></a></td>
											<td><?php echo $post_name ?></td>
											<td><?php echo $post_date ?></td>
											<td><a href="javascript:;" class="preview-notice-<?php echo $post_id ?>">[보기]</a>
											<td><?php echo $post_area ?></td>
											<td><?php echo $post_view_count ?></td>
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
			
			<div class="dialog" id="dialog-preview" title="미리보기">
			
				<table class="table table-bordered">
					<colgroup>
						<col style="width: 20%; min-width: 80x;">
						<col>
					</colgroup>
					<tbody>
						<tr>
							<td class="active">제목</td>
							<td id="prv-title"></td>
						</tr>
						<tr>
							<td class="active">이름</td>
							<td id="prv-nickname"></td>
						</tr>
						<tr>
							<td class="active">첨부파일</td>
							<td id="prv-attachment"></td>
						</tr>
						<tr>
							<td class="active">본문</td>
							<td><div style="width: 690px; overflow-x: auto;" id="prv-content"></div></td>
						</tr>
					</tbody>
				</table>
			</div>
			<!-- /.dialog -->
			
			<script src="<?php echo INC_URL ?>/js/ls-util.js"></script>
			<script src="<?php echo INC_URL ?>/js/jquery/jquery.oLoader.min.js"></script>
			<script src="<?php echo INC_URL ?>/js/jquery-ui.min.js"></script>
			
			<script>
			$(function() {
				$("#switch-all").click(function() {
					var chk = $(this).prop("checked");
					$(".post_list").prop("checked", chk);
				});

				$("#dialog-preview").dialog({
					autoOpen: false,
					width: 900,
					height: 550,
					buttons: {
						"닫기": function() {
							$(this).dialog("close");
						}
					}
				});
				
				$('a[class*="preview-notice-"]').click(function(e) {
					e.preventDefault();
					var id = $(this).attr("class").replace(/\D/g, "");

					$.ajax({
						type : "POST",
						url : "../pages/ajax/notice-preview.php",
						data : {
							"id" : id
						},
						dataType : "json",
						success : function(res) {
							if (res.code == "0") {
								$("#prv-title").html(res.posts.post_title);
								$("#prv-nickname").html(res.posts.post_name);
								$("#prv-attachment").html(res.attachment);
								$("#prv-content").html(res.posts.post_content);
							} else {
								alert(res.msg);
							}
						}
					});

					$("#dialog-preview").dialog("open");
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
						url : "./ajax/notice-delete.php",
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

				// 정렬순
				$("#order-by").change(function() {
					location.href = "?ob=" + $(this).val();
				});
			});
			</script>

<?php 
require_once ADMIN_PATH . '/admin-footer.php';
?>