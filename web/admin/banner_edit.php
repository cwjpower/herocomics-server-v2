<?php require __DIR__."/nav.php";
$pdo = db();
$id = (int)($_GET["id"] ?? 0);
$st = $pdo->prepare("SELECT * FROM banners WHERE id=?");
$st->execute([$id]);
$it = $st->fetch();
if(!$it){ http_response_code(404); exit("not found"); }
?>
<h2>배너 수정</h2>
<form method="post" action="banner_save.php" enctype="multipart/form-data" style="max-width:560px;">
  <input type="hidden" name="_csrf" value="<?=h(csrf_token())?>">
  <input type="hidden" name="action" value="update">
  <input type="hidden" name="id" value="<?=h((string)$it["id"])?>">
  <label>제목<br><input name="title" required value="<?=h((string)$it["title"])?>"></label><br>
  <label>링크 URL(선택)<br><input name="link_url" value="<?=h((string)$it["link_url"])?>"></label><br>
  <label>정렬순서<br><input name="sort_order" type="number" value="<?=h((string)$it["sort_order"])?>"></label><br>
  <label>활성화 <input type="checkbox" name="is_active" value="1" <?=((int)$it["is_active"]? "checked":"")?>></label><br>
  <div>현재 이미지:<br><?php if($it["image_path"]){ ?><img class="imgx" src="<?=h($it["image_path"])?>"><?php } ?></div>
  <label>이미지 교체(선택)<br><input type="file" name="image" accept="image/*"></label><br>
  <button>저장</button> <a href="banners.php">목록</a>
</form>
