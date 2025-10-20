<?php
/**
 * 뉴스/공지사항 목록 API
 * 경로: ~/herocomics-server/web/admin/apis/posts/news_list.php
 */

// 에러 표시 끄기
error_reporting(0);

// 헤더 설정
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');

// DB 연결
$conn = new mysqli('herocomics-mariadb', 'root', 'rootpass', 'herocomics');

// DB 연결 확인
if ($conn->connect_error) {
    echo json_encode([
        'code' => 1,
        'msg' => 'Database connection failed',
        'data' => []
    ]);
    exit;
}

// UTF-8 설정
$conn->set_charset("utf8mb4");

// 파라미터 받기
$category = isset($_GET['category']) ? $_GET['category'] : 'TOTAL';
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;

// Mock 데이터 (실제 news 테이블이 없을 경우를 대비)
$mockNews = [
    [
        'id' => '1',
        'title' => 'Marvel Announces New Spider-Man Series',
        'summary' => 'A brand new Spider-Man series is coming this summer with Miles Morales',
        'image_url' => 'https://picsum.photos/400/300?random=101',
        'category' => 'MARVEL',
        'date' => date('Y-m-d H:i:s', strtotime('-2 hours')),
        'content' => 'Full story content here...',
        'author' => 'Marvel Editorial',
        'view_count' => 1234
    ],
    [
        'id' => '2',
        'title' => 'DC Comics Batman #100 Celebration',
        'summary' => 'Celebrating 100 issues of the new Batman run with special variant covers',
        'image_url' => 'https://picsum.photos/400/300?random=102',
        'category' => 'DC',
        'date' => date('Y-m-d H:i:s', strtotime('-5 hours')),
        'content' => 'Batman reaches a milestone...',
        'author' => 'DC Editorial',
        'view_count' => 2341
    ],
    [
        'id' => '3',
        'title' => 'Image Comics: Saga Returns',
        'summary' => 'The beloved Saga series returns after hiatus with new story arc',
        'image_url' => 'https://picsum.photos/400/300?random=103',
        'category' => 'IMAGE',
        'date' => date('Y-m-d H:i:s', strtotime('-1 day')),
        'content' => 'Saga is back...',
        'author' => 'Image Editorial',
        'view_count' => 3456
    ],
    [
        'id' => '4',
        'title' => 'Free Comic Book Day 2025 Announced',
        'summary' => 'Mark your calendars for the biggest comic event of the year',
        'image_url' => 'https://picsum.photos/400/300?random=104',
        'category' => 'TOTAL',
        'date' => date('Y-m-d H:i:s', strtotime('-2 days')),
        'content' => 'Free comics for everyone...',
        'author' => 'Industry News',
        'view_count' => 5678
    ],
    [
        'id' => '5',
        'title' => 'X-Men: New Mutants Revival',
        'summary' => 'Classic New Mutants team reunites for 40th anniversary',
        'image_url' => 'https://picsum.photos/400/300?random=105',
        'category' => 'MARVEL',
        'date' => date('Y-m-d H:i:s', strtotime('-3 days')),
        'content' => 'The New Mutants are back...',
        'author' => 'Marvel Editorial',
        'view_count' => 4321
    ],
    [
        'id' => '6',
        'title' => 'Wonder Woman: New Origin Story',
        'summary' => 'DC reimagines Diana\'s origin for modern audiences',
        'image_url' => 'https://picsum.photos/400/300?random=106',
        'category' => 'DC',
        'date' => date('Y-m-d H:i:s', strtotime('-4 days')),
        'content' => 'Wonder Woman gets a fresh start...',
        'author' => 'DC Editorial',
        'view_count' => 3210
    ]
];

// 카테고리 필터링
if ($category && $category != 'TOTAL') {
    $mockNews = array_filter($mockNews, function($news) use ($category) {
        return $news['category'] == $category;
    });
}

// limit 적용
$mockNews = array_slice($mockNews, 0, $limit);

// 응답
$response = [
    'code' => 0,
    'msg' => 'Success',
    'data' => array_values($mockNews)  // array_values로 인덱스 재정렬
];

// JSON 출력
echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

// 연결 종료
$conn->close();
?>
