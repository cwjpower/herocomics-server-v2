<?php 
require_once '../../wps-config.php';

if ( empty($_GET['pid'] )) {
	lps_alert_back( '게시글의 아이디가 존재하지 않습니다.' );
}
$post_id = $_GET['pid'];

$post_row = wps_get_post( $post_id );
$post_type = $post_row['post_type'];
$post_label = $wps_post_type[$post_type];
$post_title = htmlspecialchars( $post_row['post_title'] );
$post_content = $post_row['post_content'];
$post_name = $post_row['post_name'];
$post_order = $post_row['post_order'];
$post_user_id = $post_row['post_user_id'];
$post_modified = $post_row['post_modified'];
$post_type_area = lps_get_value_by_key($post_row['post_type_area'], $wps_notice_coverage);

$user_rows = wps_get_user( $post_user_id );
$user_login = $user_rows['user_login'];
$user_name = $user_rows['user_name'];

$posts_meta = wps_get_post_meta($post_id);

$attachment = wps_get_meta_value_by_key($posts_meta, 'wps-post-attachment');

if (!empty($attachment)) {
	$attachment = unserialize($attachment);
	$attachment_link = '<a href="' . INC_URL . '/lib/download-post-attachment.php?pid=' . $post_id . '&key=0">'. $attachment[0]['file_name'] .'</a>'; 
} else {
	$attachment_link = '';
}

$post_view_count = empty($posts_meta['post_view_count']) ? 0 : number_format($posts_meta['post_view_count']);

require_once ADMIN_PATH . '/admin-header.php';

require_once './pages-lnb.php';
?>

			<!-- Content Wrapper. Contains page content -->
			<div class="content-wrapper">
				<!-- Content Header (Page header) -->
				<div class="content-header">
					<h1>
						상세보기
					</h1>
					<ol class="breadcrumb">
						<li><a href="<?php echo ADMIN_URL ?>/admin.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li><a href="<?php echo ADMIN_URL ?>/pages/">페이지 관리</a></li>
						<li><a href="<?php echo ADMIN_URL ?>/pages/"><?php echo $post_label ?></a></li>
						<li class="active"><b>공지글 확인</b></li>
					</ol>
				</div>

				<!-- Main content -->
				<div class="content body">
					
					<div class="box box-primary">
						<div class="box-header">
							<div class="pull-right">
								<a href="post_edit.php?pid=<?php echo $post_id ?>" class="btn btn-info btn-flat margin">수정</a>
								<button type="button" id="btn-notice-delete" class="btn btn-danger btn-flat">삭제</button>
							</div>
						</div>
						<div class="box-body">
							<table class="table table-bordered">
								<colgroup>
									<col style="width: 15%; min-width: 100px;">
									<col style="width: 35%;">
									<col style="width: 15%; min-width: 100px;">
									<col>
								</colgroup>
								<tbody>
									<tr>
										<th>제목</th>
										<td colspan="3"><?php echo $post_title ?></td>
									</tr>
									<tr>
										<th>작성자</th>
										<td><?php echo $post_name ?></td>
										<th>등록범위</th>
										<td><?php echo $post_type_area ?></td>
									</tr>
									<tr>
										<th>계정</th>
										<td><?php echo $user_login ?></td>
										<th>조회수</th>
										<td><?php echo number_format($post_view_count) ?></td>
									</tr>
									<tr>
										<th>작성일(수정일)</th>
										<td><?php echo $post_modified ?></td>
										<th>댓글 수</th>
										<td>?</td>
									</tr>
									<tr>
										<th>첨부</th>
										<td colspan="3"><?php echo $attachment_link ?></td>
									</tr>
									<tr>
										<th>내용</th>
										<td colspan="3"><?php echo $post_content ?></td>
									</tr>
								</tbody>
							</table>
						</div><!-- /.box-body -->
						<div class="box-footer text-center">
							<button id="cancel-btn" type="button" class="btn btn-success"><i class="fa fa-times"></i> 확인</button>
						</div><!-- /.box-footer -->
					</div><!-- /. box -->
				</div><!-- /.content -->
			</div><!-- /.content-wrapper -->
			
			<script src="<?php echo INC_URL ?>/js/ls-util.js"></script>
			<script src="<?php echo INC_URL ?>/js/jquery/jquery.oLoader.min.js"></script>
			
			<script>
			$(function() {
				$("#cancel-btn").click(function() {
					history.back();
				});

				// 삭제
				$("#btn-notice-delete").click(function() {
					if (!confirm("삭제하시겠습니까?")) {
						return;
					}

					showLoader();
					
					$.ajax({
						type : "POST",
						url : "./ajax/post-delete.php",
						data : {
							"id" : "<?php echo $post_id ?>"
						},
						dataType : "json",
						success : function(res) {
							hideLoader();
							if (res.code == "0") {
								location.href = "./";
							} else {
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