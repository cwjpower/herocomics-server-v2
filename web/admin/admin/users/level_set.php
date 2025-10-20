<?php 
require_once '../../wps-config.php';

$taxonomy = empty($_GET['tkey']) ? 'wps_category_admin_menu' : $_GET['tkey'];
$lvl = empty($_GET['user_level']) ? '' : trim($_GET['user_level']);

require_once ADMIN_PATH . '/admin-header.php';

require_once './users-lnb.php';
?>

			<link href="<?php echo ADMIN_URL ?>/css/jquery.contextMenu.css" rel="stylesheet">
			<link href="<?php echo ADMIN_URL ?>/css/ui.fancytree.css" rel="stylesheet">
		
			<!-- Content Wrapper. Contains page content -->
			<div class="content-wrapper">
				<!-- Content Header (Page header) -->
				<div class="content-header">
					<h1>
						등급권한 설정
					</h1>
					<ol class="breadcrumb">
						<li><a href="<?php echo ADMIN_URL ?>/admin.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li class="active">카테고리관리</li>
					</ol>
				</div>

				<!-- Main content -->
				<div class="content body">
					<div class="row">
						<div class="col-md-6">
							<div class="alert alert-warning">
								<h4>회원등급</h4>
								<select id="user_level" class="form-control">
									<optgroup label="CMS 사용 회원등급">
										<option value="">-선택-</option>
					<?php 
					foreach ($wps_user_level as $key => $val ) {
						if ($key > 1 && $key < 10) {
							$selected = $lvl == $key ? 'selected' : '';
					?>
										<option value="<?php echo $key ?>" <?php echo $selected ?>><?php echo $val ?></option>
					<?php 
						}
					}
					?>
									</optgroup>
								</select>
								
								<div class="text-center">
									<button type="button" id="btn-save-level" class="btn margin btn-primary btn-sm">저장합니다</button>
									<button type="reset" class="btn btn-default btn-sm">초기화</button>
								</div>
								
							</div>
						</div>
						<div class="col-md-6">
							<div class="box box-success">
								<div class="box-header with-border">
									<div class="pull-left">
										<button type="button" id="tree-select-all" class="btn bg-maroon btn-flat btn-xs"><i class="fa fa-fw fa-check-square-o"></i> 전체 선택</button>
										<button type="button" id="tree-deselect-all" class="btn bg-purple btn-flat margin btn-xs"><i class="fa fa-fw fa-square-o"></i> 전체 해제</button>
									</div>
									<div class="pull-right">
										<button type="button" id="tree-expand" class="btn btn-success btn-sm"><i class="fa fa-fw fa-folder-open"></i> 모두 확장</button>
										<button type="button" id="tree-reduce" class="btn btn-info btn-sm"><i class="fa fa-fw fa-folder"></i> 모두 축소</button>
									</div>
								</div>
								<div class="box-body">
									<div id="tree"></div>
								</div><!-- /.box-body -->
							</div><!-- /.box -->
						</div>
					</div>
				</div><!-- /.content -->
			</div><!-- /.content-wrapper -->
			
			<script src="<?php echo INC_URL ?>/js/jquery-ui.min.js"></script>
			<script src="<?php echo ADMIN_URL ?>/js/jquery/jquery.fancytree-all.min.js"></script>
			<script src="<?php echo ADMIN_URL ?>/js/jquery/jquery.contextMenu-1.6.5.js"></script>
			<script src="<?php echo ADMIN_URL ?>/js/jquery/jquery.fancytree.contextMenu.js"></script>
			
			<script>
			$(function() {
				var selKeys = [];
				
				$("#tree").fancytree({
					source: {
						url: "./ajax/fancytree_node.php?user_level=<?php echo $lvl?>"
					},
					checkbox: true,
					selectMode: 2,
					debugLevel: 0,
					select: function(event, data) {
						selKeys = $.map(data.tree.getSelectedNodes(), function(node) {
							return node.key;
						});
// 						console.log( selKeys );
					},
					dblclick: function(event, data) {
						data.node.toggleSelected();
					},
					keydown: function(event, data) {
						if (event.which === 32) {
							data.node.toggleSelected();
							return false;
						}
					}
				});

				// 전체 선택
				$("#tree-select-all").click(function() {
					$("#tree").fancytree("getTree").visit(function(node){
						node.setSelected(true);
					});
				});
				// 전체 해제
				$("#tree-deselect-all").click(function() {
					$("#tree").fancytree("getTree").visit(function(node){
						node.setSelected(false);
					});
				});
				// 모두 확장
				$("#tree-expand").click(function() {
					$("#tree").fancytree("getRootNode").visit(function(node){
						node.setExpanded(true);
					});
				});
				// 모두 취소
				$("#tree-reduce").click(function() {
					$("#tree").fancytree("getRootNode").visit(function(node){
						node.setExpanded(false);
					});
				});

				$("#btn-save-level").click(function() {
					var level = $("#user_level").val();
					var items = selKeys;

					$.ajax({
						type : "POST",
						url : "./ajax/level-set.php",
						data : {
							"level" : level,
							"items" : items
						},
						dataType : "json",
						success : function(res) {
							if (res.code == "0") {
								if (res.result) {
									alert("적용했습니다.");
								}
							} else {
								alert(res.msg);
							}
						}
					});
				});

				$("#user_level").change(function() {
					location.href = "?user_level=" + $(this).val();
				});
			});
			</script>

<?php 
require_once ADMIN_PATH . '/admin-footer.php';
?>