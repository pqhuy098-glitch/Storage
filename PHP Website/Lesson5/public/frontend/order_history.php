<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/helper.php';

if (!isset($_SESSION['user']['id'])) {
    redirect('../auth/login.php');
    exit;
}

$userId = $_SESSION['user']['id'];

$sql = $con->prepare("
    SELECT 
        o.id AS order_id,
        o.total_amount,
        o.status,
        o.shipping_address,
        o.order_date,
        oi.quantity,
        oi.price_at_time,
        p.name AS product_name,
        p.image_url AS product_image
    FROM orders o
    JOIN order_items oi ON o.id = oi.order_id
    JOIN products p ON oi.product_id = p.id
    WHERE o.user_id = ?
    ORDER BY o.order_date DESC, o.id DESC
");

$sql->bind_param("i", $userId);
$sql->execute();
$rawOrders = $sql->get_result()->fetch_all(MYSQLI_ASSOC);

$orders = [];
foreach ($rawOrders as $row) {
    $orderId = $row['order_id'];
    if (!isset($orders[$orderId])) {
        $orders[$orderId] = [
            'id'               => $row['order_id'],
            'total_amount'     => $row['total_amount'],
            'status'           => $row['status'],
            'shipping_address' => $row['shipping_address'],
            'order_date'       => $row['order_date'],
            'items'            => []
        ];
    }
    
    $orders[$orderId]['items'][] = [
        'product_name'  => $row['product_name'],
        'product_image' => $row['product_image'] ?? 'assets/img/placeholder.svg',
        'quantity'      => $row['quantity'],
        'price_at_time' => $row['price_at_time']
    ];
}

include __DIR__ . '/../../includes/frontend_header.php';
?>

<div class="container">
    <h1 style="margin-bottom: 24px;">Order History</h1>

    <?php if (empty($orders)): ?>
        <div class="alert alert-info">
            You haven't placed any orders yet.
        </div>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <div class="card" style="margin-bottom: 20px; padding: 0; overflow: hidden;">
                <div style="background: var(--light); padding: 12px 16px; border-bottom: 1px solid #d0d7de; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
                    <div>
                        <strong>Order #<?= e($order['id']) ?></strong>
                    </div>
                    <div>
                        <?php
                        $statusStyles = [
                            'pending'   => 'background: #fde68a; color: #7a4d00;',
                            'paid'      => 'background: #dbeafe; color: #0b4fb3;',
                            'shipped'   => 'background: #ddf4ff; color: #0969da;',
                            'delivered' => 'background: #dafbe1; color: #1a7f37;',
                            'cancelled' => 'background: #ffebe9; color: #cf222e;'
                        ];
                        $currentStyle = $statusStyles[$order['status']] ?? 'background: #eaeef2; color: #24292f;';
                        ?>
                        <span class="badge" style="<?= $currentStyle ?> font-weight: bold; text-transform: capitalize;">
                            <?= e($order['status']) ?>
                        </span>
                    </div>
                </div>

                <div style="padding: 0;">
                    <table class="table" style="margin-top: 0; border: none; padding: 0;">
                        <thead>
                            <tr>
                                <th style="border-top: none;">Item</th>
                                <th style="border-top: none; width: 100px; text-align: center;">Quantity</th>
                                <th style="border-top: none; width: 150px; text-align: right;">Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order['items'] as $item): ?>
                                <tr>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 12px;">
                                            <?php if (!empty($item['product_image'])): ?>
                                                <img src="/Lesson5/<?= e($item['product_image']) ?>" 
                                                     alt="<?= e($item['product_name']) ?>" 
                                                     class="history-img"
                                                     style="border-radius: 6px; border: 1px solid #d0d7de;">
                                            <?php else: ?>
                                                <div class="small-img" style="border-radius: 6px; border: 1px solid #d0d7de; display: flex; align-items: center; justify-content: center; color: #57606a; font-size: 12px;">No Image</div>
                                            <?php endif; ?>
                                            <div>
                                                <strong><?= e($item['product_name']) ?></strong>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="text-align: center; vertical-align: middle;">
                                        x<?= e($item['quantity']) ?>
                                    </td>
                                    <td style="text-align: right; vertical-align: middle; font-weight: bold;">
                                        <?= vnd($item['price_at_time']) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div style="background: #fff; padding: 12px 16px; border-top: 1px solid #d0d7de; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                    <div style="font-size: 14px; color: #57606a;">
                        <strong>Shipping Address:</strong> <?= e($order['shipping_address']) ?>
                    </div>
                    <div style="display: flex; align-items: center; gap: 16px;">
                        <div>
                            <span>Total Amount: </span>
                            <strong style="color: var(--danger); font-size: 18px;"><?= vnd($order['total_amount']) ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../../includes/frontend_footer.php'; ?>