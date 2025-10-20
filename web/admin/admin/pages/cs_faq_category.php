<?php 
require_once '../../wps-config.php';

$taxonomy = empty($_GET['tkey']) ? 'wps_category_faq' : $_GET['tkey'];

require_once ADMIN_PATH . '/admin-header.php';

require_once './pages-lnb.php';
?>

			<link href="<?php echo ADMIN_URL ?>/css/jquery.contextMenu.css" rel="stylesheet">
			<link href="<?php echo ADMIN_URL ?>/css/ui.fancytree.css" rel="stylesheet">
			
			<!-- Content Wrapper. Contains page content -->
			<div class="content-wrapper">
				<!-- Content Header (Page header) -->
				<div class="content-header">
					<h1>
						자주묻는질문(FAQ) 유형 관리
					</h1>
					<ol class="breadcrumb">
						<li><a href="<?php echo ADMIN_URL ?>/admin.php"><i class="fa fa-dashboard"></i> Home</a></li>
						<li><a href="<?php echo ADMIN_URL ?>/pages/">페이지 관리</a></li>
						<li class="active"><b>질문유형 관리</b></li>
					</ol>
				</div>

				<!-- Main content -->
				<div class="content body">
					<div class="row">
						<div class="col-md-4">
							<div class="box box-success">
								<div class="box-header with-border">
									<div>
										<div class="box-tools pull-right">
											<button type="button" class="btn btn-primary btn-sm" id="new_node_btn" data-target="#myModal">최상위 카테고리 등록</button>
										</div>
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
			
			<div class="modal fade" id="myModal">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title">카테고리 추가</h4>
						</div>
						<div class="modal-body">
							<form id="category-new-form">
								<input type="hidden" name="tkey" id="tkey" value="<?php echo $taxonomy ?>">
								<input type="hidden" name="mode" id="mode" value="NEW">
								<input type="hidden" name="tid" id="tid" value="0">
								<input type="hidden" name="parent" id="parent" value="0">
	
								<div class="form-group">
									<label>이름(필수)</label>
									<input type="text" name="tag_name" id="tag_name" class="form-control" maxlength="100" required autofocus>
								</div>
								<div class="form-group">
									<label>URL</label>
									<input type="text" name="tag_url" id="tag_url" class="form-control">
									<div class="help-block">http://www.yoursite.com/path/file.html 혹은  /admin/users/index.php</div>
								</div>
							</form>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-primary" id="tree-act-save">저장합니다</button>
							<button type="button" class="btn btn-default" data-dismiss="modal">닫기</button>
						</div>
					</div><!-- /.modal-content -->
				</div><!-- /.modal-dialog -->
			</div><!-- /.modal -->

			<script src="<?php echo INC_URL ?>/js/jquery-ui.min.js"></script>
			<script src="<?php echo ADMIN_URL ?>/js/jquery/jquery.fancytree-all.min.js"></script>
			<script src="<?php echo ADMIN_URL ?>/js/jquery/jquery.contextMenu-1.6.5.js"></script>
			<script src="<?php echo ADMIN_URL ?>/js/jquery/jquery.fancytree.contextMenu.js"></script>
			
			<script>
			$(function() {
				$("#new_node_btn").click(function() {
					$("#tag_name, #tag_url").val( "" );
					$("#mode").val("NEW");
					$("#tid").val( "0" );
					$("#parent").val( "0" );
					$(".modal-title").html( "카테고리 추가" );
					$(".modal").modal("show");
				});

				$("#tree-act-save").click(function() {
					$.ajax({
						type : "POST",
						url : "../category/ajax/fancytree-node-save.php",
						data : {
							"param" : $("#category-new-form").serialize()
						},
						dataType : "json",
						success : function(res) {
							if ( res.code != "0" ) {
								alert( res.msg );
							} else {
								location.reload();
							}
						}
					});
				});

				// Context menu
				$("#tree").fancytree({
					extensions: ['contextMenu', 'dnd', 'edit'],
					source: {
						url: "../category/ajax/fancytree_node.php?tkey=<?php echo $taxonomy ?>"
					},
					debugLevel: 0,
					contextMenu: {
						menu: {
// 							'add': { 'name': '추가(Add)', 'icon': 'add' },
							'edit': { 'name': '편집(Edit)', 'icon': 'edit' },
							'sep1': '---------',
							'delete': { 'name': '삭제(Delete)', 'icon': 'delete' },
							'sep2': '---------',
							'quit': { 'name': '닫기(Quit)', 'icon': 'quit' }
						},
						actions: function(node, action, options) {
							if ( action == "add" ) {
								addNode(node);
							} else if ( action == "edit" ) {
								editNode(node);
							} else if ( action == "delete" ) {
								deleteNode(node);
							}
						}
					},
					dnd: {
// 						autoExpandMS: 400,
// 						focusOnClick: true,
// 				        preventVoidMoves: true,
// 				        preventRecursiveMoves: true,
// 				        dragStart: function(node, data) {
// 				          return true;
// 				        },
// 				        dragEnter: function(node, data) {
// 				           return true;
// 				        },
// 				        dragDrop: function(node, data) {
// 				          data.otherNode.moveTo(node, data.hitMode);
// 				          moveNode( data.otherNode, node );
// 				        }
					}
				});

				function addNode(node) {
					$("#tag_name, #tag_url").val( "" );
					$("#mode").val("NEW");
					$("#tid").val( "0" );
					$("#parent").val( node.key );
					$(".modal-title").html( "카테고리 추가" );
					$(".modal").modal("show");
				}

				function editNode(node) {
					$("#tag_name").val( node.data.titleOnly );
					$("#tag_url").val( node.data.url );
					$("#mode").val("EDIT");
					$("#tid").val( node.key );
					$(".modal-title").html( "카테고리 편집" );
					$(".modal").modal("show");
				}

				function deleteNode(node) {
					if ( !confirm("삭제하시겠습니까?") ) {
						return;
					}
					$.ajax({
						type : "POST",
						url : "../category/ajax/fancytree-node-delete.php",
						data : {
							termID : node.key
						},
						dataType : "json",
						success : function(res) {
							if ( res.code != "0" ) {
								alert( res.msg );
							} else {
								location.reload();
							}
						}
					});
				}

				function moveNode( other, node ) {
					if ( other.key == node.key ) {
						return;
					}
					var nodeKeys = new Array();
					for ( var i = 0, len = node.parent.children.length; i < len; i++ ) {
						nodeKeys.push( node.parent.children[i].key );
					}
					$.ajax({
						type : "POST",
						url : "../category/ajax/fancytree-node-move.php",
						data : {
							"termID" : other.key,
							"parentID" : node.key,
							"nodeKeys" : nodeKeys,
							"tkey" : $("#tkey").val()
						},
						dataType : "json",
						success : function(res) {
							if ( !res.result ) {
								alert("이동하지 못했습니다.");
							}
						}
					});
				}

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
			});
			</script>

<?php 
require_once ADMIN_PATH . '/admin-footer.php';
?>