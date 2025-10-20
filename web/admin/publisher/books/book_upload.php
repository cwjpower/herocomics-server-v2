<?php
$page_title = '새 책 추가 - HeroComics 출판사 CMS';
$current_page = 'book_upload';
require_once '../layout/header.php';
require_once '../../conf/db.php';

$publisher_id = $_SESSION['publisher_id'] ?? 1;
$user_id = $_SESSION['user_id'] ?? 1;
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
    
    .image-preview {
        width: 200px;
        height: 280px;
        border: 2px dashed #d1d5db;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f9fafb;
        cursor: pointer;
        overflow: hidden;
    }
    
    .image-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .image-preview:hover {
        border-color: var(--primary-color);
        background: #eff6ff;
    }
    
    .file-upload-box {
        border: 2px dashed #d1d5db;
        border-radius: 8px;
        padding: 3rem 2rem;
        text-align: center;
        background: #f9fafb;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .file-upload-box:hover {
        border-color: var(--primary-color);
        background: #eff6ff;
    }
    
    .file-upload-box.has-file {
        border-color: #10b981;
        background: #ecfdf5;
    }
    
    .required-field::after {
        content: '*';
        color: #dc3545;
        margin-left: 4px;
    }
    
    .form-hint {
        font-size: 0.875rem;
        color: #6b7280;
        margin-top: 0.25rem;
    }
    
    .alert-info-custom {
        background: #dbeafe;
        border-left: 4px solid #3b82f6;
        padding: 1rem;
        border-radius: 4px;
        margin-bottom: 1.5rem;
    }
</style>

<!-- 페이지 헤더 -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2><i class="fas fa-plus-circle"></i> 새 책 추가</h2>
        <p class="text-muted mb-0">새로운 책을 등록하고 판매를 시작하세요.</p>
    </div>
    <div>
        <a href="list.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> 목록으로
        </a>
    </div>
</div>

<!-- 안내 메시지 -->
<div class="alert-info-custom">
    <i class="fas fa-info-circle"></i>
    <strong>업로드 가이드:</strong> 
    만화 파일은 ZIP으로 압축하여 업로드하거나, 표지 이미지만 먼저 등록할 수 있습니다. 
    최대 파일 크기는 <strong>1GB</strong>입니다.
</div>

<form method="POST" action="book_upload_process.php" enctype="multipart/form-data" id="bookForm">
    <input type="hidden" name="publisher_id" value="<?= $publisher_id ?>">
    <input type="hidden" name="user_id" value="<?= $user_id ?>">
    
    <!-- 1. 기본 정보 -->
    <div class="upload-section">
        <h3 class="section-title">
            <i class="fas fa-book"></i> 기본 정보
        </h3>
        
        <div class="row">
            <div class="col-md-8">
                <!-- 제목 -->
                <div class="mb-3">
                    <label class="form-label required-field">책 제목</label>
                    <input type="text" name="book_title" class="form-control form-control-lg" required 
                           placeholder="예: Spider-Man #1">
                    <div class="form-hint">책의 정확한 제목을 입력하세요.</div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <!-- 저자 -->
                        <div class="mb-3">
                            <label class="form-label required-field">저자</label>
                            <input type="text" name="author" class="form-control" required 
                                   placeholder="예: Stan Lee">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <!-- 출판사 -->
                        <div class="mb-3">
                            <label class="form-label required-field">출판사</label>
                            <input type="text" name="publisher" class="form-control" required 
                                   placeholder="예: 마블코리아">
                        </div>
                    </div>
                </div>
                
                <!-- ISBN -->
                <div class="mb-3">
                    <label class="form-label required-field">ISBN</label>
                    <input type="text" name="isbn" class="form-control" required 
                           placeholder="예: ISBN001 (고유값이어야 합니다)">
                    <div class="form-hint">ISBN 13자리 또는 고유 식별 코드</div>
                </div>
            </div>
            <div class="col-md-4">
                <!-- 표지 이미지 -->
                <div class="mb-3">
                    <label class="form-label required-field">표지 이미지</label>
                    <div class="image-preview" id="imagePreview" onclick="document.getElementById('coverImage').click()">
                        <div class="text-center text-muted">
                            <i class="fas fa-image fa-3x mb-2"></i>
                            <p class="mb-0">클릭하여 이미지 선택</p>
                            <small>권장: 600x800px</small>
                        </div>
                    </div>
                    <input type="file" name="cover_img" id="coverImage" class="d-none" 
                           accept="image/*" required onchange="previewImage(this)">
                    <div class="form-hint mt-2">JPG, PNG (최대 10MB)</div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- 2. 만화 파일 업로드 -->
    <div class="upload-section">
        <h3 class="section-title">
            <i class="fas fa-file-archive"></i> 만화 파일 업로드
        </h3>
        
        <!-- 업로드 방식 선택 -->
        <div class="mb-4">
            <label class="form-label"><strong>업로드 방식 선택</strong></label>
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <input type="radio" name="upload_type" id="uploadZip" value="zip" class="form-check-input" checked>
                            <label for="uploadZip" class="d-block mt-2" style="cursor: pointer;">
                                <i class="fas fa-file-archive fa-3x text-primary mb-2"></i>
                                <h6>ZIP 파일</h6>
                                <small class="text-muted">압축 파일로 한번에</small>
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <input type="radio" name="upload_type" id="uploadImages" value="images" class="form-check-input">
                            <label for="uploadImages" class="d-block mt-2" style="cursor: pointer;">
                                <i class="fas fa-images fa-3x text-success mb-2"></i>
                                <h6>이미지 여러 개</h6>
                                <small class="text-muted">개별 이미지 선택</small>
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card h-100 border-warning">
                        <div class="card-body text-center">
                            <div class="form-check form-switch d-flex justify-content-center">
                                <input class="form-check-input" type="checkbox" name="has_action" id="hasAction" value="Y">
                            </div>
                            <label for="hasAction" class="d-block mt-2" style="cursor: pointer;">
                                <i class="fas fa-magic fa-3x text-warning mb-2"></i>
                                <h6>Action Viewer</h6>
                                <small class="text-muted">액션 효과 지원</small>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
  <!-- ZIP 파일 업로드 -->
        <div id="zipUploadArea">
            <div class="file-upload-box" id="comicUploadBox" onclick="document.getElementById('comicFile').click()">
                <i class="fas fa-cloud-upload-alt fa-4x text-primary mb-3"></i>
                <h5>클릭하여 ZIP 파일 선택</h5>
                <p class="text-muted mb-2">
                    만화 이미지들을 ZIP으로 압축하여 업로드<br>
                    <small>(JPG, PNG 등)</small>
                </p>
                <div id="fileInfo" class="mt-3"></div>
                <p class="text-muted mt-2">
                    <i class="fas fa-info-circle"></i> 최대 <strong>1GB</strong> (1,024MB)
                </p>
            </div>
            <input type="file" name="comic_file" id="comicFile" class="d-none" 
                   accept=".zip" onchange="showFileInfo(this)">
            
            <!-- AVF 파일 (Action Viewer용) -->
            <div id="avfUploadArea" style="display: none;" class="mt-3">
                <label class="form-label">
                    <i class="fas fa-magic text-warning"></i> frame.avf 파일 (선택)
                </label>
                <input type="file" name="avf_file" id="avfFile" class="form-control" 
                       accept=".avf" onchange="showAvfInfo(this)">
                <div class="form-hint">
                    Action Viewer 데이터 파일 (ZIP 안에 포함되어 있으면 자동 감지)
                </div>
                <div id="avfInfo" class="mt-2"></div>
            </div>
        </div>
        
        <!-- 이미지 여러 개 업로드 -->
        <div id="imagesUploadArea" style="display: none;">
            <div class="file-upload-box" onclick="document.getElementById('comicImages').click()">
                <i class="fas fa-images fa-4x text-success mb-3"></i>
                <h5>클릭하여 이미지 여러 개 선택</h5>
                <p class="text-muted mb-2">
                    Ctrl 또는 Shift 키로 여러 파일 선택 가능<br>
                    <small>(JPG, PNG, WebP 등)</small>
                </p>
                <div id="imagesInfo" class="mt-3"></div>
                <p class="text-muted mt-2">
                    <i class="fas fa-info-circle"></i> 최대 <strong>50개</strong> 파일, 총 1GB
                </p>
            </div>
            <input type="file" name="comic_images[]" id="comicImages" class="d-none" 
                   accept="image/*" multiple onchange="showImagesInfo(this)">
        </div>
        
        <div class="form-hint mt-2">
            <i class="fas fa-lightbulb"></i> 
            <strong>팁:</strong> 만화 파일은 나중에 추가할 수도 있습니다. (표지와 정보만 먼저 등록 가능)
        </div>
    </div>
    
    <!-- 3. 가격 정보 -->
    <div class="upload-section">
        <h3 class="section-title">
            <i class="fas fa-dollar-sign"></i> 가격 정보
        </h3>
        
        <div class="row">
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label required-field">정가</label>
                    <div class="input-group input-group-lg">
                        <input type="number" name="normal_price" id="normalPrice" class="form-control" required 
                               placeholder="10000" min="0" value="0">
                        <span class="input-group-text">원</span>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">할인율</label>
                    <div class="input-group input-group-lg">
                        <input type="number" name="discount_rate" id="discountRate" class="form-control" 
                               placeholder="0" min="0" max="100" value="0">
                        <span class="input-group-text">%</span>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label required-field">판매가</label>
                    <div class="input-group input-group-lg">
                        <input type="number" name="sale_price" id="salePrice" class="form-control" required 
                               placeholder="10000" min="0" readonly>
                        <span class="input-group-text">원</span>
                    </div>
                    <div class="form-hint">자동 계산됩니다</div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- 4. 카테고리 & 설정 -->
    <div class="upload-section">
        <h3 class="section-title">
            <i class="fas fa-tags"></i> 카테고리 & 설정
        </h3>
        
        <div class="row">
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">코믹스 브랜드</label>
                    <select name="comics_brand" class="form-select form-select-lg">
                        <option value="">선택 안 함</option>
                        <option value="Marvel">Marvel</option>
                        <option value="DC">DC</option>
                        <option value="Image">Image</option>
                        <option value="Dark Horse">Dark Horse</option>
                        <option value="기타">기타</option>
                    </select>
                </div>
            </div>

            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">장르 선택</label>
                    <select name="genres[]" class="form-select form-select-lg" multiple size="5">
                        <?php
                        $genres_query = $pdo->query("SELECT * FROM bt_genres ORDER BY genre_order");
                        $genres_list = $genres_query->fetchAll();
                        foreach ($genres_list as $g): ?>
                            <option value="<?= $g['genre_id'] ?>"><?= htmlspecialchars($g['genre_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <small class="text-muted">Ctrl 키를 누르고 클릭하면 여러 개 선택 가능</small>
                </div>
            </div>

            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">출판일</label>
                    <input type="date" name="published_dt" class="form-control form-control-lg">
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label required-field">판매 상태</label>
                    <select name="book_status" class="form-select form-select-lg" required>
                        <option value="1" selected>판매중</option>
                        <option value="2">품절</option>
                        <option value="0">판매중단</option>
                    </select>
                </div>
            </div>
        </div>
<!-- 추가 정보 -->
        <div class="row mt-4">
            <div class="col-12">
                <h5 class="section-title">📚 추가 설정</h5>
            </div>
            
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">미리보기 페이지 수</label>
                    <input type="number" name="preview_pages" class="form-control form-control-lg" 
                           min="0" placeholder="예: 10">
                    <small class="text-muted">사용자가 무료로 볼 수 있는 페이지 수</small>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">연령 등급</label>
                    <select name="age_rating" class="form-select form-select-lg">
                        <option value="전체이용가" selected>전체이용가</option>
                        <option value="12세이용가">12세이용가</option>
                        <option value="15세이용가">15세이용가</option>
                        <option value="19세이용가">19세이용가</option>
                    </select>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">무료 체험 기간 (일)</label>
                    <input type="number" name="free_trial_days" class="form-control form-control-lg" 
                           min="0" placeholder="예: 7">
                    <small class="text-muted">구매 후 무료로 이용할 수 있는 일수</small>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">시리즈명</label>
                    <input type="text" name="series_name" class="form-control form-control-lg" 
                           placeholder="예: Spider-Man">
                    <small class="text-muted">같은 시리즈의 책들을 묶어줍니다</small>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">시리즈 권수</label>
                    <input type="number" name="series_volume" class="form-control form-control-lg" 
                           min="1" placeholder="예: 1">
                    <small class="text-muted">이 책이 시리즈의 몇 권째인지</small>
                </div>
            </div>
        </div>
<div class="row">
            <div class="col-md-6">
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" name="is_free" id="isFree" value="Y">
                    <label class="form-check-label" for="isFree">
                        <strong>무료 제공</strong>
                        <small class="text-muted d-block">무료로 제공하는 콘텐츠</small>
                    </label>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" name="is_pkg" id="isPkg" value="Y">
                    <label class="form-check-label" for="isPkg">
                        <strong>패키지 상품</strong>
                        <small class="text-muted d-block">여러 권을 묶은 패키지</small>
                    </label>
                </div>
            </div>
        </div>
    </div>
    
    <!-- 제출 버튼 -->
    <div class="d-flex justify-content-end gap-3 mb-5">
        <a href="list.php" class="btn btn-lg btn-outline-secondary px-5">
            <i class="fas fa-times"></i> 취소
        </a>
        <button type="submit" class="btn btn-lg btn-primary px-5" id="submitBtn">
            <i class="fas fa-check"></i> 책 등록하기
        </button>
    </div>
</form>

<script>
// 이미지 미리보기
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" alt="표지 미리보기">`;
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}

// 만화 파일 정보 표시
function showFileInfo(input) {
    const box = document.getElementById('comicUploadBox');
    const fileInfo = document.getElementById('fileInfo');
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
        
        // 1GB 체크
        if (file.size > 1024 * 1024 * 1024) {
            alert('파일 크기가 1GB를 초과합니다!');
            input.value = '';
            return;
        }
        
        box.classList.add('has-file');
        fileInfo.innerHTML = `
            <div class="alert alert-success mb-0">
                <i class="fas fa-check-circle"></i>
                <strong>${file.name}</strong><br>
                <small>크기: ${sizeMB} MB</small>
            </div>
        `;
    }
}

// 이미지 여러 개 정보 표시
function showImagesInfo(input) {
    const imagesInfo = document.getElementById('imagesInfo');
    
    if (input.files && input.files.length > 0) {
        let totalSize = 0;
        for (let file of input.files) {
            totalSize += file.size;
        }
        
        const sizeMB = (totalSize / (1024 * 1024)).toFixed(2);
        
        // 1GB 체크
        if (totalSize > 1024 * 1024 * 1024) {
            alert('총 파일 크기가 1GB를 초과합니다!');
            input.value = '';
            return;
        }
        
        // 50개 체크
        if (input.files.length > 50) {
            alert('최대 50개 파일까지만 업로드 가능합니다!');
            input.value = '';
            return;
        }
        
        imagesInfo.innerHTML = `
            <div class="alert alert-success mb-0">
                <i class="fas fa-check-circle"></i>
                <strong>${input.files.length}개 파일 선택됨</strong><br>
                <small>총 크기: ${sizeMB} MB</small>
            </div>
        `;
    }
}

// AVF 파일 정보 표시
function showAvfInfo(input) {
    const avfInfo = document.getElementById('avfInfo');
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const sizeKB = (file.size / 1024).toFixed(2);
        
        avfInfo.innerHTML = `
            <div class="alert alert-info mb-0">
                <i class="fas fa-magic"></i>
                <strong>${file.name}</strong><br>
                <small>크기: ${sizeKB} KB</small>
            </div>
        `;
    }
}
// 업로드 방식 전환
document.querySelectorAll('input[name="upload_type"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const zipArea = document.getElementById('zipUploadArea');
        const imagesArea = document.getElementById('imagesUploadArea');
        
        if (this.value === 'zip') {
            zipArea.style.display = 'block';
            imagesArea.style.display = 'none';
            document.getElementById('comicImages').value = '';
        } else {
            zipArea.style.display = 'none';
            imagesArea.style.display = 'block';
            document.getElementById('comicFile').value = '';
        }
    });
});

// Action Viewer 체크
document.getElementById('hasAction').addEventListener('change', function() {
    const avfArea = document.getElementById('avfUploadArea');
    avfArea.style.display = this.checked ? 'block' : 'none';
});

// 판매가 자동 계산
function calculateSalePrice() {
    const normalPrice = parseInt(document.getElementById('normalPrice').value) || 0;
    const discountRate = parseInt(document.getElementById('discountRate').value) || 0;
    const salePrice = normalPrice - (normalPrice * discountRate / 100);
    document.getElementById('salePrice').value = Math.round(salePrice);
}

document.getElementById('normalPrice').addEventListener('input', calculateSalePrice);
document.getElementById('discountRate').addEventListener('input', calculateSalePrice);

// 무료 체크 시 가격 0으로
document.getElementById('isFree').addEventListener('change', function() {
    if (this.checked) {
        document.getElementById('normalPrice').value = 0;
        document.getElementById('discountRate').value = 0;
        document.getElementById('salePrice').value = 0;
    }
});

// 폼 제출
document.getElementById('bookForm').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> 업로드 중...';
});
</script>

<?php require_once '../layout/footer.php'; ?>
