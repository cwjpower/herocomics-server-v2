<?php
require_once '../../wps-config.php';
require_once '../../wps-settings.php';

if (!isset($_SESSION['publisher_id'])) {
    $_SESSION['publisher_id'] = 1;
}
$publisher_id = $_SESSION['publisher_id'];

// 검색 조건
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? intval($_GET['status']) : -1;
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';

// WHERE 조건 구성
$where_conditions = ["b.publisher_id = ?"];
$params = [$publisher_id];
$param_types = 'i';

if (!empty($search)) {
    $where_conditions[] = "(o.order_id LIKE ? OR u.user_login LIKE ? OR i.book_title LIKE ?)";
    $search_param = "%{$search}%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $param_types .= 'sss';
}

if ($status_filter >= 0) {
    $where_conditions[] = "o.order_status = ?";
    $params[] = $status_filter;
    $param_types .= 'i';
}

if (!empty($date_from)) {
    $where_conditions[] = "DATE(o.created_dt) >= ?";
    $params[] = $date_from;
    $param_types .= 's';
}

if (!empty($date_to)) {
    $where_conditions[] = "DATE(o.created_dt) <= ?";
    $params[] = $date_to;
    $param_types .= 's';
}

$where_clause = implode(' AND ', $where_conditions);

// 주문 목록 조회
$query = "
    SELECT 
        o.order_id,
        o.created_dt,
        o.order_status,
        u.user_login as buyer_name,
        u.user_email as buyer_email,
        i.book_title,
        i.sale_price as amount,
        o.coupon_code,
        o.coupon_discount,
        o.discount_amount,
        o.cybercash_paid,
        o.cyberpoint_paid,
        o.total_paid
    FROM bt_order o
    INNER JOIN bt_order_item i ON o.order_id = i.order_id
    LEFT JOIN bt_users u ON o.user_id = u.ID
    LEFT JOIN bt_books b ON i.book_id = b.ID
    WHERE $where_clause
    ORDER BY o.created_dt DESC
    LIMIT 50
";

$stmt = $wdb->prepare($query);
$stmt->bind_param($param_types, ...$params);
$stmt->execute();
$orders = $wdb->get_results($stmt);

// 주문 상태 변환
function get_order_status($status) {
    switch($status) {
        case 0: return '대기';
        case 1: return '결제완료';
        case 2: return '취소';
        case 3: return '환불';
        default: return '알 수 없음';
    }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>주문 관리 - HeroComics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include "../includes/sidebar.php"; ?>

<div class="main-content" style="margin-left: 270px; padding: 20px;">
    <h1>🛒 주문 관리</h1>
    
    <!-- 필터 -->
    <form method="GET" action="list.php">
    <div class="card mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <label>날짜</label>
                    <input type="date" class="form-control" name="date_from" value="<?php echo htmlspecialchars($date_from); ?>">
                </div>
                <div class="col-md-3">
                    <label>상태</label>
                    <select class="form-control" name="status">
                        <option value="-1">전체</option>
                        <option value="1" <?php echo $status_filter == 1 ? 'selected' : ''; ?>>결제완료</option>
                        <option value="2" <?php echo $status_filter == 2 ? 'selected' : ''; ?>>취소</option>
                        <option value="3" <?php echo $status_filter == 3 ? 'selected' : ''; ?>>환불</option>
                        <option>결제완료</option>
                        <option>취소</option>
                        <option>환불</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>검색</label>
                    <input type="text" class="form-control" name="search" placeholder="주문번호/구매자/책제목" value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-2">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">검색</button>
                </div>
            </div>
        </div>
    </div>
    </form>
    
    <!-- 주문 목록 -->
    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>주문번호</th>
                        <th>구매자</th>
                        <th>책 제목</th>
                        <th>결제방법</th>
                        <th>원가</th>
                        <th>할인</th>
                        <th>결제액</th>
                        <th>상태</th>
                        <th>주문일시</th>
                        <th>상세</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($orders)): ?>
                        <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?php echo $order['order_id']; ?></td>
                            <td>
                                <?php echo htmlspecialchars($order['buyer_name']); ?><br>
                                <small class="text-muted"><?php echo htmlspecialchars($order['buyer_email']); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($order['book_title']); ?></td>
                            <td>
                                <?php if (!empty($order['coupon_code'])): ?>
                                    🎟️ 쿠폰: <?php echo htmlspecialchars($order['coupon_code']); ?><br>
                                    <small class="text-muted">(<?php echo number_format($order['coupon_discount']); ?>원 할인)</small>
                                <?php elseif ($order['cybercash_paid'] > 0): ?>
                                    💳 사이버캐시<br>
                                    <small class="text-muted">(<?php echo number_format($order['cybercash_paid']); ?>원)</small>
                                <?php elseif ($order['cyberpoint_paid'] > 0): ?>
                                    🎫 사이버포인트<br>
                                    <small class="text-muted">(<?php echo number_format($order['cyberpoint_paid']); ?>원)</small>
                                <?php else: ?>
                                    💰 일반결제
                                <?php endif; ?>
                            </td>
                            <td>₩<?php echo number_format($order['amount']); ?></td>
                            <td class="text-danger">-₩<?php echo number_format($order['discount_amount']); ?></td>
                            <td class="fw-bold text-primary">₩<?php echo number_format($order['total_paid']); ?></td>
                            <td>
                                <?php 
                                $status = get_order_status($order['order_status']);
                                $badge_class = ($status == '결제완료') ? 'bg-success' : (($status == '취소' || $status == '환불') ? 'bg-danger' : 'bg-warning');
                                ?>
                                <span class="badge <?php echo $badge_class; ?>"><?php echo $status; ?></span>
                            </td>
                            <td><?php echo date('Y-m-d H:i', strtotime($order['created_dt'])); ?></td>
                            <td>
                                <a href="detail.php?order_id=<?php echo $order['order_id']; ?>" class="btn btn-sm btn-outline-primary">상세</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-5">주문 내역이 없습니다.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>