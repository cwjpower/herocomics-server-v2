<?php require __DIR__."/nav.php"; ?>

<?php
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
    
    header('Location: genres.php?success=added');
    exit;
}

// 장르 삭제 처리
if (isset($_GET['delete'])) {
    $genre_id = (int)$_GET['delete'];
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM bt_book_genres WHERE genre_id = ?");
    $stmt->execute([$genre_id]);
    $count = $stmt->fetchColumn();
    
    if ($count > 0) {
        header('Location: genres.php?error=has_books');
        exit;
    }
    
    $stmt = $pdo->prepare("DELETE FROM bt_genres WHERE genre_id = ?");
    $stmt->execute([$genre_id]);
    
    header('Location: genres.php?success=deleted');
    exit;
}
?>

<h2>장르 관리</h2>

<?php if (isset($_GET['success'])): ?>
    <div class="alert">
        <?php if ($_GET['success'] === 'added'): ?>
            ✅ 장르가 추가되었습니다.
        <?php elseif ($_GET['success'] === 'deleted'): ?>
            ✅ 장르가 삭제되었습니다.
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    <div class="alert" style="background:#fff8f8;border-color:#fcc;color:#a00">
        <?php if ($_GET['error'] === 'has_books'): ?>
            ❌ 이 장르에 연결된 책이 있어서 삭제할 수 없습니다.
        <?php endif; ?>
    </div>
<?php endif; ?>

<button onclick="document.getElementById('addForm').style.display='block'">➕ 장르 추가</button>

<div id="addForm" style="display:none;margin:20px 0;padding:15px;border:1px solid #ddd;border-radius:8px;background:#fafafa">
    <h3>새 장르 추가</h3>
    <form method="POST">
        <input type="hidden" name="action" value="add">
        <div>
            <label>장르명 (한글) *</label><br>
            <input type="text" name="genre_name" required style="width:300px">
        </div>
        <div style="margin-top:10px">
            <label>장르명 (영문)</label><br>
            <input type="text" name="genre_name_en" style="width:300px">
        </div>
        <div style="margin-top:10px">
            <label>순서</label><br>
            <input type="number" name="genre_order" value="<?php echo count($genres) + 1; ?>" style="width:100px">
        </div>
        <div style="margin-top:15px">
            <button type="submit">추가</button>
            <button type="button" onclick="document.getElementById('addForm').style.display='none'">취소</button>
        </div>
    </form>
</div>

<table>
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
            <td><?php echo htmlspecialchars($genre['genre_name']); ?></td>
            <td><?php echo htmlspecialchars($genre['genre_name_en']); ?></td>
            <td><?php echo $genre['genre_order']; ?></td>
            <td><?php echo date('Y-m-d', strtotime($genre['created_at'])); ?></td>
            <td>
                <a href="?delete=<?php echo $genre['genre_id']; ?>" 
                   onclick="return confirm('정말 삭제하시겠습니까?');"
                   style="color:red">🗑️ 삭제</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
