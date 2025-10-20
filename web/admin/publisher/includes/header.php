<!-- 상단 보라색 헤더 -->
<div class="top-header">
    <div class="header-left">
        <span class="publisher-badge">테스트 출판사</span>
    </div>
    <div class="header-right">
        <div class="notification">
            <span class="bell-icon">🔔</span>
            <span class="badge">3</span>
        </div>
        <div class="user-menu">
            <span class="user-icon">👤</span>
            <span class="user-name">관리자</span>
            <span class="dropdown-arrow">▼</span>
        </div>
    </div>
</div>

<style>
.top-header {
    position: fixed;
    top: 0;
    left: 240px;
    right: 0;
    height: 60px;
    background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    z-index: 999;
}

.header-left .publisher-badge {
    background: rgba(255,255,255,0.2);
    color: white;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 500;
}

.header-right {
    display: flex;
    align-items: center;
    gap: 20px;
}

.notification {
    position: relative;
    cursor: pointer;
    padding: 8px;
}

.bell-icon {
    font-size: 20px;
}

.notification .badge {
    position: absolute;
    top: 0;
    right: 0;
    background: #ff4757;
    color: white;
    font-size: 11px;
    padding: 2px 6px;
    border-radius: 10px;
    font-weight: bold;
}

.user-menu {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    padding: 8px 12px;
    border-radius: 8px;
    transition: all 0.3s;
}

.user-menu:hover {
    background: rgba(255,255,255,0.1);
}

.user-icon {
    font-size: 18px;
}

.user-name {
    color: white;
    font-size: 14px;
    font-weight: 500;
}

.dropdown-arrow {
    color: white;
    font-size: 10px;
}

/* 메인 컨텐츠 여백 (상단바 때문에) */
.main-content {
    margin-top: 60px;
    margin-left: 240px;
    padding: 20px;
}
</style>
