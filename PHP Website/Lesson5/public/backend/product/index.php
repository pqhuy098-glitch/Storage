<?php
include_once __DIR__ . '/../../../includes/admin_header.php';
include_once __DIR__ . '/../../../config/db.php';
include_once __DIR__ . '/../../../config/helper.php';

if (isset($_GET['q'])){
    $keyword = e($_GET['q']);
}

$sql = "SELECT p.id, p.name, p.sku, p.description, p.price, p.stock_quantity, c.name category 
FROM products p JOIN categories c ON p.category_id = c.id";

if (isset($_GET['q'])){
    $sql .= " WHERE p.name like '%$keyword%'";
}

$result = $con->query($sql);

?>
<h1>Product List</h1>
<form>
    <input name="q" value = "<?= isset($keyword)? $keyword: '' ?>" placeholder ="Search" />
    <button type="submit" class="btn btn-primary">Search</button>
</form>
<table class="table">
    <thead>
        <tr>
            <th>Id</th>
            <th>Name</th>
            <th>SKU</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Category</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        while($row = $result->fetch_assoc()):
        ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['name'] ?></td>
            <td><?= $row['sku'] ?></td>
            <td><?= vnd($row['price']) ?></td>
            <td><?= $row['stock_quantity'] ?></td>
            <td><?= $row['category'] ?></td>
            <td>
                <a href="update.php?id= <?= $row['id'] ?>" class="btn">Update</a>
                <a href="delete.php?id= <?= $row['id'] ?>" class="btn btn-danger" onclick = "return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
        <?php endwhile ?>
    </tbody>
</table>
<?php
include_once __DIR__ . '/../../../includes/admin_footer.php';
?>