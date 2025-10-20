<?php
/**
 * Book select Popup
 *  Modal 영역에 html 을 Return 한다.
 *  책 제목으로 검색 한다.
 */

require_once '../../../wps-config.php';
require_once FUNC_PATH . '/functions-book.php';
require_once INC_PATH . '/classes/WpsPaginator.php';

// page number
$page = empty($_GET['page']) ? 1 : $_GET['page'];

// search
$sql = '';
$sparam = [];
$book_title='';
$book_title = $_GET['book-title'];

if(!empty($book_title)){
	$sql = " AND ( book_title LIKE ? ) ";
	array_push( $sparam,  '%' . $book_title . '%' );
}

$query = "
		SELECT
			*
		FROM
			bt_books
		WHERE
			book_title like ?
		ORDER BY
			ID DESC
	";



$stmt = $wdb->prepare( $query );
$book_title_like = '%'. $book_title .'%';
$stmt->bind_param('s',$book_title_like );

$stmt->execute();
$rows = $wdb->get_results($stmt);


//$paginator = new WpsPaginator($wdb, $page, $rows_count=10);
//$rows = $paginator->ls_init_pagination( $query, $sparam );
//$total_count = $paginator->ls_get_total_rows();
//$total_records = $paginator->ls_get_total_records();

?>

<div class="box-body">
	<table class="table table-striped table-hover">
		<thead>
		<tr>
			<th>책 아이디 </th>
			<th>isbn </th>
			<th>제목</th>
			<th>브랜드</th>
			<!--<th>등록형태</th>-->
			<!--<th>상태</th>-->
		</tr>
		</thead>
		<tbody>
		<?php
		if ( !empty($rows) ) {
			//$list_no = $page == 1 ? $total_count : $total_count - (($page - 1) * $paginator->rows_per_page);

			foreach ( $rows as $key => $val ) {
				$book_id = $val['ID'];
				$publisher = $val['publisher'];
				$comics_brand = $val['comics_brand']; // 마블, 디씨, 이미지 코믹스 구분
				$book_title = $val['book_title'];
				$author = $val['author'];
				$isbn = $val['isbn'];
				$normal_price = $val['normal_price'];
				$sale_price = $val['sale_price'];
				$upload_type = $val['upload_type'];
				$book_status = $val['book_status'];
				$is_pkg = $val['is_pkg'];
				$pkg_label = !strcmp($is_pkg, 'N') ? '<span class="label label-default">단품</span>' : '<span class="label label-success">세트</span>';

				?>
				<tr>
					<td> <?php echo $book_id; ?></td>
					<td> <?php echo $isbn; ?></td>
					<td>
						<a href="#" data-book-id ="<?php echo $book_id ?>" data-book-title ="<?php echo $book_title ?>" class="set-book-title"><?php //echo $pkg_label ?> <?php echo $book_title ?></a>
					</td>
					<td><?php echo $wps_comics_brand[$comics_brand] ?></td>
					<!--<td>--><?php //echo $wps_upload_type[$upload_type] ?><!--</td>-->
					<!--<td>--><?php //echo $wps_book_status[$book_status] ?><!--</td>-->
				</tr>
				<?php
				//$list_no--;
			}
		}
		?>
		</tbody>
	</table>
</div>
<script>
	$('.set-book-title').on('click', function(event){
		event.preventDefault();
		$this = $(this);
		var book_id = $this.data('book-id'),
			book_title = $this.data('book-title'),
			input_from = $('#from').val();

		if(input_from === 'before'){
			$('input[name="reading_order_before"]').val(book_title);
			$('input[name="reading_order_id_before"]').val(book_id);
		} else if ( input_from ==='after'){
			$('input[name="reading_order_after"]').val(book_title);
			$('input[name="reading_order_id_after"]').val(book_id);
		}

		$('#book-search-modal').modal('hide');

//		console.log($this.data('book-id'));
//		console.log($this.data('book-title'));
//		console.log($("#from").val());
	});

</script>
<div class="box-footer text-center">
	<?php //echo $paginator->ls_bootstrap_pagination_link(); ?>
</div>
</div><!-- /.box -->
