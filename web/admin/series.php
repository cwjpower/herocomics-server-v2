<?php require __DIR__."/nav.php";
$pdo = db(); $msg = $_GET["msg"] ?? "";
$rows = $pdo->query("SELECT * FROM series ORDER BY is_active DESC, id DESC")->fetchAll();
?>
<h2>작품</h2>
<?php if($msg){ echo "<div class=\"alert\">".h($msg)."</div>"; } ?>

<h3>신규 작품 등록</h3>
<form method="post" action="series_save.php" enctype="multipart/form-data" style="max-width:640px;">
  <input type="hidden" name="_csrf" value="<?=h(csrf_token())?>">
  <input type="hidden" name="action" value="create">
  <label>제목<br><input name="title" required></label><br>
  <label>작가(선택)<br><input name="author"></label><br>
  <label>설명(선택)<br><textarea name="description" rows="4" style="width:100%;"></textarea></label><br>
  <label>활성화 <input type="checkbox" name="is_active" value="1" checked></label><br>
  <label>표지 이미지(선택, jpg/png/webp/gif)<br><input type="file" name="cover" accept="image/*"></label><br>
  <button>등록</button>
</form>

<h3 style="margin-top:22px;">작품 목록</h3>
<table>
  <thead><tr><th>ID</th><th>표지</th><th>제목</th><th>작가</th><th>활성</th><th>생성일</th><th>작업</th></tr></thead>
  <tbody>
  <?php if($rows){ foreach($rows as $r){ ?>
    <tr>
      <td><?=h((string)$r["id"])?></td>
      <td><?php if($r["cover_path"]){ ?><img class="imgx" src="<?=h($r["cover_path"])?>"><?php } ?></td>
      <td><?=h((string)$r["title"])?></td>
      <td><?=h((string)$r["author"])?></td>
      <td><?=((int)$r["is_active"] ? "Y":"N")?></td>
      <td><?=h((string)$r["created_at"])?></td>
      <td>
        <a href="series_edit.php?id=<?=h((string)$r["id"])?>">수정</a>
        &nbsp;|&nbsp;
        <a href="episodes.php?series_id=<?=h((string)$r["id"])?>">회차 관리</a>
        <form method="post" action="series_delete.php" style="display:inline;" onsubmit="return confirm(&quot;정말 삭제? (회차/파일 포함)&quot;);">
          <input type="hidden" name="_csrf" value="<?=h(csrf_token())?>">
          <input type="hidden" name="id" value="<?=h((string)$r["id"])?>">
          <button>삭제</button>
        </form>
      </td>
    </tr>
  <?php }} else { ?>
    <tr><td colspan="7" style="text-align:center;color:#888;">작품이 없습니다</td></tr>
  <?php } ?>
  </tbody>
</table>
