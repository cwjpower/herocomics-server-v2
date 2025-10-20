<?php require __DIR__."/includes/common.php";
csrf_check($_POST["_csrf"] ?? "");
$pdo = db();
$id = (int)($_POST["id"] ?? 0);
$st = $pdo->prepare("SELECT image_path FROM banners WHERE id=?");
$st->execute([$id]);
$img = $st->fetchColumn();
$pdo->prepare("DELETE FROM banners WHERE id=?")->execute([$id]);
if($img && str_starts_with($img, "/web/admin/uploads/banners/")){
  $fs = "/var/www/html".$img;
  if(is_file($fs)) @unlink($fs);
}
header("Location: banners.php?msg=".urlencode("삭제 완료"));
