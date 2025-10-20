<?php require __DIR__."/nav.php";
$pdo=db(); $id=(int)($_GET["id"]??0);
$st=$pdo->prepare("SELECT e.*, s.title AS series_title FROM episodes e JOIN series s ON s.id=e.series_id WHERE e.id=?");
$st->execute([$id]); $it=$st->fetch(); if(!$it){ http_response_code(404); exit("not found"); }
$dir="/var/www/html".$it["content_dir"];
$imgs=[];
if(is_dir($dir)){
  $all=scandir($dir);
  foreach($all as $f){
    if($f==="."||$f==="..") continue;
    $p="$dir/$f";
    if(is_file($p) && preg_match("/\\.(jpe?g|png|webp|gif)$/i",$f)) $imgs[]=$f;
  }
  sort($imgs, SORT_NATURAL);
}
?>
<h2>미리보기 - <?=h((string)$it["series_title"])?> / #<?=h((string)$it["ep_no"])?> <?=h((string)$it["title"])?></h2>
<p>경로: <?=h((string)$it["content_dir"])?> (이미지 <?=count($imgs)?>개)</p>
<?php foreach($imgs as $f): ?>
  <div><img style="max-width:900px;width:100%;height:auto;border:1px solid #eee;margin:6px 0" src="<?=h($it["content_dir"]."/".$f)?>"></div>
<?php endforeach; if(!$imgs): ?>
  <p style="color:#888;">이미지 없음</p>
<?php endif; ?>
