<?php require __DIR__."/nav.php";
$pdo = db(); $sid=(int)($_GET["series_id"]??0);
$ser=$pdo->prepare("SELECT * FROM series WHERE id=?"); $ser->execute([$sid]); $series=$ser->fetch();
if(!$series){ http_response_code(404); exit("series not found"); }
$msg=$_GET["msg"]??"";
$rows=$pdo->prepare("SELECT * FROM episodes WHERE series_id=? ORDER BY ep_no ASC, id ASC"); $rows->execute([$sid]); $rows=$rows->fetchAll();
?>
<h2>회차 관리 - <?=h((string)$series["title"])?> (ID <?=h((string)$series["id"])?>)</h2>
<?php if($msg){ echo "<div class=\"alert\">".h($msg)."</div>"; } ?>

<h3>신규 회차 등록</h3>
<form method="post" action="episode_save.php" enctype="multipart/form-data" style="max-width:640px;">
  <input type="hidden" name="_csrf" value="<?=h(csrf_token())?>">
  <input type="hidden" name="action" value="create">
  <input type="hidden" name="series_id" value="<?=$series["id"]?>">
  <label>회차 제목<br><input name="title" required></label><br>
  <label>회차 번호(정렬용)<br><input name="ep_no" type="number" value="1"></label><br>
  <label>게시일(선택)<br><input name="published_at" type="datetime-local"></label><br>
  <label>활성화 <input type="checkbox" name="is_active" value="1" checked></label><br>
  <label>콘텐츠 ZIP(필수, 이미지들 압축)<br><input type="file" name="archive" accept=".zip,application/zip" required></label><br>
  <button>등록</button> <a href="series.php">작품 목록</a>
</form>

<h3 style="margin-top:22px;">회차 목록</h3>
<table>
  <thead><tr><th>ID</th><th>No</th><th>제목</th><th>게시일</th><th>활성</th><th>보기</th><th>작업</th></tr></thead>
  <tbody>
  <?php if($rows){ foreach($rows as $r){ ?>
    <tr>
      <td><?=h((string)$r["id"])?></td>
      <td><?=h((string)$r["ep_no"])?></td>
      <td><?=h((string)$r["title"])?></td>
      <td><?=h((string)$r["published_at"])?></td>
      <td><?=((int)$r["is_active"]? "Y":"N")?></td>
      <td><a target="_blank" href="episode_view.php?id=<?=h((string)$r["id"])?>">미리보기</a></td>
      <td>
        <a href="episode_edit.php?id=<?=h((string)$r["id"])?>">수정</a>
        <form method="post" action="episode_delete.php" style="display:inline;" onsubmit="return confirm(&quot;정말 삭제? (파일 포함)&quot;);">
          <input type="hidden" name="_csrf" value="<?=h(csrf_token())?>">
          <input type="hidden" name="id" value="<?=h((string)$r["id"])?>">
          <button>삭제</button>
        </form>
      </td>
    </tr>
  <?php }} else { ?>
    <tr><td colspan="7" style="text-align:center;color:#888;">회차가 없습니다</td></tr>
  <?php } ?>
  </tbody>
</table>
