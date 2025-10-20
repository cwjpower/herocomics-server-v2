<?php require __DIR__."/includes/common.php";
csrf_check($_POST["_csrf"] ?? "");
$pdo = db();
$id=(int)($_POST["id"]??0);
$st=$pdo->prepare("SELECT cover_path FROM series WHERE id=?"); $st->execute([$id]); $cover=$st->fetchColumn();
$pdo->prepare("DELETE FROM series WHERE id=?")->execute([$id]); // episodes는 FK CASCADE
if($cover && str_starts_with($cover,"/web/admin/uploads/covers/")){
  $fs="/var/www/html".$cover; if(is_file($fs)) @unlink($fs);
}
$seriesDir="/var/www/html/web/content/series/S".$id;
if(is_dir($seriesDir)){ exec("rm -rf ".escapeshellarg($seriesDir)); }
header("Location: series.php?msg=".urlencode("삭제 완료"));
