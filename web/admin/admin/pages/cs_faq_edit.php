<?php 
require_once '../../wps-config.php';
require_once FUNC_PATH . '/functions-term.php';

if ( empty($_GET['pid'] )) {
	lps_alert_back( '게시글의 아이디가 존재하지 않습니다.' );
}
$post_id = $_GET['pid'];

$post_row = wps_get_post( $post_id );
$post_type = $post_row['post_type'];
$post_label = $wps_post_type[$post_type];
$post_title = htmlspecialchars( $post_row['post_title'] );
$post_content = $post_row['post_content'];
$post_order = $post_row['post_order'];
$post_type_secondary = $post_row['post_type_secondary'];
$post_user_id = $post_row['post_user_id'];
$post_modified = $post_row['post_modified'];
$post_type_area = explode(',', $post_row['post_type_area']);

$user_rows = wps_get_user( $post_user_id );
$user_login = $user_rows['user_login'];
$user_name = $user_rows['user_name'];

$attachment = unserialize(wps_get_post_meta( $post_id, 'wps-post-attachment' ));

// 질문유형
$faq_type_groups = wps_get_term_by_taxonomy('wps_category_faq');

require_once ADMIN_PATH . '/admin-header.php';

require_once './pages-lnb.php';
?>

			<link rel="stylesheet" href="<?php echo INC_URL ?>/css/jquery-ui.css">
			
			<!-- Content Wrapper. Contains page content -->
			<div class="content-wrapper">
				<!-- Content Header (Page header) -->
				<div class="content-header">
					<h1>
						<?php echo $post_label ?> 수정
					</h1>
					<ol class="breadcrumb">
						<li><a href="<?php echo ADMIN_URL ?>/admin.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li><a href="<?php echo ADMIN_URL ?>/pages/">페이지 관리</a></li>
						<li><a href="<?php echo ADMIN_URL ?>/pages/"><?php echo $post_label ?></a></li>
						<li class="active"><b>수정</b></li>
					</ol>
				</div>

				<!-- Main content -->
				<div class="content body">
					<form id="item-new-form" method="post" enctype="multipart/form-data">
						<input type="hidden" name="ID" id="post_id" value="<?php echo $post_id ?>">
						<input type="hidden" name="post_type" value="<?php echo $post_type ?>">
						<input type="hidden" name="post_type_area[]" value="web">
					
						<div class="box box-primary">
							<div class="box-body">
								<div class="form-group">
									<label>
										<input type="checkbox" id="post_order" name="post_order" value="1" <?php echo empty($post_order) ? '' : 'checked'; ?>> 게시글을 상단에 노출합니다.
									</label>
								</div>
								<div class="form-group">
									<label>제목</label>
									<input type="text" id="post_title" name="post_title" class="form-control" placeholder="제목" required value="<?php echo $post_title ?>">
								</div>
								<div class="form-group">
									<label>질문유형</label>
									
									<select name="post_type_secondary" class="form-control">
										<option value="">-선택-</option>

						<?php 
						if (!empty($faq_type_groups)) {
							foreach ($faq_type_groups as $key => $val) {
								$term_id = $val['term_id'];
								$tname = $val['name'];
								$selected = strcmp($tname, $post_type_secondary) ? '' : 'selected'; 
						?>
										<option value="<?php echo $tname ?>" <?php echo $selected ?>><?php echo $tname ?></option>
						<?php
							}
						}
						?>
									</select>
								</div>
								<div class="form-group">
									<label>첨부파일</label>
						<?php 
						if ( !empty($attachment) ) {
							echo '<ul class="list-group" id="uploaded-file-lists">';
							foreach ( $attachment as $key => $val ) {
								$fname = $val['file_name'];
								$fsize = $val['file_size'];
						?>
									<li class="list-group-item">
										<a class="badge bg-red" id="delete-uploaded-file-<?php echo $key ?>">삭제</a>
										<span class="glyphicon glyphicon-download-alt"></span> &nbsp;
										<a href="<?php echo INC_URL ?>/lib/download-post-attachment.php?pid=<?php echo $post_id ?>&key=<?php echo $key ?>"> 
											<?php echo $fname ?>
											<span class="label label-success"><?php echo wps_format_bytes($fsize) ?></span>
										</a>
									</li>
						<?php
							}
							echo '</ul>';
						}
						?>
									<input type="file" id="attachment" name="attachment[]" class="form-control">
								</div>
								<div class="form-group">
									<label>내용</label>
									<textarea id="post_content" name="post_content" class="form-control"><?php echo $post_content ?></textarea>
								</div>
							</div><!-- /.box-body -->
							<div class="box-footer">
								<div class="pull-right">
									<button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> 적용합니다</button>
								</div>
								<a href="post_view.php?pid=<?php echo $post_id ?>" class="btn btn-default"><i class="fa fa-times"></i> 취소</a>
							</div><!-- /.box-footer -->
						</div><!-- /. box -->
					</form>	
				</div><!-- /.content -->
			</div><!-- /.content-wrapper -->
			
			<!-- jQuery Form plugin -->
			<script src="<?php echo INC_URL ?>/js/jquery/jquery.form.min.js"></script>
			<script src="<?php echo INC_URL ?>/js/jquery/jquery.serializeObject.min.js"></script>
			
			<script src="<?php echo INC_URL ?>/js/jquery-ui.min.js"></script>
			<script src="<?php echo INC_URL ?>/js/ls-util.js"></script>
			<script src="<?php echo INC_URL ?>/js/jquery/jquery.oLoader.min.js"></script>
			<!-- CkEditor -->
			<script src="<?php echo INC_URL ?>/js/ckeditor-4.5.10/ckeditor.js"></script>
			<script src="<?php echo INC_URL ?>/js/ckeditor-4.5.10/adapters/jquery.js"></script>
			
			<script>
			$(function() {
				$("#item-new-form").submit(function(e) {
					e.preventDefault();
	
					if ( $.trim($("#post_title").val()) == "" ) {
						alert("제목을 입력해 주십시오.");
						$("#post_title").focus();
						return false;
					}
					if ( $.trim($("#post_content").val()) == "" ) {
						alert("내용을 입력해 주십시오.");
						return false;
					}

					showLoader();

					$("#item-new-form").ajaxSubmit({
						type : "POST",
						url : "./ajax/post-edit.php",
// 						data : $(this).serialize(),
						data : $(this).serializeObject(),
						dataType : "json",
						success: function(xhr) {
							hideLoader();
							if ( xhr.code == "0" ) {
								location.href = "./cs_faq_list.php";
							} else {
								alert( xhr.msg );
							}
						}
					});
				});

				$("#post_content").ckeditor({});

				$("#switch-all").click(function() {
					var chk = $(this).prop("checked");
					$(".post_type_area").prop("checked", chk);
				});

				$('a[id*="delete-uploaded-file-"]').click(function(e) {
					e.preventDefault();
					var key = $(this).attr("id").replace(/\D/g, "");

					$.ajax({
						type : "POST",
						url : "./ajax/delete-uploaded-file.php",
						data : {
							"pid" : $("#post_id").val(),
							"key" : key
						},
						dataType : "json",
						success : function(res) {
							if (res.code == "0") {
								$("#uploaded-file-lists").remove();
							} else {
								alert(res.msg);
							}
						}
					});
				});
				
			}); //$
			</script>

<?php 
require_once ADMIN_PATH . '/admin-footer.php';
?>