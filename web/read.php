<?php require __DIR__."/admin/includes/common.php"; $pdo=db();
$eid=(int)($_GET["id"]??0);
$st=$pdo->prepare("SELECT e.*, s.title AS series_title FROM episodes e JOIN series s ON s.id=e.series_id WHERE e.id=? AND e.is_active=1 AND (e.published_at IS NULL OR e.published_at<=NOW())");
$st->execute([$eid]); $ep=$st->fetch(); if(!$ep){ http_response_code(404); exit("Episode not found"); }
$sid=(int)$ep["series_id"]; $epno=(int)$ep["ep_no"];
$prev=$pdo->prepare("SELECT id FROM episodes WHERE series_id=? AND is_active=1 AND (published_at IS NULL OR published_at<=NOW()) AND (ep_no < ? OR (ep_no=? AND id < ?)) ORDER BY ep_no DESC, id DESC LIMIT 1");
$prev->execute([$sid,$epno,$epno,$eid]); $prev=$prev->fetchColumn();
$next=$pdo->prepare("SELECT id FROM episodes WHERE series_id=? AND is_active=1 AND (published_at IS NULL OR published_at<=NOW()) AND (ep_no > ? OR (ep_no=? AND id > ?)) ORDER BY ep_no ASC, id ASC LIMIT 1");
$next->execute([$sid,$epno,$epno,$eid]); $next=$next->fetchColumn();
$dir="/var/www/html".$ep["content_dir"];
$imgs=[];
if(is_dir($dir)){
  $all=scandir($dir);
  foreach($all as $f){ if($f==="."||$f==="..") continue; $p="$dir/$f";
    if(is_file($p) && preg_match("/\\.(jpe?g|png|webp|gif)$/i",$f)) $imgs[]=$f;
  }
  sort($imgs, SORT_NATURAL|SORT_FLAG_CASE);
}
?><!doctype html><meta charset="utf-8"><title><?=h((string)$ep["series_title"])?> - #<?=h((string)$ep["ep_no"])?> <?=h((string)$ep["title"])?> | HeroComics</title>
<style>
*{box-sizing:border-box} body{margin:0;font-family:system-ui,Segoe UI,Roboto,Arial,sans-serif;background:#111;color:#eee}
.topbar{position:sticky;top:0;background:rgba(0,0,0,.8);backdrop-filter:saturate(1.2) blur(6px);padding:10px 14px;display:flex;gap:10px;align-items:center;z-index:10}
.topbar a{color:#fff;text-decoration:none;opacity:.9} .topbar a:hover{opacity:1}
.btn{padding:6px 10px;border:1px solid #666;border-radius:10px;background:#222;color:#eee;text-decoration:none}
.btn[disabled]{opacity:.5;pointer-events:none}
.wrap{max-width:980px;margin:0 auto;padding:10px}
.page img{width:100%;height:auto;border-radius:6px;background:#222;border:1px solid #222;margin:6px 0}
.footer{display:flex;justify-content:space-between;gap:8px;margin:20px 0}
.progress{height:4px;background:#333;margin:0;position:sticky;top:48px}
.progress>div{height:4px;background:#4da3ff;width:0%}
</style>
<div class="topbar">
  <a href="/web/series_view.php?sid=<?=$sid?>">← <?=h((string)$ep["series_title"])?></a>
  <span>#<?=h((string)$ep["ep_no"])?> <?=h((string)$ep["title"])?></span>
  <div style="flex:1"></div>
  <a class="btn" href="/web/read.php?id=<?=$prev?>" <?= $prev? "" : "disabled" ?>>이전</a>
  <a class="btn" href="/web/read.php?id=<?=$next?>" <?= $next? "" : "disabled" ?>>다음</a>
</div>
<div class="progress"><div id="bar"></div></div>
<div class="wrap" id="wrap">
  <?php if($imgs){ foreach($imgs as $f){ $src=$ep["content_dir"]."/".$f; ?>
    <div class="page"><img loading="lazy" src="<?=h($src)?>"></div>
  <?php }} else { echo "<p style='padding:20px;color:#bbb'>이미지가 없습니다.</p>"; } ?>
  <div class="footer">
    <a class="btn" href="/web/read.php?id=<?=$prev?>" <?= $prev? "" : "disabled" ?>>← 이전 회차</a>
    <a class="btn" href="/web/series_view.php?sid=<?=$sid?>">목록</a>
    <a class="btn" href="/web/read.php?id=<?=$next?>" <?= $next? "" : "disabled" ?>>다음 회차 →</a>
  </div>
</div>
<script>
document.addEventListener("keydown",e=>{
  if(e.key==="ArrowRight"||e.key==="d"){ const n=document.querySelector(".footer a.btn:last-child"); if(n && !n.hasAttribute("disabled")) location.href=n.href; }
  if(e.key==="ArrowLeft"||e.key==="a"){ const p=document.querySelector(".footer a.btn:first-child"); if(p && !p.hasAttribute("disabled")) location.href=p.href; }
});
const bar=document.getElementById("bar");
document.addEventListener("scroll",()=>{
  const el=document.getElementById("wrap");
  const h=el.scrollHeight - window.innerHeight; const y=window.scrollY; const p = Math.max(0, Math.min(100, (y/h)*100));
  bar.style.width=p+"%";
},{passive:true});
</script>
