<?php
require_once '../conf/db.php';

echo "DB 연결 테스트:\n";
var_dump($pdo);

echo "\n\n출판사 데이터:\n";
$stmt = $pdo->query("SELECT * FROM bt_publishers");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    print_r($row);
}

echo "\n\n시리즈 데이터:\n";
$stmt = $pdo->query("SELECT * FROM bt_series");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    print_r($row);
}
