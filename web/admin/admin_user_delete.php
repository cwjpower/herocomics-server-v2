<?php require __DIR__."/includes/common.php";
csrf_check($_POST["_csrf"] ?? "");
$pdo = db();
$id = (int)($_POST["id"] ?? 0);
if ($id<=0) { header("Location: admin_users.php?msg=".urlencode("잘못된 ID")); exit; }
if ($id === (int)($_SESSION["admin_id"] ?? 0)) {
  header("Location: admin_users.php?msg=".urlencode("본인 계정은 삭제할 수 없습니다")); exit;
}
try{
  $pdo->prepare("DELETE FROM admin_users WHERE id=?")->execute([$id]);
  header("Location: admin_users.php?msg=".urlencode("삭제됨: ID $id")); exit;
}catch(Throwable $e){
  header("Location: admin_users.php?msg=".urlencode("삭제 실패: ".$e->getMessage())); exit;
}
