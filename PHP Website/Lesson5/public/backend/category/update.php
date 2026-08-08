<?php
include __DIR__ . '/../../../includes/admin_header.php';
include __DIR__ . '/../../../config/db.php';
include_once __DIR__ . '/../../../config/helper.php';

$id = (int)($_GET['id'] ?? 0);
$stm = $con->prepare("SELECT * FROM categories WHERE id=?");
$stm->bind_param("i",$id);
$stm->execute();
$cate = $stm->get_result()->fetch_assoc();

$errors = [];

if($_SERVER['REQUEST_METHOD'] === "POST") {
    $id = (int)($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $created_at = trim($_POST['created_at'] ?? '');

    if ($name === ''){
        $errors['name'] = "Name is required";
    }

    $queryFindName = $con -> prepare("SELECT name FROM categories WHERE name = ? AND id != ?");
    $queryFindName -> bind_param("si", $name, $id);
    $queryFindName -> execute();
    $result = $queryFindName -> get_result() -> fetch_assoc();

    if ($result) {
        $errors['name'] = "Name already exists";
    }

    if (empty($errors)) {
        $stm = $con -> prepare("UPDATE categories SET name=?, description=? WHERE id=?");
        $stm -> bind_param("ssi", $name, $description, $id);
        $stm -> execute();
        redirect("index.php");
    }
}
?>
<h1>Update Category</h1>
<?php
if (!empty($errors)):
?>
<div class ="alert alert-danger"><?= e(array_values($errors)[0])?></div>
<?php endif ?>
<?php
include __DIR__ . '/form.php'
?>
<?php
include __DIR__ . '/../../../includes/admin_footer.php';
?>