<?php
header('Content-Type:text/plain; charset=utf-8');

$cfg = [
  "host" => getenv("MARIADB_HOST") ?: "mariadb",
  "db"   => getenv("MARIADB_DATABASE") ?: "herocomics",
  "user" => getenv("MARIADB_USER") ?: "hero",
  "pass" => getenv("MARIADB_PASSWORD") ?: "secret",
];

echo "== mysqli test ==\n";
$mysqli = @new mysqli($cfg["host"], $cfg["user"], $cfg["pass"], $cfg["db"]);
echo $mysqli->connect_errno ? "FAIL: {$mysqli->connect_error}\n" : "OK\n";

echo "\n== PDO test ==\n";
try {
  $pdo = new PDO("mysql:host={$cfg["host"]};dbname={$cfg["db"]};charset=utf8mb4", $cfg["user"], $cfg["pass"], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  ]);
  echo "OK\n";
} catch(Throwable $e) { echo "FAIL: ".$e->getMessage()."\n"; }
