<?php
require_once '../../wps-config.php';
require_once FUNC_PATH . '/functions-fancytree.php';
//
//// 책 카테고리
//$book_cate_first = wps_fancytree_root_node_by_name('wps_category_books');
//
//if (wps_is_admin()) {
//	require_once ADMIN_PATH . '/admin-header.php';
//} else {
//	require_once ADMIN_PATH . '/agent-header.php';
//}
//
//require_once './books-lnb.php';
?>


<!doctype html>
<html>
<head>
	<title>Easy Image Mapper</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">
	<link rel="stylesheet" href="<?php echo INC_URL ?>/css/bootstrap.min.css">
	<link rel="stylesheet" href="easy-mapper.css">
<!--	<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>-->
	<script src="<?php echo ADMIN_URL ?>/js/jquery-2.2.3.min.js"></script>
	<script src="easy-mapper-1.2.0.js"></script>
</head>
<body>

<!-- 딤 스크린 -->
<div id="dim"></div>

<div class="pop" id="pop-local">
	<p class="pop-title">LOAD LOCAL IMAGE</p>
	<div class="pop-content">
		<input type="file" id="pop-local-input">
	</div>
	<div class="pop-btn">
		<div class="pop-btn-confirm">LOAD</div>
		<div class="pop-btn-cancel">CANCEL</div>
	</div>
</div>

<div class="pop" id="pop-url">
	<p class="pop-title">LINK IMAGE URL</p>
	<div class="pop-content">
		<input type="text" id="pop-url-input">
	</div>
	<div class="pop-btn">
		<div class="pop-btn-confirm">LINK</div>
		<div class="pop-btn-cancel">CANCEL</div>
	</div>	
</div>

<div class="pop" id="pop-code">
	<p class="pop-title">CODE GENERATED</p>
	<div class="pop-btn">
		<div class="pop-btn-copy" id="pop-btn-copy-a">SHOW MARKUP AS <em>&lt;A&gt; TAG</em> FORM</div>
		<div class="pop-btn-copy" id="pop-btn-copy-im">SHOW MARKUP AS <em>IMAGE MAP</em> FORM</div>
		<div class="pop-btn-cancel _full">CLOSE</div>
	</div>
</div>

<div class="pop" id="pop-codegen-a">
	<p class="pop-title">&lt;A&gt; TAG FORM</p>
	<div class="pop-content">
		<p></p>
	</div>
	<div class="pop-btn-cancel _back">BACK</div>
	<div class="pop-btn-cancel">CLOSE</div>
</div>

<div class="pop" id="pop-codegen-im">
	<p class="pop-title">IMAGE MAP FORM</p>
	<div class="pop-content">
		<p></p>
	</div>
	<div class="pop-btn-cancel _back">BACK</div>
	<div class="pop-btn-cancel">CLOSE</div>
</div>

<div class="pop" id="pop-info">
	<p class="pop-title">APP INFORMATION</p>
	<div class="pop-content">
		<p>
			<em class="pop-content-alert">&#9888; This app works on IE10+ only.</em>
			<strong>Easy Image Mapper (v1.2.0)</strong><br>
			Author: Inpyo Jeon<br>
			Contact: inpyoj@gmail.com<br>
			Website: <a class="_hover-ul" href="https://github.com/1npy0/easy-mapper" target="_blank">GitHub Repository</a>
		</p>
	</div>
	<div class="pop-btn-cancel _full">CLOSE</div>
</div>

<div class="pop" id="pop-addlink">
	<p class="pop-title">ADD URL LINK</p>
	<div class="pop-content">
		<input type="text" id="pop-addlink-input">
		<label><input type="radio" name="pop-addlink-target" value="_blank" checked>New Window (target:_blank)</label>
		<label><input type="radio" name="pop-addlink-target" value="_self">Self Frame (target:_self)</label>
		<label><input type="radio" name="pop-addlink-target" value="_parent">Parent Frame (target:_parent)</label>
		<label><input type="radio" name="pop-addlink-target" value="_top">Full Body (target:_top)</label>
	</div>
	<div class="pop-btn">
		<div class="pop-btn-confirm">ADD LINK</div>
		<div class="pop-btn-cancel">CANCEL</div>
	</div>
</div>

<!-- 헤더 -->
<div id="gnb">
	<a id="gnb-title" href="easy-mapper.html" onclick="if (!confirm('Do you want to reset all the changes?')) return false;">&#8635; REFRESH</a>

	<!-- 드롭다운 메뉴 -->
	<ul id="gnb-menu">
		<li id="gnb-menu-source">
			<span>SOURCE &#9662;</span>
			<ul class="gnb-menu-sub">
				<li id="gnb-menu-local">LOCAL</li>
				<li id="gnb-menu-url">URL</li>
			</ul>
		</li>
		<li id="gnb-menu-measure">
			<span>MEASURE &#9662;</span>
			<ul class="gnb-menu-sub _toggle">
				<li id="gnb-menu-drag" class="_active">DRAG<em>&nbsp;&#10003;</em></li>
				<li id="gnb-menu-click">CLICK<em>&nbsp;&#10003;</em></li>
			</ul>
		</li>
		<li id="gnb-menu-unit">
			<span>UNIT &#9662;</span>
			<ul class="gnb-menu-sub _toggle">
				<li id="gnb-menu-pixel" class="_active">PX<em>&nbsp;&#10003;</em></li>
				<li id="gnb-menu-percent">%<em>&nbsp;&#10003;</em></li>
			</ul>
		</li>
		<li id="gnb-menu-clear">
			<span>CLEAR</span>
		</li>
		<li id="gnb-menu-generate">
			<span>GENERATE</span>
		</li>
		<li id="gnb-menu-info">
			<span>?</span>
		</li>
	</ul>
</div>

<!-- 작업공간 -->
<div id="workspace">
	<!-- 눈금자 -->
	<div id="workspace-ruler">
		<div id="workspace-ruler-x">
			<div id="workspace-ruler-x-2"></div>
			<div id="workspace-ruler-x-1"></div>
		</div>
		<div id="workspace-ruler-y">
			<div id="workspace-ruler-y-2"></div>
			<div id="workspace-ruler-y-1"></div>
		</div>
	</div>

	<!-- 이미지 -->
	<div id="workspace-img-wrap">
		<img id="workspace-img" src="sampleImage.png">

		<!-- 그리드 -->
		<div id="grid-x1" class="grid-1"></div>
		<div id="grid-y1" class="grid-1"></div>
		<div id="grid-x2" class="grid-2"></div>
		<div id="grid-y2" class="grid-2"></div>
		<span id="grid-coords"></span>
	</div>

</div>
<div id="map-info-wrap" style="">
	<div id="map-info-gnb">
		<h1 class="title">프레임 정보</h1>
	</div>
	<div class="map-info-fields">
		<form id="form-book-action" action="./ajax/book-action-map.php" method="POST">
			<fieldset>
			<div class="form-group">
				<label for="Page">Page: </label>
				<input type="text" id="Page" name="Page" class="form-control">
			</div>
			<div class="form-group">
				<label for="Width">Page Width: </label>
				<input type="text" id="Width" name="Width" class="form-control">
			</div>
			<div class="form-group">
				<label for="Height">Page Height: </label>
				<input type="text" id="Height" name="Height" class="form-control">
			</div></fieldset>
			<hr>


			<h4>프레임 정보</h4>
			<fieldset id="Frames-wrap">
				<div class="form-group">
					<textarea class="form-control" rows="3" id="Frames" name="Frames"></textarea>
					<input type="hidden" id="Frames-hidden" name="Frames-hidden" class="form-control">
				</div>

<!--				<div class="form-group">-->
<!--					<div class="input-group">-->
<!--						<span class="input-group-addon">1</span>-->
<!--						<input type="text" class="form-control" id="page-frame-1" name="page-frame-1">-->
<!--					</div>-->
<!--				</div>-->


				<button type="submit" class="btn btn-primary">페이지 프레임 저장</button>
			</fieldset>
		</form>
	</div>

</div>

<script>

	/*
	$("#form-book-action").submit(function(e) {
		e.preventDefault();
 					//showLoader();

//		console.log('=====>');
//		console.log($(this).serialize());

		$.ajax({
			type : "POST",
			url : "./ajax/book-action-map.php",
			data : $(this).serialize(),
			dataType : "json",
			success : function(res) {
				console.log(res);
				if (res.code == "0") {
					location.href = "./index.php";
				} else {
					//hideLoader();
					console.log(res.msg);
				}
			}
		});


	});
*/

</script>
</body></html>