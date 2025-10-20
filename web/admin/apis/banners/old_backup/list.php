<?php
/*
 * Desc: 배너 목록 API
 * Method: GET
 */

$code = 0;
$msg = '';
$banners = [];

// DB 연결
$conn = new mysqli('herocomics-mariadb', 'root', 'rootpass', 'herocomics');

if ($conn->connect_error) {
    $code = 500;
    $msg = 'DB 연결 실패';
} else {
    // 활성화된 배너만 조회 (표시순서대로)
    $stmt = $conn->prepare("SELECT * FROM banners WHERE is_active = 1 ORDER BY display_order ASC");
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $banners[] = [
            'banner_id' => $row['banner_id'],
            'title' => $row['title'],
            'subtitle' => $row['subtitle'],
            'image_url' => $row['image_url'],
            'link_url' => $row['link_url']
        ];
    }
    
    $stmt->close();
    $conn->close();
}

header('Content-Type: application/json');
echo json_encode(compact('code', 'msg', 'banners'));
?>
