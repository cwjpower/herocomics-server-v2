<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'code' => 1,
        'msg' => 'Method not allowed',
        'data' => null
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

$host = getenv('DB_HOST') ?: 'herocomics-mariadb';
$dbname = getenv('DB_NAME') ?: 'herocomics';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: 'rootpass';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // volume_id 받기 (실제로는 book_id로 사용)
    $volume_id = $_POST['volume_id'] ?? 0;

    if (empty($volume_id)) {
        http_response_code(400);
        echo json_encode([
            'code' => 1,
            'msg' => 'volume_id is required',
            'data' => null
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }

    // 볼륨 존재 확인
    $stmt = $pdo->prepare("SELECT * FROM bt_volumes WHERE volume_id = :volume_id");
    $stmt->execute([':volume_id' => $volume_id]);
    $volume = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$volume) {
        http_response_code(404);
        echo json_encode([
            'code' => 1,
            'msg' => 'Volume not found',
            'data' => null
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }

    // 업로드된 파일 확인
    if (empty($_FILES['files'])) {
        http_response_code(400);
        echo json_encode([
            'code' => 1,
            'msg' => 'No files uploaded',
            'data' => null
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }

    // 업로드 디렉토리 생성
    $upload_dir = __DIR__ . "/../../../uploads/pages/volume_" . $volume_id;
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $uploaded_files = [];
    $uploaded_count = 0;
    $files = $_FILES['files'];
    $episode_num = 1; // 기본 에피소드 번호

    // 여러 파일 처리
    $file_count = is_array($files['name']) ? count($files['name']) : 1;

    for ($i = 0; $i < $file_count; $i++) {
        if (is_array($files['name'])) {
            $file_name = $files['name'][$i];
            $file_tmp = $files['tmp_name'][$i];
            $file_error = $files['error'][$i];
        } else {
            $file_name = $files['name'];
            $file_tmp = $files['tmp_name'];
            $file_error = $files['error'];
        }

        if ($file_error === UPLOAD_ERR_OK) {
            // 파일 확장자 확인
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if (!in_array($file_ext, $allowed_ext)) {
                continue;
            }

            // 현재 페이지 번호 가져오기 (실제 테이블 구조 사용)
            $stmt = $pdo->prepare("SELECT IFNULL(MAX(page_num), 0) + 1 as next_page FROM bt_books_pages WHERE book_id = :book_id AND episode_num = :episode_num");
            $stmt->execute([
                ':book_id' => $volume_id,
                ':episode_num' => $episode_num
            ]);
            $page_num = $stmt->fetchColumn();

            // 새 파일명 생성
            $new_filename = "page_" . $page_num . "." . $file_ext;
            $file_path = $upload_dir . "/" . $new_filename;

            // 파일 이동
            if (move_uploaded_file($file_tmp, $file_path)) {
                // DB에 저장 (실제 테이블 구조 사용)
                $web_path = "/uploads/pages/volume_" . $volume_id . "/" . $new_filename;

                $stmt = $pdo->prepare("INSERT INTO bt_books_pages (book_id, episode_num, page_num, image_path) VALUES (:book_id, :episode_num, :page_num, :image_path)");
                $stmt->execute([
                    ':book_id' => $volume_id,
                    ':episode_num' => $episode_num,
                    ':page_num' => $page_num,
                    ':image_path' => $web_path
                ]);

                $uploaded_files[] = [
                    'page_num' => $page_num,
                    'filename' => $new_filename,
                    'path' => $web_path
                ];
                $uploaded_count++;
            }
        }
    }

    // 볼륨의 total_pages 업데이트
    $stmt = $pdo->prepare("UPDATE bt_volumes SET total_pages = (SELECT COUNT(*) FROM bt_books_pages WHERE book_id = :book_id) WHERE volume_id = :volume_id");
    $stmt->execute([
        ':book_id' => $volume_id,
        ':volume_id' => $volume_id
    ]);

    echo json_encode([
        'code' => 0,
        'msg' => 'Upload successful',
        'data' => [
            'volume_id' => $volume_id,
            'uploaded_count' => $uploaded_count,
            'files' => $uploaded_files
        ]
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'code' => 1,
        'msg' => 'Database error: ' . $e->getMessage(),
        'data' => null
    ], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'code' => 1,
        'msg' => 'Error: ' . $e->getMessage(),
        'data' => null
    ], JSON_UNESCAPED_UNICODE);
}
?>