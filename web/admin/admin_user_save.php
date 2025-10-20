<?php require __DIR__."/includes/common.php";
csrf_check($_POST["_csrf"] ?? "");
$pdo = db();

function rand_pwd(int $n=16): string {
  $c="ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!@#\$%^&*";
  $o=""; for($i=0;$i<$n;$i++){ $o.=$c[random_int(0,strlen($c)-1)]; } return $o;
}

$action = $_POST["action"] ?? "";
if ($action === "create") {
  $u = trim((string)($_POST["username"] ?? ""));
  $pwd = (string)($_POST["password"] ?? "");
  if ($u === "") { header("Location: admin_users.php?msg=".urlencode("아이디가 비었습니다")); exit; }
  if ($pwd === "") $pwd = rand_pwd();
  $h = password_hash($pwd, PASSWORD_BCRYPT);
  try{
    $st = $pdo->prepare("INSERT INTO admin_users(username,password_hash) VALUES(?,?)");
    $st->execute([$u,$h]);
    header("Location: admin_users.php?msg=".urlencode("추가됨: $u / 초기비번: $pwd")); exit;
  }catch(Throwable $e){
    header("Location: admin_users.php?msg=".urlencode("추가 실패: ".$e->getMessage())); exit;
  }
}
elseif ($action === "reset") {
  $id = (int)($_POST["id"] ?? 0);
  if ($id<=0) { header("Location: admin_users.php?msg=".urlencode("잘못된 ID")); exit; }
  $pwd = rand_pwd();
  $h = password_hash($pwd, PASSWORD_BCRYPT);
  $st = $pdo->prepare("UPDATE admin_users SET password_hash=? WHERE id=?");
  $st->execute([$h,$id]);
  header("Location: admin_users.php?msg=".urlencode("ID $id 비번 재설정: $pwd")); exit;
}
else {
  header("Location: admin_users.php?msg=".urlencode("알 수 없는 동작")); exit;
}
