<?php require __DIR__."/includes/common.php";
csrf_check($_POST["_csrf"] ?? "");
$pdo = db();

function save_image(string $field): string {
  if(empty($_FILES[$field]) || $_FILES[$field]["error"]!==UPLOAD_ERR_OK){
    throw new RuntimeException("이미지 업로드 오류");
  }
  if($_FILES[$field]["size"] > 10*1024*1024){
    throw new RuntimeException("이미지 크기 초과(10MB)");
  }
  $tmp = $_FILES[$field]["tmp_name"];
  $finfo = new finfo(FILEINFO_MIME_TYPE);
  $mime = $finfo->file($tmp);
  $map = ["image/jpeg"=>"jpg","image/png"=>"png","image/webp"=>"webp","image/gif"=>"gif"];
  if(!isset($map[$mime])){ throw new RuntimeException("이미지 형식 불가: $mime"); }
  $ext = $map[$mime];
  $dir = "/var/www/html/web/admin/uploads/banners";
  if(!is_dir($dir)) mkdir($dir,0777,true);
  $name = "banner_".date("Ymd_His")."_".bin2hex(random_bytes(3)).".$ext";
  $dest = "$dir/$name";
  if(!move_uploaded_file($tmp, $dest)){ throw new RuntimeException("파일 이동 실패"); }
  chmod($dest, 0666);
  return "/web/admin/uploads/banners/$name"; // 웹 경로 저장
}

$action = $_POST["action"] ?? "";
if($action==="create"){
  $title = trim((string)($_POST["title"] ?? ""));
  if($title===""){ header("Location: banners.php?msg=".urlencode("제목 필요")); exit; }
  $img = save_image("image");
  $link = trim((string)($_POST["link_url"] ?? ""));
  $sort = (int)($_POST["sort_order"] ?? 0);
  $act = isset($_POST["is_active"]) ? 1 : 0;
  $st = $pdo->prepare("INSERT INTO banners(title,link_url,image_path,sort_order,is_active) VALUES(?,?,?,?,?)");
  $st->execute([$title,$link,$img,$sort,$act]);
  header("Location: banners.php?msg=".urlencode("등록 완료")); exit;
}
elseif($action==="update"){
  $id = (int)($_POST["id"] ?? 0);
  $st = $pdo->prepare("SELECT image_path FROM banners WHERE id=?");
  $st->execute([$id]);
  $old = $st->fetchColumn();
  if(!$old){ header("Location: banners.php?msg=".urlencode("대상이 없음")); exit; }
  $title = trim((string)($_POST["title"] ?? ""));
  $link = trim((string)($_POST["link_url"] ?? ""));
  $sort = (int)($_POST["sort_order"] ?? 0);
  $act = isset($_POST["is_active"]) ? 1 : 0;

  $newPath = $old;
  if(!empty($_FILES["image"]) && $_FILES["image"]["error"]===UPLOAD_ERR_OK){
    $newPath = save_image("image");
    // 이전 파일 정리
    if(str_starts_with($old, "/web/admin/uploads/banners/")){
      $oldFs = "/var/www/html".$old;
      if(is_file($oldFs)) @unlink($oldFs);
    }
  }
  $st = $pdo->prepare("UPDATE banners SET title=?, link_url=?, image_path=?, sort_order=?, is_active=? WHERE id=?");
  $st->execute([$title,$link,$newPath,$sort,$act,$id]);
  header("Location: banners.php?msg=".urlencode("수정 완료")); exit;
}
else{
  header("Location: banners.php?msg=".urlencode("알 수 없는 동작")); exit;
}
