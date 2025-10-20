<?php
// 세션 시작 및 DB 연결 (header.php보다 먼저 실행)
session_start();
require_once '../../conf/db.php';

$publisher_id = $_SESSION['publisher_id'] ?? 1;
$user_id = $_SESSION['user_id'] ?? 1;

// URL에서 ID 가져오기
$book_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$book_id) {
    die('잘못된 접근입니다. 책 ID가 필요합니다.');
}

// 수정 폼이 제출되었을 때 처리 (header.php보다 먼저 처리해야 리다이렉트 가능)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // POST 데이터 받기
        $book_title = $_POST['book_title'] ?? '';
        $author = $_POST['author'] ?? '';
        $isbn = $_POST['isbn'] ?? '';
        $publisher = $_POST['publisher'] ?? '';
        $normal_price = intval($_POST['normal_price'] ?? 0);
        $discount_rate = intval($_POST['discount_rate'] ?? 0);
        $sale_price = intval($_POST['sale_price'] ?? 0);
        $book_status = intval($_POST['book_status'] ?? 1);
        $comics_brand = $_POST['comics_brand'] ?? '';
        $selected_genres = $_POST['genres'] ?? [];

        // 기본 정보 업데이트
        $sql = "UPDATE bt_books SET 
                book_title = :title,
                author = :author,
                isbn = :isbn,
                publisher = :publisher,
                normal_price = :price,
                discount_rate = :discount,
                sale_price = :sale_price,
                book_status = :status,
                comics_brand = :brand
                WHERE ID = :id AND publisher_id = :publisher_id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':title' => $book_title,
            ':author' => $author,
            ':isbn' => $isbn,
            ':publisher' => $publisher,
            ':price' => $normal_price,
            ':discount' => $discount_rate,
            ':sale_price' => $sale_price,
            ':status' => $book_status,
            ':brand' => $comics_brand,
            ':id' => $book_id,
            ':publisher_id' => $publisher_id
        ]);

        // 기존 책 정보를 다시 조회해서 폴더 경로 파악
        $book_sql = "SELECT cover_img, epub_path FROM bt_books WHERE ID = :id";
        $book_stmt = $pdo->prepare($book_sql);
        $book_stmt->execute([':id' => $book_id]);
        $current_book = $book_stmt->fetch();

        // 기존 폴더명 추출 (cover_img 또는 epub_path에서)
        $existing_folder = null;
        if (!empty($current_book['cover_img'])) {
            $cover_dir = dirname($current_book['cover_img']);
            $existing_folder = basename($cover_dir);
        } elseif (!empty($current_book['epub_path'])) {
            $epub_dir = dirname($current_book['epub_path']);
            $existing_folder = basename($epub_dir);
        }

        // 업로드 디렉토리 설정
        $upload_base_dir = __DIR__ . '/../../uploads/books/';
        
        // 기존 폴더가 있으면 사용, 없으면 새로 생성
        if ($existing_folder && is_dir($upload_base_dir . $existing_folder)) {
            $book_folder = $existing_folder;
        } else {
            $book_folder = date('YmdHis') . '_' . uniqid();
        }
        
        $book_dir = $upload_base_dir . $book_folder . '/';
        
        // 폴더가 없으면 생성
        if (!is_dir($book_dir)) {
            mkdir($book_dir, 0755, true);
        }

        // 표지 이미지 업로드 처리
        if (isset($_FILES['cover_img']) && $_FILES['cover_img']['error'] === UPLOAD_ERR_OK) {
            $cover_tmp = $_FILES['cover_img']['tmp_name'];
            $cover_ext = strtolower(pathinfo($_FILES['cover_img']['name'], PATHINFO_EXTENSION));
            $cover_filename = 'cover.' . $cover_ext;
            $cover_path = $book_dir . $cover_filename;
            
            // 기존 표지 이미지가 있다면 삭제
            if (file_exists($cover_path)) {
                unlink($cover_path);
            }
            
            if (move_uploaded_file($cover_tmp, $cover_path)) {
                $cover_url = '/admin/uploads/books/' . $book_folder . '/' . $cover_filename;
                $update_cover = "UPDATE bt_books SET cover_img = :cover WHERE ID = :id";
                $stmt_cover = $pdo->prepare($update_cover);
                $stmt_cover->execute([':cover' => $cover_url, ':id' => $book_id]);
            }
        }

        // 만화 파일 업로드 처리
        if (isset($_FILES['comic_file']) && $_FILES['comic_file']['error'] === UPLOAD_ERR_OK) {
            $zip_tmp = $_FILES['comic_file']['tmp_name'];
            $zip_name = $_FILES['comic_file']['name'];
            $zip_size = $_FILES['comic_file']['size'];
            
            if ($zip_size > 1024 * 1024 * 1024) {
                throw new Exception("파일 크기가 1GB를 초과합니다.");
            }
            
            $zip_filename = 'comic_' . time() . '.zip';
            $zip_path = $book_dir . $zip_filename;
            
            if (move_uploaded_file($zip_tmp, $zip_path)) {
                $zip = new ZipArchive;
                if ($zip->open($zip_path) === TRUE) {
                    $extract_dir = $book_dir . 'pages/';
                    
                    // 기존 pages 폴더가 있다면 내용 삭제
                    if (is_dir($extract_dir)) {
                        $files = glob($extract_dir . '*');
                        foreach ($files as $file) {
                            if (is_file($file)) {
                                unlink($file);
                            }
                        }
                    } else {
                        mkdir($extract_dir, 0755, true);
                    }
                    
                    $zip->extractTo($extract_dir);
                    $zip->close();
                    unlink($zip_path);
                    
                    $epub_path = '/admin/uploads/books/' . $book_folder . '/pages/';
                    $update_file = "UPDATE bt_books SET epub_path = :file_path, epub_name = :file_name WHERE ID = :id";
                    $stmt_file = $pdo->prepare($update_file);
                    $stmt_file->execute([
                        ':file_path' => $epub_path,
                        ':file_name' => $zip_name,
                        ':id' => $book_id
                    ]);
                }
            }
        }

        // 장르 업데이트
        $delete_genres = "DELETE FROM bt_book_genres WHERE book_id = :book_id";
        $stmt_delete = $pdo->prepare($delete_genres);
        $stmt_delete->execute([':book_id' => $book_id]);

        if (!empty($selected_genres)) {
            $insert_genre = "INSERT INTO bt_book_genres (book_id, genre_id) VALUES (:book_id, :genre_id)";
            $stmt_genre = $pdo->prepare($insert_genre);
            
            foreach ($selected_genres as $genre_id) {
                $stmt_genre->execute([':book_id' => $book_id, ':genre_id' => $genre_id]);
            }
        }

        // 성공 시 목록 페이지로 리다이렉트 (header.php 실행 전이므로 가능)
        header('Location: ../dashboard.php');
        exit;
        
    } catch (Exception $e) {
        // 에러가 발생한 경우에만 화면에 표시하기 위해 계속 진행
        $error_message = "책 정보 수정에 실패했습니다: " . $e->getMessage();
    }
}

// 여기서부터 화면 출력 시작 (POST 처리가 성공하면 위에서 exit 되므로 실행 안 됨)
$page_title = '책 수정 - HeroComics 출판사 CMS';
// 페이지 정보 설정
$page_title = '책 수정';
$current_page = 'books';
require_once '../layout/modern_header.php';

// 책 정보 조회
$sql = "SELECT * FROM bt_books WHERE ID = :id AND publisher_id = :publisher_id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $book_id, ':publisher_id' => $publisher_id]);
$book = $stmt->fetch();

if (!$book) {
    die('책을 찾을 수 없거나 접근 권한이 없습니다.');
}

// 현재 책의 장르 조회
$genre_sql = "SELECT genre_id FROM bt_book_genres WHERE book_id = :book_id";
$genre_stmt = $pdo->prepare($genre_sql);
$genre_stmt->execute([':book_id' => $book_id]);
$selected_genre_ids = $genre_stmt->fetchAll(PDO::FETCH_COLUMN);
// 모든 장르 목록 조회
$genres_sql = "SELECT * FROM bt_genres ORDER BY genre_name";
$genres = $pdo->query($genres_sql)->fetchAll();
?>

<style>
    .upload-section {
        background: white;
        padding: 2rem;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        margin-bottom: 2rem;
    }
    
    .section-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e5e7eb;
    }
    
    .preview-image {
        max-width: 200px;
        max-height: 300px;
        margin-top: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 5px;
    }
    
    .genre-select {
        height: 200px;
    }
    
    .form-label {
        font-weight: 500;
        margin-bottom: 0.5rem;
    }
</style>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">책 수정</h1>
        <a href="list.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> 목록으로
        </a>
    </div>

    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error_message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="row">
            <!-- 왼쪽: 기본 정보 -->
            <div class="col-lg-8">
                <!-- 기본 정보 섹션 -->
                <div class="upload-section">
                    <h5 class="section-title">기본 정보</h5>
                    
                    <div class="mb-3">
                        <label class="form-label">책 제목 *</label>
                        <input type="text" class="form-control" name="book_title" 
                               value="<?= htmlspecialchars($book['book_title']) ?>" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">저자 *</label>
                            <input type="text" class="form-control" name="author" 
                                   value="<?= htmlspecialchars($book['author']) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">출판사</label>
                            <input type="text" class="form-control" name="publisher" 
                                   value="<?= htmlspecialchars($book['publisher']) ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">ISBN</label>
                        <input type="text" class="form-control" name="isbn" 
                               value="<?= htmlspecialchars($book['isbn']) ?>">
                    </div>
                </div>

                <!-- 가격 정보 섹션 -->
                <div class="upload-section">
                    <h5 class="section-title">가격 정보</h5>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">정가 (원) *</label>
                            <input type="number" class="form-control" name="normal_price" 
                                   id="normal_price" value="<?= $book['normal_price'] ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">할인율 (%)</label>
                            <input type="number" class="form-control" name="discount_rate" 
                                   id="discount_rate" value="<?= $book['discount_rate'] ?>" 
                                   min="0" max="100">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">판매가 (원)</label>
                            <input type="number" class="form-control" name="sale_price" 
                                   id="sale_price" value="<?= $book['sale_price'] ?>" readonly>
                        </div>
                    </div>
                </div>

                <!-- 파일 업로드 섹션 -->
                <div class="upload-section">
                    <h5 class="section-title">파일 업로드 (변경하지 않으려면 비워두세요)</h5>
                    
                    <div class="mb-3">
                        <label class="form-label">표지 이미지</label>
                        <?php if ($book['cover_img']): ?>
                            <div class="mb-2">
                                <small class="text-muted">현재 표지:</small><br>
                                <img src="<?= htmlspecialchars($book['cover_img']) ?>" 
                                     class="preview-image" alt="현재 표지">
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" name="cover_img" 
                               accept="image/*" id="cover_input">
                        <div id="cover_preview"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">만화 파일 (ZIP)</label>
                        <?php if ($book['epub_path']): ?>
                            <div class="mb-2">
                                <small class="text-muted">현재 파일: <?= htmlspecialchars($book['epub_name'] ?? $book['epub_path']) ?></small>
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" name="comic_file" accept=".zip">
                        <small class="text-muted">최대 1GB까지 업로드 가능</small>
                    </div>
                </div>
            </div>

            <!-- 오른쪽: 추가 정보 -->
            <div class="col-lg-4">
                <!-- 판매 정보 섹션 -->
                <div class="upload-section">
                    <h5 class="section-title">판매 정보</h5>
                    
                    <div class="mb-3">
                        <label class="form-label">브랜드</label>
                        <select class="form-select" name="comics_brand">
                            <option value="">선택 안함</option>
                            <option value="Marvel" <?= $book['comics_brand'] === 'Marvel' ? 'selected' : '' ?>>Marvel</option>
                            <option value="DC" <?= $book['comics_brand'] === 'DC' ? 'selected' : '' ?>>DC</option>
                            <option value="Image" <?= $book['comics_brand'] === 'Image' ? 'selected' : '' ?>>Image</option>
                            <option value="Dark Horse" <?= $book['comics_brand'] === 'Dark Horse' ? 'selected' : '' ?>>Dark Horse</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">상태</label>
                        <select class="form-select" name="book_status">
                            <option value="1" <?= $book['book_status'] == 1 ? 'selected' : '' ?>>판매중</option>
                            <option value="2" <?= $book['book_status'] == 2 ? 'selected' : '' ?>>품절</option>
                            <option value="3" <?= $book['book_status'] == 3 ? 'selected' : '' ?>>판매중단</option>
                        </select>
                    </div>
                </div>

                <!-- 장르 선택 섹션 -->
                <div class="upload-section">
                    <h5 class="section-title">장르 선택</h5>
                    
                    <select class="form-select genre-select" name="genres[]" multiple>
                        <?php foreach ($genres as $genre): ?>
                            <option value="<?= $genre['genre_id'] ?>" 
                                    <?= in_array($genre['genre_id'], $selected_genre_ids) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($genre['genre_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="text-muted d-block mt-2">Ctrl 키를 누르고 클릭하여 여러 개 선택 가능</small>
                </div>

                <!-- 버튼 섹션 -->
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save me-2"></i>수정 완료
                    </button>
                    <a href="list.php" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>취소
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    // 가격 자동 계산
    document.getElementById('normal_price').addEventListener('input', calculateSalePrice);
    document.getElementById('discount_rate').addEventListener('input', calculateSalePrice);

    function calculateSalePrice() {
        const price = parseInt(document.getElementById('normal_price').value) || 0;
        const discount = parseInt(document.getElementById('discount_rate').value) || 0;
        const salePrice = price - (price * discount / 100);
        document.getElementById('sale_price').value = Math.round(salePrice);
    }

    // 표지 이미지 미리보기
    document.getElementById('cover_input').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('cover_preview').innerHTML = 
                    '<img src="' + e.target.result + '" class="preview-image" alt="새 표지 미리보기">';
            }
            reader.readAsDataURL(file);
        }
    });
</script>

<?php require_once '../layout/modern_footer.php'; ?>
