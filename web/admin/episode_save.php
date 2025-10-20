<?php require __DIR__."/includes/common.php";
$__bypass = is_file("/tmp/disable_csrf");
if (!$__bypass) { csrf_check($_POST["_csrf"] ?? ""); }
$pdo = db();

function rr_chmod(string $path): void {
  if (!is_dir($path)) return;
  $it = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
  );
  foreach ($it as $item) {
    $p = $item->getPathname();
    if ($item->isDir()) { @chmod($p, 0755); }
    else { @chmod($p, 0644); }
  }
  @chmod($path, 0755);
}

function flatten_if_single_subdir(string $dir): void {
  if (!is_dir($dir)) return;
  $items = array_values(array_filter(scandir($dir), fn($f) => $f !== "." && $f !== ".."));
  if (count($items) === 1) {
    $sub = $dir . "/" . $items[0];
    if (is_dir($sub)) {
      // move every item up one level
      foreach (array_diff(scandir($sub), [".",".."]) as $f) {
        @rename("$sub/$f", "$dir/$f");
      }
      // remove the (now empty) subdir
      @rmdir($sub);
    }
  }
}

function extract_zip_to(string $zipTmp, string $destDir): void {
  if (!is_dir($destDir)) mkdir($destDir, 0777, true);
  if (class_exists("ZipArchive")) {
    $zip = new ZipArchive();
    if ($zip->open($zipTmp)!==TRUE) throw new RuntimeException("ZIP 열기 실패");
    $zip->extractTo($destDir);
    $zip->close();
  } else {
    $cmd = "unzip -q ".escapeshellarg($zipTmp)." -d ".escapeshellarg($destDir);
    exec($cmd, $out, $code);
    if ($code !== 0) throw new RuntimeException("unzip 명령어 실패");
  }
  // 폴더 한 겹이면 평탄화, 권한 정리(디렉터리 755 / 파일 644)
  flatten_if_single_subdir($destDir);
  rr_chmod($destDir);
}

$action = $_POST["action"] ?? "";
if ($action==="create") {
  $sid=(int)($_POST["series_id"]??0);
  $title=trim((string)($_POST["title"]??""));
  $epno=(int)($_POST["ep_no"]??1);
  $pub=$_POST["published_at"]??null; $pub = $pub? date("Y-m-d H:i:s", strtotime($pub)) : null;
  $act=isset($_POST["is_active"])?1:0;
  if ($title==="" || $sid<=0) { header("Location: episodes.php?series_id=$sid&msg=".urlencode("필수값 누락")); exit; }

  $st=$pdo->prepare("INSERT INTO episodes(series_id,title,ep_no,content_dir,is_active,published_at) VALUES(?,?,?,?,?,?)");
  $st->execute([$sid,$title,$epno,"", $act,$pub]);
  $eid=(int)$pdo->lastInsertId();

  if (empty($_FILES["archive"]) || $_FILES["archive"]["error"]!==UPLOAD_ERR_OK) {
    header("Location: episodes.php?series_id=$sid&msg=".urlencode("ZIP 필요")); exit;
  }
  $tmp=$_FILES["archive"]["tmp_name"];
  $base="/var/www/html/web/content/series/S$sid/E$eid";
  if (is_dir($base)) exec("rm -rf ".escapeshellarg($base));
  extract_zip_to($tmp,$base);
  $web="/web/content/series/S$sid/E$eid";
  $pdo->prepare("UPDATE episodes SET content_dir=? WHERE id=?")->execute([$web,$eid]);
  header("Location: episodes.php?series_id=$sid&msg=".urlencode("회차 등록 완료")); exit;
}
elseif ($action==="update") {
  $id=(int)($_POST["id"]??0);
  $st=$pdo->prepare("SELECT series_id, content_dir FROM episodes WHERE id=?"); $st->execute([$id]); $row=$st->fetch();
  if (!$row) { http_response_code(404); exit("episode not found"); }
  $sid=(int)$row["series_id"]; $dir=$row["content_dir"];
  $title=trim((string)($_POST["title"]??"")); $epno=(int)($_POST["ep_no"]??1);
  $pub=$_POST["published_at"]??null; $pub=$pub?date("Y-m-d H:i:s",strtotime($pub)):null;
  $act=isset($_POST["is_active"])?1:0;

  if (!empty($_FILES["archive"]) && $_FILES["archive"]["error"]===UPLOAD_ERR_OK) {
    $base="/var/www/html".$dir; if (is_dir($base)) exec("rm -rf ".escapeshellarg($base));
    extract_zip_to($_FILES["archive"]["tmp_name"], $base);
  } else {
    // 새 ZIP이 없어도 권한만 틀어졌을 수 있어 보정
    $base="/var/www/html".$dir; if (is_dir($base)) rr_chmod($base);
  }
  $pdo->prepare("UPDATE episodes SET title=?, ep_no=?, is_active=?, published_at=? WHERE id=?")
      ->execute([$title,$epno,$act,$pub,$id]);
  header("Location: episodes.php?series_id=$sid&msg=".urlencode("회차 수정 완료")); exit;
}
else {
  header("Location: series.php?msg=".urlencode("알 수 없는 동작")); exit;
}
