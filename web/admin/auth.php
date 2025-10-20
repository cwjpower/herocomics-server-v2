<?php
/**
 * Auth.php - HerosComics CMS 리다이렉트
 * 기존 auth.php를 HerosComics_Admin-master 시스템으로 통합
 */

session_start();

// 이미 HerosComics 시스템으로 로그인되어 있으면 대시보드로
if (!empty($_SESSION['login']) && !empty($_SESSION['login']['userid'])) {
    $user_level = $_SESSION['login']['user_level'] ?? 1;
    
    if ($user_level == 10) {
        // 관리자 레벨 10
        header("Location: HerosComics_Admin-master/admin/admin.php");
    } else {
        // 에이전트
        header("Location: HerosComics_Admin-master/admin/agent.php");
    }
    exit;
}

// 로그인 안 되어 있으면 로그인 페이지로
header("Location: HerosComics_Admin-master/admin/login.php");
exit;
?>
