<?php 
require_once '../../wps-config.php';

if ( empty($_GET['pid'] )) {
	lps_alert_back( '게시글의 아이디가 존재하지 않습니다.' );
}
$post_id = $_GET['pid'];

$post_row = wps_get_post_qnas( $post_id );
$post_type = $post_row['post_type'];

$post_title = htmlspecialchars( $post_row['post_title'] );
$post_content = $post_row['post_content'];

$post_ans_title = htmlspecialchars( $post_row['post_ans_title'] );
$post_ans_content = $post_row['post_ans_content'];

$post_user_id = $post_row['post_user_id'];
$post_date = $post_row['post_date'];

$user_rows = wps_get_user( $post_user_id );
$user_login = $user_rows['user_login'];
$user_name = $user_rows['user_name'];

require_once ADMIN_PATH . '/admin-header.php';

require_once './customer-lnb.php';
?>


			<!-- Content Wrapper. Contains page content -->
			<div class="content-wrapper">
				<!-- Content Header (Page header) -->
				<section class="content-header">
					<h1>
						도서신청 답변
					</h1>
				</section>

				<!-- Main content -->
				<section class="content">
				
					<div class="row">
						<div class="col-md-10">
							<form id="item-new-form">
								<input type="hidden" name="post_id" value="<?php echo $post_id ?>">
								
								<div class="box box-primary">
									<div class="box-body">
										<div class="form-group">
											<label>답변 제목</label>
											<input type="text" id="post_title" name="post_title" class="form-control" placeholder="제목" value="[답변] <?php echo $post_title ?>">
										</div>
										<div class="form-group">
											<label>답변 내용</label>
											<textarea id="post_content" name="post_content" class="form-control" style="height: 250px"><?php echo $post_ans_content ?></textarea>
										</div>
										<div class="callout callout-default">
											<div class="pull-right">
												<span class="badge bg-green"><a href="#"><?php echo $user_login ?>(<?php echo $user_name ?>)</a></span>
												<span class="description"><i class="fa fa-clock-o"></i> <?php echo $post_date ?></span>
											</div>
											<h4><?php echo $post_title ?></h4>
											<div style="font-size: 12px;"><?php echo nl2br($post_content) ?></div>
										</div>
									</div><!-- /.box-body -->
									<div class="box-footer">
										<div class="pull-right">
											<button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> 등록합니다</button>
										</div>
										<button id="cancel-btn" type="button" class="btn btn-default"><i class="fa fa-times"></i> 취소</button>
									</div><!-- /.box-footer -->
								</div><!-- /. box -->
							</form>
						</div><!-- /.col -->
					</div><!-- /.row -->
				</section><!-- /.content -->
			</div><!-- /.content-wrapper -->
			
			<!-- jQuery Form plugin -->
			<script src="<?php echo INC_URL ?>/js/jquery/jquery.form.min.js"></script>
			<script src="<?php echo INC_URL ?>/js/jquery/jquery.serializeObject.min.js"></script>
			
			<script src="<?php echo INC_URL ?>/js/jquery-ui.min.js"></script>
			<script src="<?php echo INC_URL ?>/js/ls-util.js"></script>
			<script src="<?php echo INC_URL ?>/js/jquery/jquery.oLoader.min.js"></script>
			
			<script>
			$(function() {
				$("#item-new-form").submit(function(e) {
					e.preventDefault();
	
					if ( $.trim($("#post_title").val()) == "" ) {
						alert("제목을 입력해 주십시오.");
						return false;
					}
					if ( $.trim($("#post_content").val()) == "" ) {
						alert("내용을 입력해 주십시오.");
						return false;
					}
	
					showLoader();
	
					$.ajax({
						type : "POST",
						url : "./ajax/qna-reply.php",
						data :  $(this).serialize(),
						dataType : "json",
						success : function(res) {
							hideLoader();
							if ( res.code == "0" ) {
								location.href = "ask_book_list.php";
							} else {
								alert( res.msg );
							}
						}
					});
				});
	
				$("#post_content").ckeditor({});

				$("#cancel-btn").click(function() {
					history.back();
				});
	
			});
			</script>

<?php 
require_once ADMIN_PATH . '/admin-footer.php';
?>