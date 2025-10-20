<?php
$page_title = '장르 관리 - HeroComics 출판사 CMS';
$current_page = 'genres';
require_once '../layout/modern_header.php';
require_once '../../conf/db.php';

// 장르 목록 가져오기
$stmt = $pdo->query("SELECT * FROM bt_genres ORDER BY genre_order");
$genres = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 장르 추가 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $genre_name = trim($_POST['genre_name']);
    $genre_name_en = trim($_POST['genre_name_en']);
    $genre_order = (int)$_POST['genre_order'];
    
    $stmt = $pdo->prepare("INSERT INTO bt_genres (genre_name, genre_name_en, genre_order) VALUES (?, ?, ?)");
    $stmt->execute([$genre_name, $genre_name_en, $genre_order]);
    
    header('Location: index.php?success=added');
    exit;
}

// 장르 삭제
if (isset($_GET['delete'])) {
    $genre_id = (int)$_GET['delete'];
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM bt_book_genres WHERE genre_id = ?");
    $stmt->execute([$genre_id]);
    $count = $stmt->fetchColumn();
    
    if ($count > 0) {
        header('Location: index.php?error=has_books');
        exit;
    }
    
    $stmt = $pdo->prepare("DELETE FROM bt_genres WHERE genre_id = ?");
    $stmt->execute([$genre_id]);
    
    header('Location: index.php?success=deleted');
    exit;
}
?>

<div class="container-fluid">
    <div class="content-wrapper">
        <div class="page-header">
            <h1><?php echo $page_title; ?></h1>
            <button class="btn btn-primary" onclick="showAddForm()">
                <i class="bi bi-plus-lg"></i> 장르 추가
            </button>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php if ($_GET['success'] === 'added'): ?>
                    ✅ 장르가 추가되었습니다.
                <?php elseif ($_GET['success'] === 'deleted'): ?>
                    ✅ 장르가 삭제되었습니다.
                <?php endif; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php if ($_GET['error'] === 'has_books'): ?>
                    ❌ 이 장르에 연결된 책이 있어서 삭제할 수 없습니다.
                <?php endif; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- 추가 폼 -->
        <div id="addForm" style="display:none;" class="card mb-4">
            <div class="card-body">
                <h3>새 장르 추가</h3>
                <form method="POST">
                    <input type="hidden" name="action" value="add">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-label">장르명 (한글) *</label>
                            <input type="text" class="form-control" name="genre_name" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">장르명 (영문)</label>
                            <input type="text" class="form-control" name="genre_name_en">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">순서</label>
                            <input type="number" class="form-control" name="genre_order" value="<?php echo count($genres) + 1; ?>">
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">추가</button>
                        <button type="button" class="btn btn-secondary" onclick="hideAddForm()">취소</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- 장르 목록 -->
        <div class="card">
            <div class="card-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>장르명 (한글)</th>
                            <th>장르명 (영문)</th>
                            <th>순서</th>
                            <th>등록일</th>
                            <th>관리</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($genres as $genre): ?>
                        <tr>
                            <td><?php echo $genre['genre_id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($genre['genre_name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($genre['genre_name_en']); ?></td>
                            <td><?php echo $genre['genre_order']; ?></td>
                            <td><?php echo date('Y-m-d', strtotime($genre['created_at'])); ?></td>
                            <td>
                                <a href="?delete=<?php echo $genre['genre_id']; ?>" 
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('정말 삭제하시겠습니까?');">
                                    <i class="bi bi-trash"></i> 삭제
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function showAddForm() {
    document.getElementById('addForm').style.display = 'block';
}
function hideAddForm() {
    document.getElementById('addForm').style.display = 'none';
}
</script>

<?php require_once '../layout/modern_footer.php'; ?>
