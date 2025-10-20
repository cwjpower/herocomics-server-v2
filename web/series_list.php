<?php require __DIR__."/admin/includes/common.php"; $pdo=db();
$rows=$pdo->query("SELECT id,title,author,cover_path,created_at FROM series WHERE is_active=1 ORDER BY id DESC")->fetchAll();
?><!doctype html><meta charset="utf-8"><title>작품 목록 - HeroComics</title>
<style>
*{box-sizing:border-box} body{font-family:system-ui,Segoe UI,Roboto,Arial,sans-serif;margin:0;background:#fafafa}
.header{padding:16px 20px;background:#111;color:#fff;display:flex;gap:12px;align-items:center}
.header a{color:#fff;text-decoration:none;opacity:.9} .header a:hover{opacity:1}
.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:16px;padding:20px}
.card{background:#fff;border-radius:14px;box-shadow:0 2px 10px rgba(0,0,0,.06);overflow:hidden;display:flex;flex-direction:column}
.card img{width:100%;height:260px;object-fit:cover;background:#eee}
.card .body{padding:12px}
.card .title{font-weight:700}
.card .meta{color:#666;font-size:13px;margin-top:4px}
</style>
<div class="header">
  <div style="font-weight:700">HeroComics</div>
  <a href="/web/index.php">홈</a>
  <a href="/web/admin/login.php">관리자</a>
</div>
<div class="grid">
<?php if($rows){ foreach($rows as $r){ $sid=(int)$r["id"]; ?>
  <a class="card" href="/web/series_view.php?sid=<?=$sid?>">
    <?php if($r["cover_path"]){ ?><img src="<?=h((string)$r["cover_path"])?>"><?php } else { ?>
      <img src="data:image/svg+xml;charset=utf-8,<?=urlencode("<svg xmlns='http://www.w3.org/2000/svg' width='800' height='600'><rect width='100%' height='100%' fill='%23ddd'/><text x='50%' y='50%' dominant-baseline='middle' text-anchor='middle' fill='%23666' font-family='Arial' font-size='24'>No Cover</text></svg>")?>"><?php } ?>
    <div class="body">
      <div class="title"><?=h((string)$r["title"])?></div>
      <div class="meta"><?=h((string)$r["author"] ?? "")?></div>
    </div>
  </a>
<?php }} else { echo "<p style='padding:20px;color:#666'>작품이 없습니다</p>"; } ?>
</div>
