<?php
include_once __DIR__ . '/../../includes/admin_header.php';
include_once __DIR__ . '/../../config/db.php';

$productCount = $con->query("SELECT COUNT(*) as c FROM products") -> fetch_assoc()['c'];

$userCount = $con->query("SELECT COUNT(*) as c FROM users") -> fetch_assoc()['c'];
$orderCount = $con->query("SELECT COUNT(*) as c FROM orders") -> fetch_assoc()['c'];
?>
<h1>Dashboard</h1>
<div class="stats">
    <div class="stat">
        <strong><?= $productCount ?></strong>
        Products
    </div>
    <div class="stat">
        <strong><?= $userCount ?></strong>
        Users
    </div>
    <div class="stat">
        <strong><?= $orderCount ?></strong>
        Orders
    </div>
</div>
<?php
include_once '../../includes/admin_footer.php';
?>
        