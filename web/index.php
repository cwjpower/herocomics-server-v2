<?php
require __DIR__."/admin/includes/common.php";
?>
<!doctype html><meta charset="utf-8">
<title>HeroComics</title>
<div style="font-family:system-ui,Segoe UI,Roboto,Helvetica,Arial,sans-serif;padding:24px">
  <h1>HeroComics 홈</h1>
  <p><a href="/web/admin/login.php">관리자 로그인</a> | <a href="/web/admin/series.php">작품 관리</a></p>
  <hr>
  <h3>작품 목록</h3>
  <?php
    $pdo = db();
    $rows = $pdo->query("SELECT id,title,author,cover_path FROM series WHERE is_active=1 ORDER BY id DESC")->fetchAll();
    if ($rows) {
      echo "<ul>";
      foreach($rows as $r){
        $t = h((string)$r["title"]);
        $a = h((string)$r["author"]);
        $c = h((string)$r["cover_path"]);
        $id = (int)$r["id"];
        echo "<li style=\"margin:6px 0\">";
        if ($c) echo "<img src=\"".h($c)."\" style=\"height:40px;vertical-align:middle;margin-right:8px;border:1px solid #ddd;border-radius:4px\">";
        echo "<strong>{$t}</strong>";
        if ($a) echo " <span style=\"color:#666\">/ {$a}</span>";
        echo " — <a href=\"/web/admin/episodes.php?series_id={$id}\">회차 관리</a>";
        echo "</li>";
      }
      echo "</ul>";
    } else {
      echo "<p style=\"color:#666\">등록된 작품이 없습니다.</p>";
    }
  ?>
</div>
