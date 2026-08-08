<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/helper.php';

$id = (int)($_POST['id'] ?? 0);
$stm = $con -> prepare("SELECT id, name, price, image_url, stock_quantity FROM products WHERE id=?");
$stm -> bind_param("i", $id);
$stm -> execute();
$p = $stm -> get_result() -> fetch_assoc();

if (!$p) {
    flash("error", "Product not found");
    redirect("index.php");
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_SESSION['cart'][$id])) {
    $_SESSION['cart'][$id]['quantity']++;
}
else {
    $_SESSION['cart'][$id] = [
        'id' => $p['id'],
        'name' => $p['name'],
        'price' => $p['price'],
        'image_url' => $p['image_url'],
        'quantity' => 1
    ];
}

flash("success", "Product added to cart.");
redirect("index.php");
?>
