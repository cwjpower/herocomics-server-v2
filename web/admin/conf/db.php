<?php
// MariaDB 연결 설정
$db_host = 'herocomics-mariadb';
$db_name = 'herocomics';
$db_user = 'root';
$db_pass = 'rootpass';
$db_port = '3306';

// 전역 PDO 변수
try {
    $pdo = new PDO(
        "mysql:host=$db_host;port=$db_port;dbname=$db_name;charset=utf8mb4",
        $db_user,
        $db_pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ]
    );
} catch (PDOException $e) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'code' => -1,
        'msg' => 'Database connection failed: ' . $e->getMessage(),
        'data' => null
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// 함수 버전
function getDbConnection() {
    global $pdo;
    return $pdo;
}

// db_connect 함수 (호환성)
function db_connect() {
    global $pdo;
    return $pdo;
}
