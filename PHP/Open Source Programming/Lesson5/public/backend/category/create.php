<?php
include __DIR__ . '/../../../includes/admin_header.php';
include __DIR__ . '/../../../config/db.php';
include_once __DIR__ . '/../../../config/helper.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === "POST"){
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $created = date('Y-m-d H:i:s');

    if ($name === ''){
        $errors['name'] = "Name is required";
    }

    $queryFindName = $con -> prepare("SELECT name FROM categories WHERE name = ?");
    $queryFindName -> bind_param("s", $name);
    $queryFindName -> execute();
    $result = $queryFindName -> get_result() -> fetch_assoc();

    if ($result) {
        $errors['name'] = "Name already exists";
    }

    if (empty($errors)) {
        $stm = $con -> prepare("INSERT INTO categories (name, description, created_at) VALUES (?, ?, ?)");
        $stm -> bind_param("sss", $name, $description, $created);
        $stm -> execute();
        redirect("index.php");
    }
}
?>
<h1>Create Category</h1>
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