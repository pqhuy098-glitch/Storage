<?php
include __DIR__ . '/../../../includes/admin_header.php';
include __DIR__ . '/../../../config/db.php';
include_once __DIR__ . '/../../../config/helper.php';

$id = (int)($_GET['id'] ?? 0);
$stm = $con->prepare("SELECT * FROM products WHERE id=?");
$stm->bind_param("i",$id);
$stm->execute();
$prod = $stm->get_result()->fetch_assoc();

$categories = $con->query("SELECT id, name FROM categories")->fetch_all(MYSQLI_ASSOC);

$errors = [];

if($_SERVER['REQUEST_METHOD'] === "POST") {
    $id = (int)($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $sku = trim($_POST['sku'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $stock_quantity = (int)($_POST['stock_quantity'] ?? 0);
    $category_id = (int)($_POST['category_id'] ?? 0);
    $image = $prod['image_url'] ?? 'assets/img/placeholder.svg';

    if ($name === '') {
        $errors['name'] = "Name is required";
    }
    
    if ($sku === '') {
        $errors['sku'] = "SKU is required";
    }

    if($price < 0) {
        $errors[] = "Price must be greater than equal 0";
    }

    $uploadResult = handle_upload('image_url', $image);
    if(!$uploadResult['ok'])
    {
        $errors[]=$uploadResult['error'];
    }
    else {
        $image = $uploadResult['path'];
    }

    if(!$errors)
    {
    $stm = $con->prepare("UPDATE products SET name=?, sku=?, description=?, price=?, stock_quantity=?, image_url=?, category_id =? WHERE id=?");
    $stm->bind_param("sssdisii", $name, $sku, $description, $price, $stock_quantity, $image, $category_id, $id);
    $stm->execute();
    redirect("index.php");
    }
}
?>
<h1>Update Product</h1>
<?php
foreach($errors as $err):
?>
<div class ="alert alert-danger"><?= e($err)?></div>
<?php endforeach ?>
<?php
include __DIR__ . '/form.php'
?>
<?php
include __DIR__ . '/../../../includes/admin_footer.php';
?>