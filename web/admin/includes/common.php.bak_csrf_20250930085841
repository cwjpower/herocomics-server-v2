<?php
declare(strict_types=1);
session_start();
if (empty($_SESSION["admin_id"])) { header("Location: login.php"); exit; }

function db(): PDO {
  static $pdo = null;
  if ($pdo === null) {
    $pdo = new PDO("mysql:host=mariadb;dbname=herocomics;charset=utf8mb4","hero","secret",[
      PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
    ]);
  }
  return $pdo;
}
function h(string $s): string { return htmlspecialchars($s, ENT_QUOTES|ENT_SUBSTITUTE, "UTF-8"); }
function csrf_token(): string {
  if (empty($_SESSION["csrf"])) $_SESSION["csrf"] = bin2hex(random_bytes(16));
  return $_SESSION["csrf"];
}
function csrf_check(string $token): void {
  $ok = hash_equals($_SESSION["csrf"] ?? "", $token);
  $_SESSION["csrf"] = ""; // one-time
  if (!$ok) { http_response_code(400); exit("잘못된 요청 (CSRF)"); }
}
