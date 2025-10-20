<?php require __DIR__."/nav.php";
$pdo=db(); $id=(int)($_GET["id"]??0);
$st=$pdo->prepare("SELECT e.*, s.title AS series_title FROM episodes e JOIN series s ON s.id=e.series_id WHERE e.id=?");
$st->execute([$id]); $it=$st->fetch(); if(!$it){ http_response_code(404); exit("not found"); }
?>
<h2>회차 수정 - <?=h((string)$it["series_title"])?> / #<?=h((string)$it["ep_no"])?> <?=h((string)$it["title"])?></h2>
<form method="post" action="episode_save.php" enctype="multipart/form-data" style="max-width:640px;">
  <input type="hidden" name="_csrf" value="<?=h(csrf_token())?>">
  <input type="hidden" name="action" value="update">
  <input type="hidden" name="id" value="<?=h((string)$it["id"])?>">
  <label>회차 제목<br><input name="title" required value="<?=h((string)$it["title"])?>"></label><br>
  <label>회차 번호<br><input name="ep_no" type="number" value="<?=h((string)$it["ep_no"])?>"></label><br>
  <label>게시일<br><input name="published_at" type="datetime-local" value="<?php if($it["published_at"]) echo date("Y-m-d\TH:i", strtotime($it["published_at"])); ?>"></label><br>
  <label>활성화 <input type="checkbox" name="is_active" value="1" <?=((int)$it["is_active"]?"checked":"")?>></label><br>
  <div>콘텐츠 경로: <?=h((string)$it["content_dir"])?></div>
  <label>콘텐츠 ZIP 교체(선택)<br><input type="file" name="archive" accept=".zip,application/zip"></label><br>
  <button>저장</button> <a href="episodes.php?series_id=<?=h((string)$it["series_id"])?>">회차 목록</a>
</form>
