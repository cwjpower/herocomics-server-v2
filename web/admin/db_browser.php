<?php require __DIR__."/nav.php";
$pdo = db();
$tables = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = DATABASE() ORDER BY table_name")->fetchAll(PDO::FETCH_COLUMN);
$chosen = $_GET["t"] ?? "";
if ($chosen !== "" && !in_array($chosen, $tables, true)) {
  http_response_code(400); exit("Unknown table");
}
?>
<h2>DB 브라우저</h2>
<form method="get" action="db_browser.php" style="margin:8px 0;">
  <label>테이블 선택: 
    <select name="t" onchange="this.form.submit()">
      <option value="">-- 테이블 목록 --</option>
      <?php foreach($tables as $t): ?>
        <option value="<?=h($t)?>" <?= $t===$chosen?"selected":"" ?>><?=h($t)?></option>
      <?php endforeach ?>
    </select>
  </label>
  <?php if($chosen): ?>
    <a href="export_csv.php?t=<?=urlencode($chosen)?>" style="margin-left:10px;">CSV 내보내기</a>
  <?php endif; ?>
</form>
<?php if($chosen): 
  // 최대 50행 프리뷰
  $stmt = $pdo->query("SELECT * FROM `".$chosen."` LIMIT 50");
  $rows = $stmt->fetchAll();
  $cols = array_keys($rows[0] ?? []);
?>
  <div style="margin:6px 0;color:#555;">표시 행: <?=count($rows)?> / 50</div>
  <table>
    <thead><tr>
      <?php foreach($cols as $c): ?><th><?=h($c)?></th><?php endforeach; ?>
    </tr></thead>
    <tbody>
      <?php foreach($rows as $r): ?>
        <tr><?php foreach($cols as $c): ?><td><?=h((string)($r[$c] ?? ""))?></td><?php endforeach; ?></tr>
      <?php endforeach; ?>
      <?php if(!$rows): ?><tr><td colspan="99" style="text-align:center;color:#888;">행이 없습니다</td></tr><?php endif; ?>
    </tbody>
  </table>
<?php else: ?>
  <p>위 드롭다운에서 테이블을 선택해.</p>
<?php endif; ?>
