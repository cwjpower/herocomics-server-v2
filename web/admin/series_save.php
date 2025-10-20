<?php require __DIR__."/includes/common.php";
csrf_check($_POST["_csrf"] ?? "");
$pdo = db();

function save_cover(string $field): string {
  if(empty($_FILES[$field]) || $_FILES[$field]["error"]!==UPLOAD_ERR_OK){ return ""; }
  if($_FILES[$field]["size"] > 20*1024*1024){ throw new RuntimeException("표지 20MB 초과"); }
  $tmp = $_FILES[$field]["tmp_name"];
  $finfo = new finfo(FILEINFO_MIME_TYPE); $mime = $finfo->file($tmp);
  $map=["image/jpeg"=>"jpg","image/png"=>"png","image/webp"=>"webp","image/gif"=>"gif"];
  if(!isset($map[$mime])) throw new RuntimeException("허용되지 않는 이미지: $mime");
  $ext=$map[$mime];
  $dir="/var/www/html/web/admin/uploads/covers"; if(!is_dir($dir)) mkdir($dir,0777,true);
  $name="cover_".date("Ymd_His")."_".bin2hex(random_bytes(3)).".$ext";
  $dest="$dir/$name"; if(!move_uploaded_file($tmp,$dest)) throw new RuntimeException("파일 이동 실패");
  chmod($dest,0666);
  return "/web/admin/uploads/covers/$name";
}

$action = $_POST["action"] ?? "";
if($action==="create"){
  $title=trim((string)($_POST["title"]??""));
  if($title===""){ header("Location: series.php?msg=".urlencode("제목 필요")); exit; }
  $author=trim((string)($_POST["author"]??""));
  $desc=(string)($_POST["description"]??"");
  $act = isset($_POST["is_active"])?1:0;
  $cover = ""; try{ $cover=save_cover("cover"); }catch(Throwable $e){}
  $st=$pdo->prepare("INSERT INTO series(title,author,description,cover_path,is_active) VALUES(?,?,?,?,?)");
  $st->execute([$title,$author,$desc,$cover,$act]);
  header("Location: series.php?msg=".urlencode("등록 완료")); exit;
}
elseif($action==="update"){
  $id=(int)($_POST["id"]??0);
  $st=$pdo->prepare("SELECT cover_path FROM series WHERE id=?"); $st->execute([$id]); $old=$st->fetchColumn();
  $title=trim((string)($_POST["title"]??""));
  $author=trim((string)($_POST["author"]??""));
  $desc=(string)($_POST["description"]??"");
  $act = isset($_POST["is_active"])?1:0;
  $new=$old;
  if(!empty($_FILES["cover"]) && $_FILES["cover"]["error"]===UPLOAD_ERR_OK){
    $new=save_cover("cover");
    if($old && str_starts_with($old,"/web/admin/uploads/covers/")){
      $fs="/var/www/html".$old; if(is_file($fs)) @unlink($fs);
    }
  }
  $st=$pdo->prepare("UPDATE series SET title=?, author=?, description=?, cover_path=?, is_active=? WHERE id=?");
  $st->execute([$title,$author,$desc,$new,$act,$id]);
  header("Location: series.php?msg=".urlencode("수정 완료")); exit;
}
else{
  header("Location: series.php?msg=".urlencode("알 수 없는 동작")); exit;
}
