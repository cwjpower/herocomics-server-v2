<?php 
require_once '../../wps-config.php';
require_once FUNC_PATH . '/functions-book.php';
require_once FUNC_PATH . '/functions-activity.php';

if ( empty($_GET['aid'] )) {
	lps_alert_back( '게시글의 아이디가 존재하지 않습니다.' );
}
$activity_id = $_GET['aid'];

// for activity
$act_rows = lps_get_activity($activity_id);

$is_deleted = $act_rows['is_deleted'];

$book_id = $act_rows['book_id'];
$book_rows = lps_get_book($book_id);
$book_title = $book_rows['book_title'];

$act_title = $act_rows['subject'];
$act_content = $act_rows['content'];
$act_userid = $act_rows['user_id'];
$created_dt = $act_rows['created_dt'];
$count_hit = $act_rows['count_hit'];
$count_like = $act_rows['count_like'];
$count_comment = $act_rows['count_comment'];

$user_rows = wps_get_user( $act_userid );
$user_login = $user_rows['user_login'];
$user_name = $user_rows['user_name'];

// for activity meta
$attachment = '';
$act_attach = lps_get_activity_meta($activity_id, 'wps-community-attachment');
if (!empty($act_attach)) {
	$unserial = unserialize($act_attach);
	if (!empty($unserial[0])) {
		foreach ($unserial as $key => $val) {
			$attachment .= '<p><a href="' . INC_URL . '/lib/download-community-attachment.php?aid=' . $activity_id . '&key=' . $key . '" data-ajax="false">' . $val['file_name'] . '</a></p>';
		}
	}
}
// var_dump($act_attach);
// for comment of activity
$act_comments =  lps_get_activity_comments( $activity_id );

if (wps_is_admin()) {
	require_once ADMIN_PATH . '/admin-header.php';
} else {
	require_once ADMIN_PATH . '/agent-header.php';
}

require_once './community-lnb.php';
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
						<li><a href="<?php ECHO ADMIN_URL ?>/community/">커뮤니티 관리</a></li>
						<li><a href="<?php ECHO ADMIN_URL ?>/community/">담벼락</a></li>
						<li class="active"><b><?php echo $book_title ?></b></li>
					</ol>
				</div>

				<!-- Main content -->
				<div class="content body">
					
					<div class="box box-primary">
						<!-- >div class="box-header">
							<div class="pull-right">
								<a href="post_edit.php?aid=<?php echo $activity_id ?>" class="btn btn-info btn-flat margin">수정</a>
								<button type="button" id="btn-notice-delete" class="btn btn-danger btn-flat">삭제</button>
							</div>
						</div> -->
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
										<td colspan="3"><?php echo $act_title ?></td>
									</tr>
									<tr>
										<th>작성자</th>
										<td><?php echo $user_name ?></td>
										<th>추천 수</th>
										<td><?php echo number_format($count_like) ?></td>
									</tr>
									<tr>
										<th>계정</th>
										<td><?php echo $user_login ?></td>
										<th>조회수</th>
										<td><?php echo number_format($count_hit) ?></td>
									</tr>
									<tr>
										<th>작성일(수정일)</th>
										<td><?php echo $created_dt ?></td>
										<th>댓글 수</th>
										<td><?php echo number_format($count_comment) ?></td>
									</tr>
									<tr>
										<th>첨부파일</th>
										<td colspan="3"><?php echo $attachment ?></td>
									</tr>
									<tr>
										<th>내용</th>
										<td colspan="3"><?php echo $act_content ?></td>
									</tr>
								</tbody>
							</table>
						
							<h3>댓글</h3>
							
							<form id="comment-list-form">
							<table class="table table-bordered">	
								<colgroup>
									<col style="width: 50px;">
									<col style="width: 150px;">
									<col>
									<col style="width: 80px;">
								</colgroup>
								<thead>
									<tr class="active">
										<th><input type="checkbox" id="switch-all"></th>
										<th>작성자</th>
										<th>내용</th>
										<th>삭제</th>
									</tr>
								</thead>
								<tbody>
					<?php
					if (!empty($act_comments)) {
						
						$banned_users = lps_get_banned_user_ids(wps_get_current_user_id());
						
						foreach ($act_comments as $key => $val) {
							$comment_id = $val['comment_id'];
							$com_user_id = $val['comment_user_id'];
							$com_user_level = $val['comment_user_level'];
							$created = $val['comment_date'];
							$content = nl2br($val['comment_content']);
							$author = $val['comment_author'];
					?>
					
									<tr>
										<td><input type="checkbox" class="comment_list" name="comment_list[]" value="<?php echo $comment_id ?>"></td>
										<td>
											<?php echo $author ?> <br>
											<?php echo $created ?>
										</td>
										<td><?php echo $content ?></td>
										<td><a href="#" class="btn btn-danger btn-sm btn-delete-cmt">삭제</a></td>
									</tr>
					<?php
						}
					}
					?>
							</table>
							</form>
						</div><!-- /.box-body -->
						<div class="box-footer text-center">
							<div class="pull-left">
								<button type="button" id="btn-delete" class="btn btn-danger btn-sm">삭제</button>
							</div>
						
							<button id="cancel-btn" type="button" class="btn btn-success"><i class="fa fa-times"></i> 확인</button>
						</div><!-- /.box-footer -->
					</div><!-- /. box -->
				</div><!-- /.content -->
			</div><!-- /.content-wrapper -->
			
			<script src="<?php echo INC_URL ?>/js/ls-util.js"></script>
			<script src="<?php echo INC_URL ?>/js/jquery/jquery.oLoader.min.js"></script>
			
			<script>
			$(function() {
				$("#switch-all").click(function() {
					var chk = $(this).prop("checked");
					$(".comment_list").prop("checked", chk);
				});

				// 선택 삭제
				$("#btn-delete").click(function() {
					var chkLength = $(".comment_list:checked").length;

					if (chkLength == 0) {
						alert("삭제할 댓글을 선택해 주십시오.");
						return;
					}

					if (!confirm("삭제하시겠습니까?")) {
						return;
					}

					showLoader();

					deleteComment();
				});

				// 삭제
				$(".btn-delete-cmt").click(function() {
					if (!confirm("삭제하시겠습니까?")) {
						return;
					}
					$(this).parent().parent().find(".comment_list").prop("checked", true);
					deleteComment();
				});
				
				$("#cancel-btn").click(function() {
					location.href = "./post_list.php?id=<?php echo $book_id ?>";
				});

				// 삭제 실행
				function deleteComment() {
					$.ajax({
						type : "POST",
						url : "./ajax/activity-comment-delete.php",
						data : $("#comment-list-form").serialize(),
						dataType : "json",
						success : function(res) {
							if (res.code == "0") {
								location.reload();
							} else {
								alert(res.msg);
							}
						}
					});
				}
				
			});
			</script>
<?php 
require_once ADMIN_PATH . '/admin-footer.php';
?>