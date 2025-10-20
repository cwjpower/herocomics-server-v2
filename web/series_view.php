<?php require __DIR__."/admin/includes/common.php"; $pdo=db();
$sid=(int)($_GET["sid"]??0);
$st=$pdo->prepare("SELECT * FROM series WHERE id=? AND is_active=1"); $st->execute([$sid]); $s=$st->fetch();
if(!$s){ http_response_code(404); exit("Series not found"); }
$eps=$pdo->prepare("SELECT id,title,ep_no,published_at FROM episodes WHERE series_id=? AND is_active=1 AND (published_at IS NULL OR published_at<=NOW()) ORDER BY ep_no ASC, id ASC");
$eps->execute([$sid]); $eps=$eps->fetchAll();
?><!doctype html><meta charset="utf-8"><title><?=h((string)$s["title"])?> - HeroComics</title>
<style>
body{font-family:system-ui,Segoe UI,Roboto,Arial,sans-serif;margin:0;background:#fafafa}
.header{padding:16px 20px;background:#111;color:#fff;display:flex;gap:12px;align-items:center}
.header a{color:#fff;text-decoration:none;opacity:.9} .header a:hover{opacity:1}
.wrap{max-width:1000px;margin:20px auto;background:#fff;border-radius:14px;box-shadow:0 2px 10px rgba(0,0,0,.06);overflow:hidden}
.top{display:flex;gap:16px;padding:16px}
.top img{width:180px;height:240px;object-fit:cover;border-radius:10px;border:1px solid #ddd;background:#eee}
.meta{color:#666}
.list{padding:12px 16px;border-top:1px solid #eee}
.ep{display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid #f2f2f2}
.ep a{text-decoration:none;color:#111}
.badge{font-size:12px;color:#555}
</style>
<div class="header">
  <a href="/web/series_list.php">← 작품 목록</a>
  <a href="/web/index.php">홈</a>
</div>
<div class="wrap">
  <div class="top">
    <div><?php if($s["cover_path"]){ ?><img src="<?=h((string)$s["cover_path"])?>"><?php } ?></div>
    <div>
      <h2 style="margin:4px 0 6px"><?=h((string)$s["title"])?></h2>
      <div class="meta">작가: <?=h((string)$s["author"] ?? "")?></div>
      <?php if($s["description"]){ ?><p style="margin-top:10px;white-space:pre-line"><?=h((string)$s["description"])?></p><?php } ?>
      <p class="meta">작품 ID: <?=$sid?> · 회차 <?=count($eps)?>개</p>
      <?php if($eps){ $first=(int)$eps[0]["id"]; ?><p><a href="/web/read.php?id=<?=$first?>">▶ 첫 회 보기</a></p><?php } ?>
    </div>
  </div>
  <div class="list">
    <?php if($eps){ foreach($eps as $e){ $eid=(int)$e["id"]; ?>
      <div class="ep">
        <a href="/web/read.php?id=<?=$eid?>">#<?=h((string)$e["ep_no"])?> <?=h((string)$e["title"])?></a>
        <span class="badge"><?=h((string)$e["published_at"] ?? "")?></span>
      </div>
    <?php }} else { echo "<p style='color:#666;padding:16px'>공개된 회차가 없습니다.</p>"; } ?>
  </div>
</div>
