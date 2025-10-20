<?php

/**
 * 액션뷰 정보 관리
 * fullscreen 화면으로 띄운다.
 */


require_once '../../wps-config.php';
require_once FUNC_PATH . '/functions-book.php';
require_once FUNC_PATH . '/functions-term.php';
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


$book_id = $_GET['book_id'];

$book_rows = lps_get_book($book_id);

$book_title = $book_rows['book_title'];
$author = $book_rows['author'];
$publisher = $book_rows['publisher'];
$isbn = $book_rows['isbn'];
$cover_img = $book_rows['cover_img'];

// 액션 뷰 등록 정보 조회
$book_action_rows = lps_get_book_action($book_id);
?>


<!doctype html>
<html>
<head>
	<title>액션뷰 정보 관리 </title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">
<!--	<link rel="stylesheet" href="--><?php //echo ADMIN_URL ?><!--/css/font-awesome.min.css">-->
	<script defer src="https://use.fontawesome.com/releases/v5.0.6/js/all.js"></script>
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
	<p class="pop-title">LOCAL IMAGE</p>
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
	<a id="gnb-title" href="book_action_map.php?book_id=<?php echo $book_id; ?>" onclick="if (!confirm('작업중인 정보가 보관되지 않습니다. 재설정 하시겠습니까?')) return false;">&#8635; 재설정</a>

	<!-- 드롭다운 메뉴 -->
	<ul id="gnb-menu">
		<li id="gnb-menu-source">
			<span> 이미지 로드 &#9662;</span>
			<ul class="gnb-menu-sub">
				<li id="gnb-menu-local">내컴퓨터</li>
				<li id="gnb-menu-url">URL</li>
			</ul>
		</li>
		<li id="gnb-menu-measure">
			<span>추출방법 &#9662;</span>
			<ul class="gnb-menu-sub _toggle">
				<li id="gnb-menu-drag" class="_active">DRAG<em>&nbsp;&#10003;</em></li>
				<li id="gnb-menu-click">CLICK<em>&nbsp;&#10003;</em></li>
			</ul>
		</li>
		<li id="gnb-menu-unit">
			<span>단위 &#9662;</span>
			<ul class="gnb-menu-sub _toggle">
				<li id="gnb-menu-pixel" class="_active">PX<em>&nbsp;&#10003;</em></li>
				<li id="gnb-menu-percent">%<em>&nbsp;&#10003;</em></li>
			</ul>
		</li>
<!--		<li id="gnb-menu-clear">-->
<!--			<span>재설정</span>-->
<!--		</li>-->
<!--		<li id="gnb-menu-generate">-->
<!--			<span>액션뷰 코드 생성</span>-->
<!--		</li>-->
<!--		<li id="gnb-menu-info">-->
<!--			<span>?</span>-->
<!--		</li>-->
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
	<div class="row" style="margin-top:20px;">
		<div class="col-lg-12">
			<h4><i class="fa fa-book"></i> 책 정보</h4>
			<div class="media">
				<div class="media-left">
					<a href="#">
						<img class="media-object" src="<?php echo $cover_img ?>" alt="cover img" style="height:100px;">
					</a>
				</div>
				<div class="media-body">
					<h4 class="media-heading"><?php echo $book_title;?></h4>
					<?php echo $author;?><br>
					<?php echo $publisher;?><br>
					<?php echo $isbn;?>
				</div>
			</div>
			<hr>
		</div>
	</div>
	<div class="map-info-fields row" style="">
		<div class="col-lg-6" style="overflow-y: auto;height: 500px">
		<form id="form-book-action" class="form-horizontal" action="./ajax/book-action-map.php" method="POST">
			<h4 class="" style="margin-bottom:2rem;"><i class="fas fa-file"></i> 페이지 기본 정보 - 1015 * 1560 (기본설정) </h4>
			<fieldset id="page-info">

				<div class="form-group">
					<label for="Page" class="col-sm-2 control-label" style="text-align: left">페이지 </label>
					<div class="col-sm-10">
						<input type="text" id="Page" name="Page" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<label for="Width" class="col-sm-2 control-label"  style="text-align: left">가로 </label>
					<div class="col-sm-10">
						<input type="text" id="Width" name="Width" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<label for="Height" class="col-sm-2 control-label"  style="text-align: left">세로 </label>
					<div class="col-sm-10">
						<input type="text" id="Height" name="Height" class="form-control">
					</div>

				</div>
			</fieldset>
			<hr>
			<h4> <i class="fas fa-th-large"></i> 프레임 정보 </h4>
			<div class="form-menu" style="margin-bottom:68px;">
				<button type="button" id='form-code-generate' class="btn btn-primary" style="float:left">패널정보 생성</button>
				<!--				<button type="reset" class="btn btn-primary form-menu" style="float:left; margin-left:0.5rem">재설정</button>-->
				<a id="" class="btn btn-primary form-menu" href="book_action_map.php?book_id=<?php echo $book_id; ?>" style="color:white;float:left; margin-left:0.5rem" onclick="if (!confirm('작업중인 정보가 보관되지 않습니다. 재설정 하시겠습니까?')) return false;">&#8635; 재설정</a>
				<button type="submit" class="btn btn-primary" style="float:left; margin-left:2rem">패널정보 저장</button>
				<a href="./ajax/book-action-download.php?book_id=<?=$book_id?>" class="btn btn-primary" style="float:left; margin-left:2rem">패널정보 다운로드</a>
			</div>

			<fieldset id="Frames-wrap" class="clearfix" style="clear:both;">
				<input type="hidden" id="book-id" name="book-id" value= "<?php echo $book_id;?>">
<!--				<div class="form-group">-->
<!--					<textarea class="form-control" rows="3" id="Frames" name="Frames"></textarea>-->
<!--					<input type="hidden" id="Frames-hidden" name="Frames-hidden" class="form-control">-->
<!--				</div>-->


				<!--				<div class="form-group">-->
<!--					<div class="input-group">-->
<!--						<span class="input-group-addon">1</span>-->
<!--						<input type="text" class="form-control" id="page-frame-1" name="page-frame-1">-->
<!--					</div>-->
<!--				</div>-->
			</fieldset>

		</form>

	</div>
	<div class="col-lg-3">
		<h4 style="margin-bottom: 2rem;"><i class="far fa-file"></i> 등록된 페이지 </h4>
		<div class="list-group" style="overflow-y: auto;height: 700px">
			<?php
			foreach ($book_action_rows as $action_row) :  ?>
				<?php $pannel_cnt = count(json_decode($action_row['pannel_info'], true)['Frames']); ?>
				<a href="#" class="list-group-item"><?php echo $action_row['page_no']; ?><span class="badge"><?php echo $pannel_cnt; ?></span></a>
			<?php endforeach; ?>
		</div>
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