<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/helper.php';

header('Content-Type: application/json');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method Not Allowed',
    ]);
    exit;
}

if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
    http_response_code(419);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid CSRF Token',
    ]);
    exit;
}

$id = (int)($_POST['id'] ?? 0);
$quantity = max(1, (int)($_POST['quantity'] ?? 1));

if (!isset($_SESSION['cart'][$id])) {
    echo json_encode([
        'success' => false,
        'message' => 'Product not found in cart',
    ]);
    exit;
}

unset($_SESSION['cart'][$id]);

echo json_encode([
    'success' => true,
    'message' => 'Quantity removed successfully',
]);

?>