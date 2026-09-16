<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/helper.php';


// unset($_SESSION['cart']);
$cart = $_SESSION['cart'] ?? [];
$total = 0;
$csrfToken = csrf_token();

include __DIR__ .'/../../includes/frontend_header.php';

?>

<h1>View Cart</h1>

<?php if(!$cart): ?> 
    <div class="alert alert-danger">Cart is empty</div>
<?php else: ?>
    <table class="table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Subtotal</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cart as $item): 
                $subtotal = $item['price'] * $item['quantity'];
                $total += $subtotal;
            ?>
                <tr data-id="<?= $item['id'] ?>">
                    <td><?= e($item['name']) ?></td>
                    <td>
                        <input name="quantity" type="number" value="<?= $item['quantity'] ?>" min="1" style="max-width: 80px;" class="quantity">
                        <button class="btn btn-primary update">Update</button>
                    </td>
                    <td><?= vnd($item['price']) ?></td>
                    <td><?= $subtotal ?></td>
                    <td>
                        <button class="btn btn-danger remove">Remove</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <h3>Total: <?= vnd($total); ?></h3>
<?php endif; ?>

<a href="checkout.php" class="btn btn-success">Checkout</a>
<a href="index.php" class="btn">Continue Shopping</a>

<script>
    const csrfToken = '<?= $csrfToken ?>'

    document.querySelectorAll('.update').forEach(button => button.addEventListener('click', async e => {
        const row = e.target.closest('tr');

        const data = new FormData();
        data.append('id', row.dataset.id);
        data.append('quantity', row.querySelector('.quantity').value);
        data.append('csrf_token', csrfToken);

        const res = await fetch('api/update_cart_item.php', {
            method: 'POST',
            body: data,
        });

        const json = await res.json();
        if (json.success) location.reload();
        else alert(json.message);
    }));

    document.querySelectorAll('.remove').forEach(button => button.addEventListener('click', async e => {
        const row = e.target.closest('tr');

        const data = new FormData();
        data.append('id', row.dataset.id);
        data.append('quantity', row.querySelector('.quantity').value);
        data.append('csrf_token', csrfToken);

        const res = await fetch('api/remove_cart_item.php', {
            method: 'POST',
            body: data,
        });
        const json = await res.json();
        if (json.success) location.reload();
        else alert(json.message);
    }));    
</script>

<?php include __DIR__ .'/../../includes/frontend_footer.php'; ?>