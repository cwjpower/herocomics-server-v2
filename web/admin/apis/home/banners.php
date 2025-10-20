<?php
// DB 연결 - Docker 환경에 맞게
$mysqli = new mysqli('herocomics-mariadb', 'root', 'rootpass', 'herocomics');

if ($mysqli->connect_error) {
    die(json_encode(['error' => 'Connection failed: ' . $mysqli->connect_error]));
}

$query = "SELECT 
    ID as id,
    bnr_title as title,
    bnr_file_url as image,
    bnr_url as link,
    bnr_order as order_num
FROM bt_banner 
WHERE bnr_section = 'main' 
AND hide_or_show = 'show'
ORDER BY bnr_order ASC";

$result = $mysqli->query($query);
$banners = [];

if ($result) {
    while($row = $result->fetch_assoc()) {
        $banners[] = $row;
    }
}

echo json_encode([
    'success' => true,
    'banners' => $banners
]);

$mysqli->close();
?>
