<?php require __DIR__."/nav.php";
$pdo = db();
$msg = $_GET["msg"] ?? "";
$rows = $pdo->query("SELECT * FROM banners ORDER BY is_active DESC, sort_order ASC, id DESC")->fetchAll();
?>
<h2>배너</h2>
<?php if($msg){ echo "<div class=\"alert\">".h($msg)."</div>"; } ?>

<h3>신규 배너 등록</h3>
<form method="post" action="banner_save.php" enctype="multipart/form-data" style="max-width:560px;">
  <input type="hidden" name="_csrf" value="<?=h(csrf_token())?>">
  <input type="hidden" name="action" value="create">
  <label>제목<br><input name="title" required placeholder="배너 제목"></label><br>
  <label>링크 URL(선택)<br><input name="link_url" placeholder="https://..."></label><br>
  <label>정렬순서(낮을수록 먼저)<br><input name="sort_order" type="number" value="0"></label><br>
  <label>활성화 <input type="checkbox" name="is_active" value="1" checked></label><br>
  <label>이미지(필수, jpg/png/webp/gif, ≤10MB)<br><input type="file" name="image" accept="image/*" required></label><br>
  <button>등록</button>
</form>

<h3 style="margin-top:22px;">배너 목록</h3>
<table>
  <thead><tr><th>ID</th><th>이미지</th><th>제목</th><th>링크</th><th>순서</th><th>활성</th><th>생성일</th><th>작업</th></tr></thead>
  <tbody>
  <?php if($rows){ foreach($rows as $r){ ?>
    <tr>
      <td><?=h((string)$r["id"])?></td>
      <td><?php if($r["image_path"]){ ?><img class="imgx" src="<?=h($r["image_path"])?>"><?php } ?></td>
      <td><?=h((string)$r["title"])?></td>
      <td style="max-width:320px;word-break:break-all;"><a target="_blank" href="<?=h((string)$r["link_url"])?>"><?=h((string)$r["link_url"])?></a></td>
      <td><?=h((string)$r["sort_order"])?></td>
      <td><?= ((int)$r["is_active"]? "Y":"N") ?></td>
      <td><?=h((string)$r["created_at"])?></td>
      <td>
        <a href="banner_edit.php?id=<?=h((string)$r["id"])?>">수정</a>
        <form method="post" action="banner_delete.php" style="display:inline;" onsubmit="return confirm(\"정말 삭제할까요?\");">
          <input type="hidden" name="_csrf" value="<?=h(csrf_token())?>">
          <input type="hidden" name="id" value="<?=h((string)$r["id"])?>">
          <button>삭제</button>
        </form>
      </td>
    </tr>
  <?php }} else { ?>
    <tr><td colspan="8" style="text-align:center;color:#888;">배너가 없습니다</td></tr>
  <?php } ?>
  </tbody>
</table>
