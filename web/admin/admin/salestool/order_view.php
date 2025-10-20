<?php 
require_once '../../wps-config.php';
require_once FUNC_PATH . '/functions-payment.php';

if ( empty($_GET['id'] )) {
	lps_alert_back( '주문 아이디가 존재하지 않습니다.' );
}
$order_id = $_GET['id'];

$query = "
		SELECT
			i.book_title,
		    b.author,
		    b.publisher,
			b.isbn,
		    i.sale_price,
		    b.cover_img,
			b.created_dt,
		    o.order_status,
		    o.user_id,
			o.total_amount,
			m.meta_value AS epub,
			m2.meta_value AS total_page
		FROM
			bt_books AS b
		LEFT JOIN
			bt_books_meta AS m
		ON
			b.ID = m.book_id AND
			m.meta_key = 'lps_book_epub_file'
		LEFT JOIN
			bt_books_meta AS m2
		ON
			b.ID = m2.book_id AND
			m2.meta_key = 'lps_book_total_page'
		LEFT JOIN
			bt_order_item AS i
		ON
			b.ID = i.book_id
		LEFT JOIN
			bt_order AS o
		ON
			o.order_id = i.order_id
		WHERE
			o.order_id = ?
";
$stmt = $wdb->prepare( $query );
$stmt->bind_param('i', $order_id);
$stmt->execute();
$rows = $wdb->get_results($stmt);

// 주문
$order_row = lps_get_order_by_id( $order_id );
$ord_dt = $order_row['created_dt'];
$ord_paid = $order_row['total_paid'];
$ord_cybercash = $order_row['cybercash_paid'];
$ord_cyberpoint = $order_row['cyberpoint_paid'];
$ord_status = $order_row['order_status'];


// 주문 회원
$user_id = $rows[0]['user_id'];
$user_row = wps_get_user($user_id);
$user_login = $user_row['user_login'];
$user_name = $user_row['user_name'];
$user_mobile = $user_row['mobile'];
$user_email = $user_row['user_email'];

require_once ADMIN_PATH . '/admin-header.php';

require_once './salestool-lnb.php';
?>

			<!-- Content Wrapper. Contains page content -->
			<div class="content-wrapper">
				<!-- Content Header (Page header) -->
				<div class="content-header">
					<h1>
						주문내역 상세보기
					</h1>
					<ol class="breadcrumb">
						<li><a href="<?php echo ADMIN_URL ?>/admin.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li><a href="<?php ECHO ADMIN_URL ?>/salestool/order_list.php">판매 관리</a></li>
						<li class="active"><b>주문 상세보기</b></li>
					</ol>
				</div>

				<!-- Main content -->
				<div class="content body">
					
					<div class="box box-primary">
						<div class="box-header">
							<h4>상품 정보</h4>
						</div>
						<div class="box-body">
							<table class="table table-bordered">
								<thead>
									<tr class="active">
										<th class="text-center">표지</th>
										<th class="text-center">제목/정보 </th>
										<th class="text-center">가격</th>
										<th class="text-center">진행상황</th>
										<th class="text-center">구입경로</th>
									</tr>
								</thead>
								<tbody>
						<?php 
						if (!empty($rows)) {
							foreach ($rows as $key => $val) {
								$btitle = $val['book_title'];
								$bisbn = $val['isbn'];
								$bcover = $val['cover_img'];
								$bauthor = $val['author'];
								$bpublisher = $val['publisher'];
								$bprice = $val['sale_price'];
								$bcreated = substr($val['created_dt'], 0, 10);
								$btpage = $val['total_page']; 
								$bstatus = $val['order_status'];
								$epub = unserialize($val['epub']);
								
								$epub_file_size = @wps_format_bytes(filesize($epub['file_path']));
								
						?>
									<tr>
										<td class="text-center">
											<img src="<?php echo $bcover ?>" style="height: 80px;">
										</td>
										<td>
											<b><?php echo $btitle ?></b>
											<div><?php echo $bauthor ?> | <?php echo $bpublisher ?> | <?php echo $bcreated ?></div>
											<div>ISBN : <?php echo $bisbn ?> &nbsp; ( <?php echo number_format($btpage) ?> page,  <?php echo $epub_file_size ?> )</div>
										</td>
										<td class="text-center"><?php echo number_format($bprice) ?> 원</td>
										<td class="text-center"><?php echo $wps_order_status[$bstatus] ?></td>
										<td></td>
									</tr>
						<?php 
							}
						}
						?>
									<tr class="info">
										<td colspan="5" class="text-right">
											<b>총 상품 금액 : <?php echo number_format($rows[0]['total_amount']) ?></b> 원
										</td>
									</tr>
								</tbody>
							</table>
						</div><!-- /.box-body -->
					</div><!-- /. box -->
					
					<div class="box box-warning">
						<div class="box-header">
							<h4>쿠폰 사용 내역</h4>
						</div>
						<div class="box-body">
							<table class="table table-bordered">
								<thead>
									<tr class="active">
										<th>#</th>
										<th>주문번호 </th>
										<th>쿠폰내용</th>
										<th>처리상태</th>
										<th>처리일</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
									</tr>
								</tbody>
							</table>
						</div><!-- /.box-body -->
					</div><!-- /. box -->
					
					<div class="row">
						<div class="col-md-6">
							<div class="box box-success">
								<div class="box-header">
									<h4>결제 정보</h4>
								</div>
								<div class="box-body">
									<table class="table table-bordered ls-table">
										<colgroup>
											<col style="width: 40%;">
											<col>
										</colgroup>
										<tbody>
											<tr>
												<td class="item-label">주문번호</td>
												<td>
													<?php echo $order_id ?>
												</td>
											</tr>
											<tr>
												<td class="item-label">주문시간</td>
												<td>
													<?php echo $ord_dt ?>
												</td>
											</tr>
											<tr>
												<td class="item-label">포인트 사용금액</td>
												<td>
													<?php echo number_format($ord_cyberpoint) ?> 원
												</td>
											</tr>
											<tr>
												<td class="item-label">실제 사용금액</td>
												<td>
													<?php echo number_format($ord_cybercash) ?> 원
												</td>
											</tr>
											<tr>
												<td class="item-label">총 결제 금액</td>
												<td>
													<?php echo number_format($ord_paid) ?> 원
												</td>
											</tr>
											<tr>
												<td class="item-label">진행상황</td>
												<td>
													<?php echo $wps_order_status[$ord_status] ?>
												</td>
											</tr>
										</tbody>
									</table>
								</div><!-- /.box-body -->
							</div><!-- /. box -->
						</div>
						<div class="col-md-6">
							<div class="box box-success">
								<div class="box-header">
									<h4>주문자 정보</h4>
								</div>
								<div class="box-body">
									<table class="table table-bordered ls-table">
										<colgroup>
											<col style="width: 40%;">
											<col>
										</colgroup>
										<tbody>
											<tr>
												<td class="item-label">계정</td>
												<td>
													<?php echo $user_login ?>
													<a href="../users/user_index.php?id=<?php echo $user_id ?>" class="label label-info">회원정보 보기</a>
												</td>
											</tr>
											<tr>
												<td class="item-label">성명</td>
												<td>
													<?php echo $user_name ?>
												</td>
											</tr>
											<tr>
												<td class="item-label">연락처</td>
												<td>
													<?php echo $user_mobile ?>
												</td>
											</tr>
											<tr>
												<td class="item-label">본인확인 이메일</td>
												<td>
													<?php echo $user_email ?>
												</td>
											</tr>
										</tbody>
									</table>
								</div><!-- /.box-body -->
							</div><!-- /. box -->
						</div>
					</div>
					
					<div class="text-center">
						<button type="button" class="btn btn-info" id="cancel-btn">확인</button>
					</div>
					
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
							"id" : "<?php echo $order_id ?>"
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