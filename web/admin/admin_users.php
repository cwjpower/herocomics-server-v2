<?php
require __DIR__."/nav.php";
$pdo = db();
$rows = $pdo->query("SELECT id, username, created_at FROM admin_users ORDER BY id")->fetchAll();
$msg = $_GET["msg"] ?? "";
?>
<h2>관리자 계정</h2>
<?php if ($msg !== "") { echo "<div class=\"alert\">".h($msg)."</div>"; } ?>

<h3>신규 추가</h3>
<form method="post" action="admin_user_save.php" style="max-width:480px;">
  <input type="hidden" name="_csrf" value="<?php echo h(csrf_token()); ?>">
  <input type="hidden" name="action" value="create">
  <label>아이디(이메일 또는 텍스트):<br>
    <input name="username" required placeholder="예) admin2@herocomics.local">
  </label><br>
  <label>초기 비밀번호(미입력 시 랜덤):<br>
    <input name="password" type="password" placeholder="비워두면 랜덤 생성">
  </label><br>
  <button>추가</button>
</form>

<h3 style="margin-top:20px;">계정 목록</h3>
<table>
  <thead><tr><th>ID</th><th>아이디</th><th>생성일</th><th>작업</th></tr></thead>
  <tbody>
<?php
if (!empty($rows)) {
  foreach ($rows as $r) {
?>
    <tr>
      <td><?php echo h((string)$r["id"]); ?></td>
      <td><?php echo h((string)$r["username"]); ?></td>
      <td><?php echo h((string)$r["created_at"]); ?></td>
      <td>
        <form method="post" action="admin_user_save.php" style="display:inline;">
          <input type="hidden" name="_csrf" value="<?php echo h(csrf_token()); ?>">
          <input type="hidden" name="action" value="reset">
          <input type="hidden" name="id" value="<?php echo h((string)$r["id"]); ?>">
          <button>비번 재설정(랜덤)</button>
        </form>
        <form method="post" action="admin_user_delete.php" style="display:inline;" onsubmit="return confirm(\"정말 삭제할까요?\");">
          <input type="hidden" name="_csrf" value="<?php echo h(csrf_token()); ?>">
          <input type="hidden" name="id" value="<?php echo h((string)$r["id"]); ?>">
          <button>삭제</button>
        </form>
      </td>
    </tr>
<?php
  }
} else {
?>
    <tr><td colspan="4" style="text-align:center;color:#888;">계정이 없습니다</td></tr>
<?php
}
?>
  </tbody>
</table>
