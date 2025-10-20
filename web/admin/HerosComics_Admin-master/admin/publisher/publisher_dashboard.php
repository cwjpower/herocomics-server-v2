<?php
require_once '../../wps-config.php';
session_start();

// 출판사 권한 체크 (user_level = 7)
if($_SESSION['user_level'] != '7') {
    die('<script>alert("출판사 관리자만 접근 가능합니다.");location.href="../login.php";</script>');
}

$mysqli = new mysqli('herocomics-mariadb', 'root', 'rootpass', 'herocomics');
$publisher_id = $_SESSION['publisher_id'];

// 출판사 정보 조회
$pub_query = "SELECT * FROM bt_publishers WHERE publisher_id = $publisher_id";
$pub_result = $mysqli->query($pub_query);
$publisher = $pub_result->fetch_assoc();

// 통계 조회
$stats_query = "SELECT 
    COUNT(DISTINCT s.series_id) as total_series,
    COUNT(DISTINCT v.volume_id) as total_volumes
FROM bt_series s
LEFT JOIN bt_volumes v ON s.series_id = v.series_id
WHERE s.publisher_id = $publisher_id";
$stats = $mysqli->query($stats_query)->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo $publisher['publisher_name']; ?> - 관리자</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        .header { background: #3c8dbc; color: white; padding: 15px; }
        .sidebar { background: #222d32; min-height: 100vh; padding: 0; }
        .sidebar a { color: #b8c7ce; display: block; padding: 10px; }
        .sidebar a:hover { background: #1e282c; color: white; text-decoration: none; }
        .content { padding: 20px; }
        .info-box { background: white; border-radius: 3px; padding: 10px; margin-bottom: 20px; box-shadow: 0 1px 1px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="header">
        <div class="row">
            <div class="col-md-10">
                <h3><?php echo $publisher['publisher_name']; ?> 관리 시스템</h3>
            </div>
            <div class="col-md-2 text-right">
                <a href="../logout.php" class="btn btn-danger btn-sm">로그아웃</a>
            </div>
        </div>
    </div>
    
    <div class="container-fluid">
        <div class="row">
            <!-- 사이드바 -->
            <div class="col-md-2 sidebar">
                <h4 style="color: #4b646f; padding: 10px;">메뉴</h4>
                <a href="publisher_dashboard.php"><i class="fa fa-dashboard"></i> 대시보드</a>
                <a href="publisher_series.php"><i class="fa fa-book"></i> 시리즈 관리</a>
                <a href="publisher_upload.php"><i class="fa fa-upload"></i> 신규 등록</a>
                <a href="publisher_sales.php"><i class="fa fa-bar-chart"></i> 매출 현황</a>
                <a href="publisher_settlement.php"><i class="fa fa-money"></i> 정산 내역</a>
                <a href="publisher_notice.php"><i class="fa fa-bell"></i> 공지사항</a>
            </div>
            
            <!-- 메인 콘텐츠 -->
            <div class="col-md-10 content">
                <h1>대시보드</h1>
                
                <div class="row">
                    <div class="col-md-3">
                        <div class="info-box">
                            <h4>총 시리즈</h4>
                            <h2><?php echo $stats['total_series']; ?>개</h2>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <h4>총 권수</h4>
                            <h2><?php echo $stats['total_volumes']; ?>권</h2>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <h4>이번달 매출</h4>
                            <h2>₩0</h2>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <h4>정산 예정액</h4>
                            <h2>₩0</h2>
                        </div>
                    </div>
                </div>
                
                <div class="panel panel-default">
                    <div class="panel-heading">최근 등록 시리즈</div>
                    <div class="panel-body">
                        <p>등록된 시리즈가 없습니다.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
