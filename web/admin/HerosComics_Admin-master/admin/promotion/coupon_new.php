<?php 
require_once '../../wps-config.php';

// $publishers = wps_get_user_by_level( 7 );

require_once ADMIN_PATH . '/admin-header.php';

require_once './promotion-lnb.php';
?>

			<link rel="stylesheet" href="<?php echo INC_URL ?>/css/jquery-ui.css">
			
			<!-- Content Wrapper. Contains page content -->
			<div class="content-wrapper">
				<!-- Content Header (Page header) -->
				<div class="content-header">
					<h1>
						쿠폰 등록
					</h1>
					<ol class="breadcrumb">
						<li><a href="<?php echo ADMIN_URL ?>/admin.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li>프로모션</li>
						<li class="active"><b>쿠폰 등록</b></li>
					</ol>
				</div>

				<!-- Main content -->
				<div class="content body">
					<form id="form-item-edit">
						<div class="box box-info">
							<div class="box-header">
							</div>
							<div class="box-body">
								<table class="table table-bordered ls-table">
									<colgroup>
										<col style="width: 15%;">
										<col>
									</colgroup>
									<tbody>
										<!-- 
										<tr>
											<td class="item-label">쿠폰 종류 *</td>
											<td>
												<label><input type="radio" name="coupon_type" value="item" checked> 개별 책</label> &nbsp; &nbsp;
												<label><input type="radio" name="coupon_type" value="cart"> 장바구니</label>
											</td>
										</tr>
										 -->
										<tr>
											<td class="item-label">쿠폰 이름 *</td>
											<td>
												<input type="text" name="coupon_name" id="coupon_name" class="form-control" required>
											</td>
										</tr>
										<tr>
											<td class="item-label">쿠폰 설명</td>
											<td>
												<textarea id="coupon_desc" name="coupon_desc" class="form-control"></textarea>
											</td>
										</tr>
										<!-- 
										<tr>
											<td class="item-label">쿠폰 적용 출판사</td>
											<td>
												<select name="related_publisher" id="related_publisher" class="form-control">
													<option value="">- 해 당 사 항 없 음 -</option>
										<?php 
// 										if (!empty($publishers)) {
// 											foreach ($publishers as $key => $val) {
// 												$user_id = $val['ID'];
// 												$user_name = $val['user_name'];
// 												$display_name = $val['display_name'];
										?>
													<option value="<?php echo $user_id ?>"><?php echo $user_name ?> (<?php echo $display_name ?>)</option>
										<?php
// 											}
// 										}
										?>
												</select>
											</td>
										</tr>
										 -->
										<tr>
											<td class="item-label">유효기간</td>
											<td>
												<div class="input-group">
													<input type="text" id="period_from" name="period_from" class="form-control datepicker actual_range">
													<div class="input-group-addon">
														<i class="fa fa-calendar"></i>
													</div>
													<div class="input-group-addon">
														~
													</div>
													<input type="text" id="period_to" name="period_to" class="form-control datepicker actual_range">
													<div class="input-group-addon">
														<i class="fa fa-calendar"></i>
													</div>
												</div>
											</td>
										</tr>
										<tr>
											<td class="item-label">할인 종류 *</td>
											<td>
												<!-- <label><input type="radio" name="discount_type" id="dt-amount" value="amount"> 할인금액</label> &nbsp; &nbsp; -->
												<label><input type="radio" name="discount_type" id="dt-rate" value="rate" checked> 할인율</label>
											</td>
										</tr>
										<tr id="dt-amount-pane" class="hide">
											<td class="item-label">할인 금액 *</td>
											<td>
												<div class="input-group">
													<input type="text" name="discount_amount" id="discount_amount" class="form-control price">
													<div class="input-group-addon">원</div>
												</div>
											</td>
										</tr>
										<tr id="dt-rate-pane">
											<td class="item-label">할인율 *</td>
											<td>
												<div class="input-group">
													<input type="text" name="discount_rate" id="discount_rate" class="form-control price">
													<div class="input-group-addon">%</div>
												</div>
											</td>
										</tr>
										<tr>
											<td class="item-label">할인 조건</td>
											<td>
												<div class="input-group">
													<div class="input-group-addon">최소 사용 가능 금액</div> 
													<input type="text" name="item_price_min" id="item_price_min" class="form-control price">
													<div class="input-group-addon">원 이상일때만 사용할 수 있습니다</div>
												</div>
												<div class="input-group" style="margin-top: 8px;">
													<div class="input-group-addon">최대 할인 가능 금액</div> 
													<input type="text" name="item_price_max" id="item_price_max" class="form-control price">
													<div class="input-group-addon">원 까지만 할인됩니다.</div>
												</div>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
							<div class="box-footer">
								<button type="submit" class="btn btn-primary">등록합니다</button>
								<button type="reset" class="btn btn-default">초기화</button> &nbsp;
							</div>
						</div><!-- /.box-body -->
					</form>
				</div><!-- /.content -->
			</div><!-- /.content-wrapper -->
			
			<script src="<?php echo INC_URL ?>/js/jquery-ui.min.js"></script>
			<script src="<?php echo INC_URL ?>/js/ls-util.js"></script>
			<script src="<?php echo INC_URL ?>/js/jquery/jquery.oLoader.min.js"></script>
			<!-- InputMask -->
			<script src="<?php echo ADMIN_URL ?>/js/jquery/input-mask/jquery.inputmask.js"></script>
			<script src="<?php echo ADMIN_URL ?>/js/jquery/input-mask/jquery.inputmask.date.extensions.js"></script>
			<script src="<?php echo ADMIN_URL ?>/js/jquery/input-mask/jquery.inputmask.extensions.js"></script>
			<!-- Number -->
			<script src="<?php echo INC_URL ?>/js/jquery/jquery.number.min.js"></script>
			<!-- Form(file) -->
			<script src="<?php echo INC_URL ?>/js/jquery/jquery.form.min.js"></script>
			
			<script>
			$(function() {

				$("#form-item-edit").submit(function(e) {
					e.preventDefault();

					if ($("#coupon_name").val() == "") {
						alert("쿠폰 이름을 입력해 주십시오.");
						$("#coupon_name").focus();
						return false;
					}
					
					if ( $("#dt-amount").prop("checked") && $("#discount_amount").val() == "" ) {
						alert("할인 금액을 입력해 주십시오.");
						$("#discount_amount").focus();
						return false;
					}
					if ( $("#dt-rate").prop("checked") && $("#discount_rate").val() == "" ) {
						alert("할인율을 입력해 주십시오.");
						$("#discount_rate").focus();
						return false;
					}

					showLoader();
					
					$.ajax({
						type : "POST",
						url : "./ajax/coupon-new.php",
						data : $(this).serialize(),
						dataType : "json",
						success : function(res) {
							if (res.code == "0") {
								location.href = "./coupon_list.php";
							} else {
								hideLoader();
								alert(res.msg);
							}
						}
					});
					
				});
				
				$('input[name="discount_type"]').click(function(e) {
					var val = $(this).val();
					if ( val == "amount" ) {
						$("#dt-rate-pane").addClass("hide");
						$("#dt-amount-pane").removeClass("hide");
						$("#item_price_max").prop("disabled", true);
					} else {	// rate
						$("#dt-amount-pane").addClass("hide");
						$("#dt-rate-pane").removeClass("hide");
						$("#item_price_max").prop("disabled", false);
					}
				});
				
				$(".price").number(true).attr("maxlength", 9);

				// 할인율 검증
				$("#discount_rate").blur(function() {
					var val = parseInt($(this).val().replace(/\D/g, ""));

					if (val > 10) {
						alert("할인율은 10%을 초과할 수 없습니다.");
						$("#discount_rate").val("10").focus();
					}
				});
				
				$("#discount_amount, #item_price_min").blur(function() {
					var discount = parseInt($("#discount_amount").val().replace(/\D/g, ""));
					var minAmount = parseInt($("#item_price_min").val().replace(/\D/g, ""));

					if (minAmount > 0 && discount > minAmount) {
						alert("할인 금액이 최소 사용 가능 금액을 초과할 수 없습니다.");
						$("#item_price_min").val(discount).focus();
					}
				});

				// jquery ui calendar
				$( "#period_from" ).datepicker({
					dateFormat : "yy-mm-dd",
					monthNames: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
					monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
					dayNamesMin: ['일','월','화','수','목','금','토'],
					minDate: 0,
					changeMonth: true,
					changeYear: true,
					numberOfMonths: 2,
					showMonthAfterYear: true,
					onClose: function( selectedDate ) {
						$( "#period_to" ).datepicker( "option", "minDate", selectedDate );
					}
				}).inputmask('yyyy-mm-dd');
				
				$( "#period_to" ).datepicker({
					dateFormat : "yy-mm-dd",
					monthNames: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
					monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
					dayNamesMin: ['일','월','화','수','목','금','토'],
					defaultDate: "+1w",
					changeMonth: true,
					changeYear: true,
					showMonthAfterYear: true,
					onClose: function( selectedDate ) {
						$( "#period_from" ).datepicker( "option", "maxDate", selectedDate );
					}
				}).inputmask('yyyy-mm-dd');
				
			}); //$
			</script>

<?php 
require_once ADMIN_PATH . '/admin-footer.php';
?>