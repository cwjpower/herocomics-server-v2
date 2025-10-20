<?php require __DIR__."/nav.php";
$pdo = db(); $id=(int)($_GET["id"]??0);
$st=$pdo->prepare("SELECT * FROM series WHERE id=?"); $st->execute([$id]); $it=$st->fetch();
if(!$it){ http_response_code(404); exit("not found"); }
?>
<h2>작품 수정</h2>
<form method="post" action="series_save.php" enctype="multipart/form-data" style="max-width:640px;">
  <input type="hidden" name="_csrf" value="<?=h(csrf_token())?>">
  <input type="hidden" name="action" value="update">
  <input type="hidden" name="id" value="<?=h((string)$it["id"])?>">
  <label>제목<br><input name="title" required value="<?=h((string)$it["title"])?>"></label><br>
  <label>작가<br><input name="author" value="<?=h((string)$it["author"])?>"></label><br>
  <label>설명<br><textarea name="description" rows="4" style="width:100%;"><?=h((string)$it["description"])?></textarea></label><br>
  <label>활성화 <input type="checkbox" name="is_active" value="1" <?=((int)$it["is_active"]?"checked":"")?>></label><br>
  <div>현재 표지:<br><?php if($it["cover_path"]){ ?><img class="imgx" src="<?=h($it["cover_path"])?>"><?php } ?></div>
  <label>표지 교체(선택)<br><input type="file" name="cover" accept="image/*"></label><br>
  <button>저장</button> <a href="series.php">목록</a>
</form>
