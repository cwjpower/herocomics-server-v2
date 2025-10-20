<?php
/*
 * 2016.10.26	softsyw
 * Desc : 커뮤니티 > 담벼락(게시글)
 * 
 *   bt_activity 테이블 사용함. 유의할 것.
 * 
 */
require_once '../../wps-config.php';
require_once FUNC_PATH . '/functions-book.php';
require_once FUNC_PATH . '/functions-activity.php';
require_once INC_PATH . '/classes/WpsPaginator.php';

if ( empty($_GET['id'] )) {
	lps_alert_back( '책 아이디가 존재하지 않습니다.' );
}
$book_id = $_GET['id'];

$book_rows = lps_get_book($book_id);
$book_title = $book_rows['book_title'];

$ob = '';

// 정렬순서
if (empty($_GET['ob'])) {
	$orderby = 'a.id DESC';
} else {
	$ob = $_GET['ob'];
	if (!strcmp($ob, 'hit')) {
		$orderby = 'a.count_hit DESC';
	} else if (!strcmp($ob, 'rec')) {
		$orderby = 'a.count_like DESC';
	} else if (!strcmp($ob, 'com')) {
		$orderby = 'a.count_comment DESC';
	} else {
		$orderby = 'a.id DESC';
	}
}

// page number
$page = empty($_GET['page']) ? 1 : $_GET['page'];

// search
$sts = empty($_GET['status']) ? '0' : $_GET['status'];
$qa = empty($_GET['qa']) ? '' : trim($_GET['qa']);
$q = !isset($_GET['q']) ? '' : trim($_GET['q']);
$sql = '';
$pph = '';
$sparam = [];

// Simple search
if ( !empty($q) && !empty($qa) ) {
	if ( !strcmp($qa, 'user_name') ) {
		$sql = " AND u.user_name LIKE ?";
		array_push( $sparam, '%' . $q . '%' );
	} else if ( !strcmp($qa, 'subject') ) {
		$sql = " AND a.subject LIKE ?";
		array_push( $sparam, '%' . $q . '%' );
	} else {	// all
		$sql = " AND a.subject LIKE ? OR a.content LIKE ? ";
		array_push( $sparam, '%' . $q . '%', '%' . $q . '%' );
	}
}

if ($sts) {
	$sql .= " AND a.is_deleted = ?";
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
		bt_activity AS a
	LEFT JOIN
		bt_users AS u
	ON
		a.user_id = u.ID
	WHERE
		a.component = 'activity' AND
		a.type = 'activity_update' AND
		a.book_id = '$book_id'
		$sql
	ORDER BY
		$orderby
";

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
						<?php echo $book_title ?> 담벼락 &nbsp;
					</h1>
					<ol class="breadcrumb">
						<li><a href="<?php echo ADMIN_URL ?>/admin.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li><a href="<?php ECHO ADMIN_URL ?>/community/">커뮤니티 관리</a></li>
						<li><a href="<?php ECHO ADMIN_URL ?>/community/">담벼락</a></li>
						<li class="active"><b><?php echo $book_title ?></b></li>
					</ol>
				</div>

				<!-- Main content -->
				<div class="content body">
					<div class="box box-default">
						<div class="box-body">
							<form id="search-form" class="form-inline">
								<input type="hidden" name="id" value="<?php echo $book_id ?>">
								<input type="hidden" name="status" value="<?php echo $sts ?>">
								<div class="row">
									<div class="col-sm-6">
										<div class="form-group">
											<a href="?status=0&id=<?php echo $book_id ?>" class="btn <?php echo empty($sts) ? 'btn-success' : 'btn-info'; ?> btn-flat">전체보기</a>
											<a href="?status=1&id=<?php echo $book_id ?>" class="btn <?php echo $sts == '1' ? 'btn-success' : 'btn-info'; ?> btn-flat">삭제글만 보기</a>
										</div>
									</div>
									<div class="col-sm-6 text-right">
										<div class="form-group">
											<select name="qa" class="form-control">
												<optgroup label="키워드 검색">
													<option value="subject" <?php echo strcmp($qa, 'subject') ? '' : 'selected'; ?>>제목</option>
													<option value="user_name" <?php echo strcmp($qa, 'user_name') ? '' : 'selected'; ?>>작성자</option>
													<option value="all" <?php echo strcmp($qa, 'all') ? '' : 'selected'; ?>>제목 + 내용</option>
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

					<form id="activity-list-form">
						<input type="hidden" name="id" value="<?php echo $book_id ?>">	
						<div class="box box-primary">
							<div class="box-header">
								<!-- i class="fa fa-circle-o text-yellow"></i> Total: <b><?php echo number_format($total_count) ?></b> -->
								<div class="row">
									<div class="col-sm-3">
										<select name="ob" id="order-by" class="form-control">
											<option value="last" <?php echo strcmp($ob, 'last') ? '' : 'selected'; ?>>최근 작성일 순</option>
											<option value="hit" <?php echo strcmp($ob, 'hit') ? '' : 'selected'; ?>>조회수 순</option>
											<option value="rec" <?php echo strcmp($ob, 'rec') ? '' : 'selected'; ?>>추천 순</option>
											<option value="com" <?php echo strcmp($ob, 'com') ? '' : 'selected'; ?>>댓글 순</option>
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
											<th>조회수</th>
											<th>추천수</th>
											<th>댓글수</th>
											<th>상태</th>
										</tr>
									</thead>
									<tbody>
						<?php 
						// 담벼락 게시글
						if (!empty($rows)) {
							$list_no = $page == 1 ? $total_count : $total_count - (($page - 1) * $paginator->rows_per_page);
							
							foreach ($rows as $key => $val) {
								$act_id = $val['id'];
								$user_id = $val['user_id'];
								$subject = $val['subject'];
								$created = substr($val['created_dt'], 0, 10);
								$count_hit = $val['count_hit'];
								$count_like = $val['count_like'];
								$count_comment = $val['count_comment'];
								$is_deleted = $val['is_deleted'];
								
								$user_rows = wps_get_user($user_id);
								$user_name = $user_rows['user_name'];
								$user_level = $user_rows['user_level'];
								
								$status = $is_deleted ? '<span class="label label-danger">삭제</label>' : '<span class="label label-success">게시중</label>';

								$act_attach = lps_get_activity_meta($act_id, 'wps-community-attachment');
								$attach_file = empty($act_attach) ? '' : '<i class="fa fa-fw fa-paperclip"></i>';
						?>
										<tr>
											<td><input type="checkbox" class="activity_list" name="activity_list[]" value="<?php echo $act_id ?>"></td>
											<td><?php echo $list_no ?></td>
											<td>
												<a href="activity_view.php?aid=<?php echo $act_id ?>"><?php echo $subject ?></a>
												<?php echo $attach_file ?>
											</td>
											<td><?php echo $user_name ?></td>
											<td><?php echo $created ?></td>
											<td><a href="javascript:;" class="preview-activity-<?php echo $act_id ?>">[보기]</a>
											<td><?php echo number_format($count_hit) ?></td>
											<td><?php echo number_format($count_like) ?></td>
											<td><?php echo number_format($count_comment) ?></td>
											<td><?php echo $status ?></td>
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
					$(".activity_list").prop("checked", chk);
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

				// 미리보기
				$('a[class*="preview-activity-"]').click(function(e) {
					e.preventDefault();
					var id = $(this).attr("class").replace(/\D/g, "");

					$.ajax({
						type : "POST",
						url : "./ajax/activity-preview.php",
						data : {
							"id" : id
						},
						dataType : "json",
						success : function(res) {
							if (res.code == "0") {
								$("#prv-title").html(res.activity.subject);
								$("#prv-nickname").html(res.user_name);
								$("#prv-attachment").html(res.attachment);
								$("#prv-content").html(res.activity.content);
							} else {
								alert(res.msg);
							}
						}
					});

					$("#dialog-preview").dialog("open");
				});

				// 선택 삭제
				$("#btn-delete").click(function() {
					var chkLength = $(".activity_list:checked").length;

					if (chkLength == 0) {
						alert("삭제할 게시글을 선택해 주십시오.");
						return;
					}

					if (!confirm("삭제하시겠습니까?")) {
						return;
					}

					showLoader();
					
					$.ajax({
						type : "POST",
						url : "./ajax/activity-delete.php",
						data : $("#activity-list-form").serialize(),
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
					$("#activity-list-form").submit();
				});
			});
			</script>

<?php 
require_once ADMIN_PATH . '/admin-footer.php';
?>