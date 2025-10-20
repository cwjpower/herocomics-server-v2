<?php 
require_once '../../wps-config.php';
require_once ADMIN_PATH . '/admin-header.php';

require_once './promotion-lnb.php';
?>
			<!-- bootstrap datepicker -->
			<link rel="stylesheet" href="<?php echo ADMIN_URL ?>/css/datepicker3.css">
			
			<!-- bootstrap datepicker -->
			<script src="<?php echo ADMIN_URL ?>/js/bootstrap-datepicker.js"></script>
			<script src="<?php echo ADMIN_URL ?>/js/locales/bootstrap-datepicker.kr.js"></script>

			<!-- Content Wrapper. Contains page content -->
			<div class="content-wrapper">
				<!-- Content Header (Page header) -->
				<div class="content-header">
					<h1>
						EMAIL 발송
					</h1>
					<ol class="breadcrumb">
						<li><a href="<?php echo ADMIN_URL ?>/admin.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li>프로모션</li>
						<li class="active"><b>EMAIL 발송</b></li>
					</ol>
				</div>

				<!-- Main content -->
				<div class="content body">
					<form id="form-new-item">
						<input type="hidden" name="prom_type" value="EMAIL">
						
						<div class="box box-primary">
							<div class="box-header with-border">
								<h3 class="box-title">메일 작성</h3>
							</div>
							<!-- /.box-header -->
							<div class="box-body">
								<div class="form-group">
									<input class="form-control" name="user_list" placeholder="To:">
								</div>
								<div class="form-group">
									<input class="form-control" name="prom_title" placeholder="Subject:">
								</div>
								<div class="form-group">
									<textarea id="compose-textarea" name="message" class="form-control" style="height: 300px;"></textarea>
								</div>
							</div>
							<!-- /.box-body -->
							<div class="box-footer">
								<div class="pull-right">
									<button type="submit" class="btn btn-primary"><i class="fa fa-envelope-o"></i> 보내기</button>
								</div>
								<button type="reset" class="btn btn-default"><i class="fa fa-times"></i> 초기화</button>
							</div>
							<!-- /.box-footer -->
						</div>

					</form>
				</div><!-- /.content -->
			</div><!-- /.content-wrapper -->
			
			<!-- Numeric -->
			<script src="<?php echo INC_URL ?>/js/jquery/jquery.numeric.min.js"></script>
			<script src="<?php echo INC_URL ?>/js/ls-util.js"></script>
			<script src="<?php echo INC_URL ?>/js/jquery/jquery.oLoader.min.js"></script>
			<!-- CkEditor -->
			<script src="<?php echo INC_URL ?>/js/ckeditor-4.5.10/ckeditor.js"></script>
			<script src="<?php echo INC_URL ?>/js/ckeditor-4.5.10/adapters/jquery.js"></script>
			
			<script>
			$(function() {		
				// CKEditor
				var config = {
						height: 500,
// 						extraPlugins: 'autogrow',
						autoGrow_bottomSpace: 50,
						toolbar:
						[
							['FontSize', 'TextColor', 'Bold', 'Italic', 'Underline', '-', 'NumberedList', 'BulletedList', 'Blockquote', 'Table', '-', 'Undo', 'Redo', '-', 'SelectAll'],
							['UIColor']
						]
				}; 
				$("#compose-textarea").ckeditor(config);

				$("#form-new-item").submit(function(e) {
					e.preventDefault();

					showLoader();

					$.ajax({
						type : "POST",
						url : "./ajax/send-email.php",
						data : $(this).serialize(),
						dataType : "json",
						success : function(res) {
							hideLoader();
							if ( res.code == "0" ) {
								location.href = "email_list.php";
							} else {
								alert( res.msg );
							}
						}
					});
					
				});
			});
			</script>

<?php 
require_once ADMIN_PATH . '/admin-footer.php';
?>