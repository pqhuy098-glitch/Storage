<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

include_once __DIR__ . '/../../includes/frontend_header.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/helper.php';

$id = (int)($_GET['id'] ?? 0);
$stm = $con->prepare(
    "SELECT p.*, c.name AS category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    WHERE p.id = ?"
);
$stm->bind_param("i",$id);
$stm->execute();
$p = $stm->get_result()->fetch_assoc();

?>
<h1>Product Details</h1>

<?php if (empty($p)) { ?>
    <p>Product with id <?= e($id) ?> does not exist</p>
<?php } else { ?>
    <a class="btn" href="index.php">Back to product list</a>
    <div class="details">
        <div>
            <img src="/Lesson5/<?= e($p['image_url'] ?: 'assets/img/placeholder.svg') ?>" alt="<?= e($p['name']) ?>">
        </div>

        <div>
            <h3><?= e($p['name']) ?></h3>
            <p class="badge"><?= e($p['category_name'] ?? 'Chưa phân loại') ?></p>
            <p><?= e($p['description']) ?></p>
            <p><strong><?= vnd($p['price']) ?></strong></p>
            <?php if ((int)($p['stock_quantity'] ?? 0) <= 0): ?>
                <p class="alert alert-danger">Out of stock</p>
            <?php endif; ?>
        </div>
    </div>
<?php } ?>

<?php include __DIR__ . '/../../includes/frontend_footer.php'; ?>