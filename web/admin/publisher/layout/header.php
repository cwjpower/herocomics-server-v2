<?php
// 세션 시작 (이미 시작되지 않았다면)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 로그인 체크 (임시 비활성화)
$is_logged_in = true; // 나중에 세션 체크로 변경
$publisher_name = $_SESSION['publisher_name'] ?? '테스트 출판사';
$user_name = $_SESSION['user_name'] ?? '관리자';

// 현재 페이지 확인
$current_page = basename($_SERVER['PHP_SELF']);
$current_dir = basename(dirname($_SERVER['PHP_SELF']));
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'HeroComics 출판사 CMS' ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #2563eb;
            --sidebar-width: 250px;
        }
        
        body {
            font-family: 'Noto Sans KR', sans-serif;
            background: #f8f9fa;
        }
        
        /* 상단 네비게이션 */
        .top-navbar {
            background: white;
            border-bottom: 1px solid #e5e7eb;
            padding: 0;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            height: 60px;
        }
        
        .navbar-brand {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary-color) !important;
            padding: 0 2rem;
        }
        
        .navbar-brand i {
            margin-right: 0.5rem;
        }
        
        /* 사이드바 */
        .sidebar {
            position: fixed;
            top: 60px;
            left: 0;
            bottom: 0;
            width: var(--sidebar-width);
            background: white;
            border-right: 1px solid #e5e7eb;
            overflow-y: auto;
            z-index: 999;
        }
        
        .sidebar-menu {
            list-style: none;
            padding: 1rem 0;
            margin: 0;
        }
        
        .sidebar-menu li {
            margin: 0;
        }
        
        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: #6b7280;
            text-decoration: none;
            transition: all 0.2s;
        }
        
        .sidebar-menu a:hover {
            background: #f3f4f6;
            color: var(--primary-color);
        }
        
        .sidebar-menu a.active {
            background: #eff6ff;
            color: var(--primary-color);
            border-right: 3px solid var(--primary-color);
            font-weight: 600;
        }
        
        .sidebar-menu a i {
            width: 20px;
            margin-right: 0.75rem;
            font-size: 1.1rem;
        }
        
        /* 메인 컨텐츠 */
        .main-content {
            margin-left: var(--sidebar-width);
            margin-top: 60px;
            padding: 2rem;
            min-height: calc(100vh - 60px);
        }
        
        /* 사용자 드롭다운 */
        .user-dropdown {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: #f8f9fa;
            border-radius: 8px;
            cursor: pointer;
        }
        
        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
        
        .publisher-badge {
            background: #fef3c7;
            color: #92400e;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.875rem;
            font-weight: 600;
        }
        
        /* 모바일 대응 */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s;
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .mobile-menu-toggle {
                display: block !important;
            }
        }
        
        .mobile-menu-toggle {
            display: none;
        }
    </style>
</head>
<body>
<?php include dirname(__FILE__) . "/../includes/sidebar.php"; ?>

    <!-- 상단 네비게이션 -->
    <nav class="navbar top-navbar">
        <div class="container-fluid">
            <div class="d-flex align-items-center w-100">
                <!-- 로고 -->
                <button class="btn mobile-menu-toggle me-2" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <a class="navbar-brand" href="/admin/publisher/">
                    <i class="fas fa-book-open"></i>
                    HeroComics 출판사
                </a>
                
                <!-- 우측 메뉴 -->
                <div class="ms-auto d-flex align-items-center gap-3">
                    <!-- 출판사 배지 -->
                    <span class="publisher-badge">
                        <?= htmlspecialchars($publisher_name) ?>
                    </span>
                    
                    <!-- 알림 -->
                    <div class="position-relative">
                        <button class="btn btn-link text-dark position-relative">
                            <i class="fas fa-bell fa-lg"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                3
                            </span>
                        </button>
                    </div>
                    
                    <!-- 사용자 드롭다운 -->
                    <div class="dropdown">
                        <button class="btn user-dropdown" data-bs-toggle="dropdown">
                            <div class="user-avatar">
                                <?= strtoupper(substr($user_name, 0, 1)) ?>
                            </div>
                            <div class="text-start d-none d-md-block">
                                <div style="font-size: 0.875rem; font-weight: 600;">
                                    <?= htmlspecialchars($user_name) ?>
                                </div>
                                <div style="font-size: 0.75rem; color: #6b7280;">
                                    출판사 관리자
                                </div>
                            </div>
                            <i class="fas fa-chevron-down fa-sm"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="/admin/publisher/settings/profile.php">
                                <i class="fas fa-user me-2"></i> 내 정보
                            </a></li>
                            <li><a class="dropdown-item" href="/admin/publisher/settings/company.php">
                                <i class="fas fa-building me-2"></i> 출판사 정보
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="/admin/logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i> 로그아웃
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- 사이드바 -->
    <!-- sidebar는 includes/sidebar.php에서 로드 -->
    
    <!-- 메인 컨텐츠 영역 시작 -->
    <main class="main-content">
        <!-- 페이지 컨텐츠는 여기에 들어감 -->
        
<script>
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('show');
}
</script>
