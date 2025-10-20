			<footer class="main-footer">
				<div class="pull-right hidden-xs">
					<b>BookTalk</b> CMS
					<a href="<?php echo ADMIN_URL ?>/category/?tkey=wps_category_admin_menu"><i class="fa fa-gears"></i></a>
				</div>
				<strong>Copyright &copy; 2016 <a href="#">Bicon</a>.</strong> All rights reserved.
			</footer>

			<!-- Control Sidebar -->
			<aside class="control-sidebar control-sidebar-dark">
				<!-- Create the tabs -->
				<div class="pad">
					This is an example of the control sidebar.
				</div>
			</aside><!-- /.control-sidebar -->
			<!-- Add the sidebar's background. This div must be placed
					 immediately after the control sidebar -->
			<div class="control-sidebar-bg"></div>

		</div><!-- ./wrapper -->


		<!-- modal for Book Select -->
		<div class="modal fade" id="book-search-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="exampleModalLabel">
							<span class="glyphicon glyphicon-book" aria-hidden="true"></span>&nbsp;책 검색
							<small>책 제목으로 검색합니다.</small>
						</h4>
					</div>
					<div class="modal-body">
						<form id="book-search" class="form-inline">
							<div class="form-group">
								<label for="recipient-name" class="control-label">제목</label>
								<input type="text" class="form-control form-group-sm" id="book-title" name ="book-title" style="width:300px">
								<input type="hidden" class="form-control form-group-sm" id="from" name ="from">
								<button type="submit" class="btn btn-default">검색</button>
							</div>
						</form>
						<!-- search result -->
						<div id="book-list">

						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

					</div>
				</div>
			</div>
		</div><!-- modal for Book Select -->

			<script>

				$('#book-search').submit(function(e){
					e.preventDefault();
					var $book_title = $("#book-title").val();
					if ($book_title.length == 0) {
						alert("검색할 책의 제목을 입력하세요!");
						return false;
					}

					$.ajax({
						type : "GET",
						url : "./ajax/book-select-pop.php",
						data : $(this).serialize(),
						dataType : "html",
						success : function(res) {
							$('#book-list').html(res);
						}
					});
				});

				$('#book-search-modal').on('hidden.bs.modal', function (e) {
					$( "#book-list" ).empty();
					$( "#book-title" ).val("");
				});

				$('#book-search-modal').on('show.bs.modal', function (event) {
					var button = $(event.relatedTarget);
					// Button that triggered the modal
					var $from = button.data('from');
					var modal = $(this)
					modal.find('#from').val($from)
				});

			</script>

	</body>
</html>
