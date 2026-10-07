<?php
// frontend/index.php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/helper.php';
$keyword = trim($_GET['keyword'] ?? '');
$categoryId = (int)($_GET['category_id'] ?? 0);
$min_price = (int)($_GET['min_price'] ?? '');
$max_price = (int)($_GET['max_price'] ?? '');
 
$sort = $_GET['sort'] ?? '';
$params = [];
$types = '';
$errors = '';

$sql = 'SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE 1=1';

if ($keyword !== '') {
  $sql .= ' AND p.name LIKE ?';
  $params[] = '%' . $keyword . '%';
  $types .= 's';
}

if ($categoryId > 0) {
  $sql .= ' AND p.category_id = ?';
  $params[] = $categoryId;
  $types .= 'i';
}

if ($min_price != 0 && $max_price != 0) {
  if ($min_price > $max_price) {
    $errors = "Please input valid price range";
  }
}
if (empty($errors)) {
  if ($min_price != 0 && is_numeric($min_price)) {
    $sql .= ' AND p.price >= ?';
    $params[] = (int)$min_price;
    $types .= 'i';
  }
  if ($max_price != 0 && is_numeric($max_price)) {
    $sql .= ' AND p.price <= ?';
    $params[] = (int)$max_price;
    $types .= 'i';
  }
}

$sql .= $sort === 'price_desc' ? ' ORDER BY p.price DESC' : ($sort === 'price_asc' ? ' ORDER BY p.price ASC' : ' ORDER BY p.id DESC');
$stmt = $con->prepare($sql);

if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$categories = $con->query('SELECT id, name FROM categories ORDER BY name')->fetch_all(MYSQLI_ASSOC);

include __DIR__ . '/../../includes/frontend_header.php';
?>

<h1>Product List</h1>
<form method="get" class="function">
  <div>
    <div class="form-row">
      <label>Keyword</label>
      <input name="keyword" value="<?= e($keyword) ?>">
    </div>

    <div class="form-row">
      <label>Category</label>
      <select name="category_id">
        <option value="0">All</option>
        <?php foreach ($categories as $c): ?>
          <option value="<?= e($c['id']) ?>" <?= $categoryId === $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
          <?php endforeach; ?>
      </select>
    </div>

    <div class="form-row">
      <label>Sort</label>
      <select name="sort">
        <option value="">Newest</option>
        <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Price ascending</option>
        <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Price descending</option>
      </select>
    </div>

    <button class="btn btn-primary">Filter</button>
    <a href="index.php" class="btn">Reset Filters</a>
  </div>
  
  <div>
    <p><strong>Price range</strong></p>
    <div class="grid-row-container">  
      <div class="grid-row">
        <div>
          <label>Min price</label>
          <input type="number" name="min_price" min="0" step="1000" value="<?= e($min_price) ?>">
        </div>

        <div>
          <label>Max price</label>
          <input type="number" name="max_price" min="0" step="1000"  value="<?= e($max_price) ?>">
        </div>
      </div>

      <p class ="text-alert-danger"><?= e($errors)?></p>
    </div>
  </div>
</form>

<div class="grid">
  <?php foreach ($products as $p): ?>
    <div class="card">
      <img src="/Lesson5/<?= e($p['image_url'] ?: 'assets/img/placeholder.svg') ?>" alt="<?= e($p['name']) ?>">
      <h3><?= e($p['name']) ?></h3>
      <p class="badge"><?= e($p['category_name']) ?></p>
      <p><?= e($p['description']) ?></p>
      <p><strong><?= vnd($p['price']) ?></strong></p>
      <?php if ((int)$p['stock_quantity'] <= 0): ?>
        <p class="alert alert-danger">Out of stock</p>
        <?php endif; ?>
        <a class="btn" href="detail.php?id=<?= e($p['id']) ?>">Detail</a>
      <form method="post" action="add_to_cart.php" style="display:inline">
        <input type="hidden" name="id" value="<?= e($p['id']) ?>">
        <button class="btn btn-success">Add to Cart</button>
      </form>
    </div>
  <?php endforeach; ?>
</div>
<?php include __DIR__ . '/../../includes/frontend_footer.php'; ?>