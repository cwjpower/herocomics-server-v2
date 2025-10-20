<?php 
require_once '../../wps-config.php';

$wps_options = wps_get_option();

$site_title = wps_get_meta_value_by_key($wps_options, 'site_title', 'option_value');
$return_email = wps_get_meta_value_by_key($wps_options, 'return_email', 'option_value');
// $xxx = wps_get_meta_value_by_key($wps_options, 'xxx', 'option_value');


require_once ADMIN_PATH . '/admin-header.php';

?>

			<!-- Left side column. contains the logo and sidebar -->
			<aside class="main-sidebar">
				<!-- sidebar: style can be found in sidebar.less -->
				<section class="sidebar" style="height: auto;" id="scrollspy">
					<!-- Sidebar user panel -->
					<div class="user-panel">
						<div class="pull-left image">
							<img src="<?php echo IMG_URL ?>/common/photo-default.png" class="img-circle" alt="User Image">
						</div>
						<div class="pull-left info" style="width: 70%;">
							<div class="pull-right"><a href="<?php echo ADMIN_URL ?>/users/profile.php"><i class="fa fa-gear"></i></a></div>
							<div class="pull-left"><?php echo wps_get_current_user_name() ?></div>
							<div style="clear: both; padding-top: 10px;"><a href="<?php echo ADMIN_URL ?>/logout.php" class="label label-warning">로그아웃</a></div>
						</div>
					</div>
				</section>
				<!-- /.sidebar -->
			</aside>
			<!-- /.main-sidebar -->
			
			<!-- Content Wrapper. Contains page content -->
			<div class="content-wrapper">
				<!-- Content Header (Page header) -->
				<section class="content-header">
					<h1>일반 설정</h1>
				</section>

				<!-- Main content -->
				<section class="content">
					<div class="row">
						<div class="col-md-6">
							<div class="box box-warning">
								<div class="box-header with-border">
									<h3 class="box-title">Site</h3>
								</div>
								<!-- /.box-header -->
								<!-- form start -->
								<form id="item-new-form" class="form-horizontal">
									<div class="box-body">
										<div class="form-group">
											<label for="site_title" class="col-sm-2 control-label">사이트 제목</label>
											<div class="col-sm-10">
												<input type="text" class="form-control" id="site_title" name="site_title" placeholder="사이트 제목" value="<?php echo $site_title ?>">
											</div>
										</div>
										<div class="form-group">
											<label for="return_email" class="col-sm-2 control-label">이메일</label>
											<div class="col-sm-10">
												<input type="email" class="form-control" id="return_email" name="return_email" placeholder="Return Email Address" value="<?php echo $return_email ?>">
												<div class="help-block">이 주소는 메일 발송 시 보내는 사람(From) 이메일로 사용합니다.</div>
											</div>
										</div>
									</div>
									<!-- /.box-body -->
									<div class="box-footer">
										<button type="submit" class="btn btn-primary">저장합니다</button>
									</div>
									<!-- /.box-footer -->
								</form>
							</div>
						</div>
					</div><!-- /.row -->
				</section><!-- /.content -->
			</div><!-- /.content-wrapper -->


			<script src="<?php echo INC_URL ?>/js/ls-util.js"></script>
			<script src="<?php echo INC_URL ?>/js/jquery/jquery.oLoader.min.js"></script>
			
			<script>
			$(function() {
				$("#item-new-form").submit(function(e) {
					e.preventDefault();

					showLoader();
					
					$.ajax({
						type : "POST",
						url : "./ajax/options-general.php",
						data : $(this).serialize(),
						dataType : "json",
						success : function(res) {
							hideLoader();
							if (res.code == "0") {
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