<?php
require_once '../../wps-config.php';
require_once '../../wps-settings.php';

if (!isset($_SESSION['publisher_id'])) {
    $_SESSION['publisher_id'] = 1;
}
$publisher_id = $_SESSION['publisher_id'];

// 주문번호 받기
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

// 주문 상세 조회
$query = "
    SELECT 
        o.*,
        u.user_login as buyer_name,
        u.user_email as buyer_email,
        i.*,
        b.publisher_id
    FROM bt_order o
    INNER JOIN bt_order_item i ON o.order_id = i.order_id
    LEFT JOIN bt_users u ON o.user_id = u.ID
    LEFT JOIN bt_books b ON i.book_id = b.ID
    WHERE o.order_id = ? AND b.publisher_id = ?
";

$stmt = $wdb->prepare($query);
$stmt->bind_param('ii', $order_id, $publisher_id);
$stmt->execute();
$order = $wdb->get_row($stmt);

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
    <title>주문 상세 - HeroComics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include "../includes/sidebar.php"; ?>

<div class="main-content" style="margin-left: 270px; padding: 20px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>🛒 주문 상세</h1>
        <a href="list.php" class="btn btn-outline-secondary">← 목록으로</a>
    </div>
    
    <?php if ($order): ?>
    <!-- 주문 정보 -->
    <div class="card mb-3">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">주문 정보</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>주문번호:</strong> <?php echo $order['order_id']; ?></p>
                    <p><strong>주문일시:</strong> <?php echo date('Y-m-d H:i:s', strtotime($order['created_dt'])); ?></p>
                    <p><strong>주문상태:</strong> 
                        <?php 
                        $status = get_order_status($order['order_status']);
                        $badge_class = ($status == '결제완료') ? 'bg-success' : 'bg-danger';
                        ?>
                        <span class="badge <?php echo $badge_class; ?>"><?php echo $status; ?></span>
                    </p>
                </div>
                <div class="col-md-6">
                    <p><strong>구매자:</strong> <?php echo htmlspecialchars($order['buyer_name']); ?></p>
                    <p><strong>이메일:</strong> <?php echo htmlspecialchars($order['buyer_email']); ?></p>
                    <p><strong>IP:</strong> <?php echo $order['remote_ip']; ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- 책 정보 -->
    <div class="card mb-3">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">구매 도서</h5>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>책 제목</th>
                        <th>원가</th>
                        <th>판매가</th>
                        <th>할인율</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo htmlspecialchars($order['book_title']); ?></td>
                        <td>₩<?php echo number_format($order['original_price']); ?></td>
                        <td>₩<?php echo number_format($order['sale_price']); ?></td>
                        <td><?php echo $order['book_dc_rate']; ?>%</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- 결제 정보 -->
    <div class="card mb-3">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">결제 정보</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>총 금액:</strong> ₩<?php echo number_format($order['total_amount']); ?></p>
                    <p><strong>할인 금액:</strong> ₩<?php echo number_format($order['discount_amount']); ?></p>
                    <?php if (!empty($order['coupon_code'])): ?>
                    <p><strong>쿠폰:</strong> <?php echo htmlspecialchars($order['coupon_code']); ?></p>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <p><strong>사이버캐시:</strong> ₩<?php echo number_format($order['cybercash_paid']); ?></p>
                    <p><strong>사이버포인트:</strong> ₩<?php echo number_format($order['cyberpoint_paid']); ?></p>
                    <p><strong class="text-primary">최종 결제액:</strong> <span class="text-primary fs-4">₩<?php echo number_format($order['total_paid']); ?></span></p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- 액션 버튼 -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">주문 관리</h5>
            <?php if ($order['order_status'] == 1): ?>
            <button class="btn btn-danger" onclick="cancelOrder(<?php echo $order_id; ?>)">주문 취소</button>
            <button class="btn btn-warning" onclick="refundOrder(<?php echo $order_id; ?>)">환불 처리</button>
            <?php else: ?>
            <p class="text-muted">이미 처리된 주문입니다.</p>
            <?php endif; ?>
        </div>
    </div>
    
    <?php else: ?>
    <div class="alert alert-warning">
        주문을 찾을 수 없거나 권한이 없습니다.
    </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
