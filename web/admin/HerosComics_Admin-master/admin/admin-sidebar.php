<aside class="main-sidebar" style="position: fixed; background: #222d32; width: 230px; height: 100%;">
    <section class="sidebar" style="padding: 10px;">
        <ul class="sidebar-menu" style="list-style: none; padding: 0;">
            <li class="header" style="color: #4b646f; padding: 10px;">메인 메뉴</li>
            
            <!-- 대시보드 -->
            <li><a href="../admin.php" style="color: #b8c7ce; display: block; padding: 10px;">
                <i class="fa fa-dashboard"></i> <span>대시보드</span>
            </a></li>
            
            <!-- 회원 관리 -->
            <li><a href="../users/" style="color: #b8c7ce; display: block; padding: 10px;">
                <i class="fa fa-users"></i> <span>회원 관리</span>
            </a></li>
            
            <!-- 출판사 관리 (신규) -->
            <li class="treeview">
                <a href="#" style="color: #b8c7ce; display: block; padding: 10px;">
                    <i class="fa fa-building"></i> <span>출판사 관리</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu" style="list-style: none; padding-left: 20px;">
                    <li><a href="../publishers/publisher_list.php" style="color: #8aa4af; display: block; padding: 5px;">출판사 목록</a></li>
                    <li><a href="../publishers/publisher_add.php" style="color: #8aa4af; display: block; padding: 5px;">출판사 추가</a></li>
                    <li><a href="../publishers/publisher_accounts.php" style="color: #8aa4af; display: block; padding: 5px;">계정 관리</a></li>
                    <li><a href="../publishers/publisher_stats.php" style="color: #8aa4af; display: block; padding: 5px;">통계/정산</a></li>
                </ul>
            </li>
            
            <!-- 책 관리 -->
            <li class="treeview">
                <a href="#" style="color: #b8c7ce; display: block; padding: 10px;">
                    <i class="fa fa-book"></i> <span>책 관리</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu" style="list-style: none; padding-left: 20px;">
                    <li><a href="../books/" style="color: #8aa4af; display: block; padding: 5px;">책 목록</a></li>
                    <li><a href="../books/book_new.php" style="color: #8aa4af; display: block; padding: 5px;">책 등록</a></li>
                    <li><a href="../books/category.php" style="color: #8aa4af; display: block; padding: 5px;">카테고리</a></li>
                </ul>
            </li>
            
            <!-- 배너/컨텐츠 -->
            <li class="treeview">
                <a href="#" style="color: #b8c7ce; display: block; padding: 10px;">
                    <i class="fa fa-image"></i> <span>컨텐츠 관리</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu" style="list-style: none; padding-left: 20px;">
                    <li><a href="../pages/banner.php" style="color: #8aa4af; display: block; padding: 5px;">배너 관리</a></li>
                    <li><a href="../pages/news_list.php" style="color: #8aa4af; display: block; padding: 5px;">뉴스 관리</a></li>
                    <li><a href="../pages/curation.php" style="color: #8aa4af; display: block; padding: 5px;">큐레이팅</a></li>
                </ul>
            </li>
            
            <!-- 통계/정산 -->
            <li class="treeview">
                <a href="#" style="color: #b8c7ce; display: block; padding: 10px;">
                    <i class="fa fa-bar-chart"></i> <span>통계/정산</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu" style="list-style: none; padding-left: 20px;">
                    <li><a href="../statistics/" style="color: #8aa4af; display: block; padding: 5px;">판매 통계</a></li>
                    <li><a href="../settle/" style="color: #8aa4af; display: block; padding: 5px;">정산 관리</a></li>
                </ul>
            </li>
            
        </ul>
    </section>
</aside>
