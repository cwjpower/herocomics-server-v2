<?php require __DIR__."/includes/common.php";
csrf_check($_POST["_csrf"] ?? "");
$pdo=db(); $id=(int)($_POST["id"]??0);
$st=$pdo->prepare("SELECT series_id, content_dir FROM episodes WHERE id=?"); $st->execute([$id]); $row=$st->fetch();
if($row){
  $pdo->prepare("DELETE FROM episodes WHERE id=?")->execute([$id]);
  $base="/var/www/html".$row["content_dir"]; if(is_dir($base)) exec("rm -rf ".escapeshellarg($base));
  header("Location: episodes.php?series_id=".$row["series_id"]."&msg=".urlencode("삭제 완료")); exit;
}
header("Location: series.php?msg=".urlencode("대상이 없음"));
