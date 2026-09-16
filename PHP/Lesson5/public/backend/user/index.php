<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

include_once __DIR__ . '/../../../includes/admin_header.php';
include_once __DIR__ . '/../../../config/db.php';
include_once __DIR__ . '/../../../config/helper.php';

$sql = "SELECT id, username, password, email, full_name, role FROM users";
$result = $con->query($sql);

?>
<h1>User List</h1>
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Password</th>
            <th>Email</th>
            <th>Full name</th>
            <th>Role</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        while($row = $result->fetch_assoc()):
        ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['username'] ?></td>

            <td style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                <?= $row['password'] ?>
            </td>

            <td><?= $row['email'] ?></td>
            <td><?= $row['full_name'] ?></td>
            <td><?= $row['role'] ?></td>
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