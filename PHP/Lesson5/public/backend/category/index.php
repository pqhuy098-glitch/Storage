<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

include_once __DIR__ . '/../../../includes/admin_header.php';
include_once __DIR__ . '/../../../config/db.php';
include_once __DIR__ . '/../../../config/helper.php';

$sql = "SELECT * FROM categories";
$result = $con->query($sql);

?>
<h1>Category List</h1>
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Description</th>
            <th>Created at</th>
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
            <td><?= $row['description'] ?></td>
            <td><?= $row['created_at'] ?></td>

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