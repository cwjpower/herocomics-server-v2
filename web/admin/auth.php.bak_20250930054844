<?php
session_start();
$u = trim($_POST["username"] ?? "");
$p = $_POST["password"] ?? "";

try {
  $pdo = new PDO("mysql:host=mariadb;dbname=herocomics;charset=utf8mb4","hero","secret",
                 [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
  $stmt = $pdo->prepare("SELECT id, username, password_hash FROM admin_users WHERE username = ?");
  $stmt->execute([$u]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($user && password_verify($p, $user["password_hash"])) {
    $_SESSION["admin_id"] = $user["id"];
    header("Location: dashboard.php"); exit;
  }
} catch(Throwable $e) {
  error_log($e);
}
http_response_code(401);
echo "로그인 실패";
