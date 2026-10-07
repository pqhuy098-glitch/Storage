<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/helper.php';

require_login();

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    flash("error", "Cart is empty");
    redirect("index.php");
}

$address = trim($_POST['shipping_address'] ?? (current_user()['address'] ?? 'HCM'));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        flash("error", "CSRF token is invalid");
        redirect("index.php");
    }

    $pIds = array_map('intval', array_keys($cart));
    if (!$pIds) {
        flash("error", "Cart is empty");
        redirect("index.php");
    }

    try {
        $con -> begin_transaction();

        $placeholder = implode(',', array_fill(0, count($pIds), '?'));
        $sql = "SELECT id, name, price, stock_quantity FROM products WHERE id IN (" . $placeholder . ") FOR UPDATE;";

        $prodStm = $con -> prepare($sql);

        $types = str_repeat('i', count($pIds));
        $prodStm -> bind_param($types, ...$pIds);
        $prodStm -> execute();
        $prodsRow = $prodStm -> get_result()->fetch_all(MYSQLI_ASSOC);

        $prodsById = [];
        foreach($prodsRow as $row) {
            $prodsById[(int)$row['id']] = $row;
        }

        $validItems = [];
        $total = 0.0;

        foreach ($cart as $id => $item) {
            $id = (int)$id;
            $quantity = max(1, (int)($item['quantity'] ?? 1));
            if (!isset($prodsById[$id])) {
                throw new RuntimeException("A product in your cart is no longer available");
            }

            $product = $prodsById[$id];
            if ($product['stock_quantity'] < $quantity) {
                throw new RuntimeException("Not enough stock available for product: ". $product['name']);
            }

            $price = (float)$product['price'];
            $total += $price * $quantity;
            $validItems[] = [
                'id' => $id,
                'quantity' => $quantity,
                'price' => $price
            ];

        }

        $orderStm = $con -> prepare("INSERT INTO orders (user_id, total_amount, shipping_address) VALUES (?, ?, ?)");
        $uid = current_user()['id'];
        $orderStm -> bind_param("ids", $uid, $total, $address);
        if (!$orderStm -> execute()) {
            throw new RunTimeException("Cannot create order");
        }

        $orderId = $orderStm -> insert_id;
        $itemStm = $con -> prepare("INSERT INTO order_items (order_id, product_id, quantity, price_at_time) VALUES (?, ?, ?, ?)");
        $stockStm = $con -> prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id=?");

        foreach ($validItems as $item) {
            $itemStm -> bind_param("iiid", $orderId, $item['id'], $item['quantity'], $item['price']);
            if (!$itemStm -> execute()) {
                throw new RunTimeException("Cannot save order item");
            }

            $stockStm -> bind_param("ii", $item['quantity'], $item['id']);
            if (!$stockStm -> execute()) {
                throw new RunTimeException("Cannot update stock quantity");
            }
        }

        $con -> commit();
        unset($_SESSION['cart']);
        flash('success', 'Checkout completed order #'. $orderId);
        redirect("index.php");
    } catch (Throwable $e) {
        $con -> rollback();
        flash("error", "Error processing the order: " . $e->getMessage());
        redirect("index.php");
    }
}

include __DIR__ .'/../../includes/frontend_header.php';
?>

<h1>Checkout</h1>

<form method="POST">
    <?= csrf_input() ?>
    <div class="form-row">
        <label for="addr">Shipping Address</label>
        <input name="shipping_address" id="addr" value="<?= $address?>"  required>
    </div>
    <button class="btn btn-success">
        Checkout Now!
    </button>
</form>

<?php include __DIR__ . '/../../includes/frontend_footer.php'; ?>