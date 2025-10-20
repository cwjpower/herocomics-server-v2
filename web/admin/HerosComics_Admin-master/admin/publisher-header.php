<?php
require_once '../wps-config.php';
session_start();

// 출판사 권한 체크
if($_SESSION['user_level'] != '7') {
    die('<script>alert("출판사 관리자만 접근 가능합니다.");location.href="login.php";</script>');
}

$publisher_id = $_SESSION['publisher_id'];
$mysqli = new mysqli('herocomics-mariadb', 'root', 'rootpass', 'herocomics');

// 출판사 정보 조회
$pub_query = "SELECT * FROM bt_publishers WHERE publisher_id = $publisher_id";
$publisher = $mysqli->query($pub_query)->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title><?php echo $publisher['publisher_name']; ?> - HeroComics CMS</title>
    <link rel="stylesheet" href="<?php echo CSS_URL ?>/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo CSS_URL ?>/AdminLTE.css">
    <link rel="stylesheet" href="<?php echo CSS_URL ?>/skin-blue.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
    <header class="main-header">
        <a href="<?php echo ADMIN_URL ?>/publisher/" class="logo">
            <span class="logo-mini"><?php echo substr($publisher['publisher_code'], 0, 3); ?></span>
            <span class="logo-lg"><?php echo $publisher['publisher_name']; ?></span>
        </a>
        
        <nav class="navbar navbar-static-top">
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <li><a href="<?php echo ADMIN_URL ?>/publisher/">대시보드</a></li>
                    <li><a href="<?php echo ADMIN_URL ?>/publisher/series_list.php">시리즈 관리</a></li>
                    <li><a href="<?php echo ADMIN_URL ?>/publisher/volume_upload.php">콘텐츠 등록</a></li>
                    <li><a href="<?php echo ADMIN_URL ?>/publisher/sales.php">매출/정산</a></li>
                    <li style="float:right;"><a href="<?php echo ADMIN_URL ?>/logout.php" style="background:#d9534f; color:white;">로그아웃</a></li>
                </ul>
            </div>
        </nav>
    </header>
