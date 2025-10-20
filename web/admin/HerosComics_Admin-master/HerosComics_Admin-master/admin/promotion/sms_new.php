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
						SMS 발송
					</h1>
					<ol class="breadcrumb">
						<li><a href="<?php echo ADMIN_URL ?>/admin.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li>프로모션</li>
						<li class="active"><b>SMS 발송</b></li>
					</ol>
				</div>

				<!-- Main content -->
				<div class="content body">
					<form id="form-new-item">
						<input type="hidden" name="prom_type" value="SMS">
						<div class="col-md-3">
							<div class="box box-info">
								<div class="box-body">
									<div class="form-group">
										<label>관리용 제목</label>
										<input type="text" name="prom_title" id="prom_title" class="form-control">
									</div>
									<div class="form-group">
										<label>메시지 내용</label>
										<textarea name="message" id="message" class="form-control" style="height: 100px;"></textarea>
										<span class="pull-right" id="byte-guage"><b>0</b> / 90</span>
									</div>
									<div class="form-group">
										<label>발신번호</label>
										<input type="text" name="callback" id="callback" class="form-control numeric" value="" maxlength="12" placeholder="숫자만 입력하십시오.">
									</div>
								</div><!-- /.box-body -->
								<div class="box-footer text-right">
									<button type="submit" class="btn btn-primary">전송</button>
								</div>
							</div><!--  /.box -->
						</div><!-- /.col-md-3 -->
						<div class="col-md-3">
							<div class="box box-success">
								<div class="box-body">
									<div class="form-group">
										<label>수신자 (휴대전화번호 숫자만)</label>
										<textarea name="user_list" class="form-control" style="height: 400px;"></textarea>
									</div>
								</div><!-- /.box-body -->
							</div><!--  /.box -->
						</div><!-- /.col-md-3 -->
					</form>
				</div><!-- /.content -->
			</div><!-- /.content-wrapper -->
			
			<!-- Numeric -->
			<script src="<?php echo INC_URL ?>/js/jquery/jquery.numeric.min.js"></script>
			<script src="<?php echo INC_URL ?>/js/ls-util.js"></script>
			<script src="<?php echo INC_URL ?>/js/jquery/jquery.oLoader.min.js"></script>
			
			<script>
			$(function() {
				$(".numeric").numeric( true, 0 );

				/* message */
				$("#message").on("click keyup keypress input propertychange", function() {
					limitBytes( $("#message").val(), 90 );
				});
				/* message */

				$("#form-new-item").submit(function(e) {
					e.preventDefault();

					showLoader();

					$.ajax({
						type : "POST",
						url : "./ajax/send-sms.php",
						data : $(this).serialize(),
						dataType : "json",
						success : function(res) {
							hideLoader();
							if ( res.code == "0" ) {
								location.href = "sms_list.php";
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