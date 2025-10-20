<?php require __DIR__."/includes/common.php";
$t = $_GET["t"] ?? "";
if ($t === "") { http_response_code(400); exit("no table"); }
$pdo = db();
$tables = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = DATABASE()")->fetchAll(PDO::FETCH_COLUMN);
if (!in_array($t, $tables, true)) { http_response_code(400); exit("bad table"); }
header("Content-Type: text/csv; charset=UTF-8");
header("Content-Disposition: attachment; filename=\"".$t."_export.csv\"");
$fp = fopen("php://output", "w");
$stm = $pdo->query("SELECT * FROM `".$t."`");
$first = true;
while($row = $stm->fetch(PDO::FETCH_ASSOC)){
  if($first){ fputcsv($fp, array_keys($row)); $first=false; }
  fputcsv($fp, array_values($row));
}
if($first){ /* 빈 테이블이면 헤더만 */ 
  $cols = $pdo->query("SELECT column_name FROM information_schema.columns WHERE table_schema=DATABASE() AND table_name=".$pdo->quote($t)." ORDER BY ordinal_position")->fetchAll(PDO::FETCH_COLUMN);
  fputcsv($fp, $cols);
}
fclose($fp);
