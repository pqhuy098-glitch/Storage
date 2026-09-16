<?php $isUpdate = isset($prod); ?>
<form method="post" enctype="multipart/form-data">
  <input type="hidden" name="id" value="<?= $isUpdate ? $prod ['id'] : '' ?>"/>
  <div class="form-row">
    <label for="name">Name</label>
    <input name="name" id="name" required  value="<?= $isUpdate ? $prod['name'] : '' ?>"/>
  </div>
  <div class="form-row">
    <label for="sku">SKU</label>
    <input name="sku" id="sku" required  value="<?= $isUpdate ? $prod['sku'] : '' ?>"/>
  </div>
  <div class="form-row">
    <label for="desc">Description</label>
    <textarea name="description" id="desc"><?= $isUpdate ? $prod['description'] : '' ?></textarea>
  </div>
  <div class="form-row">
    <label for="price">Price</label>
    <input type="number" name="price" id="price" min="0" step="1000" value="<?= $isUpdate ? $prod['price'] : '' ?>"/>
  </div>
  <div class="form-row">
    <label for="stock">Stock</label>
    <input type="number" name="stock_quantity" id="stock" min="0" value="<?= $isUpdate ? $prod['stock_quantity'] : '' ?>"/>
  </div>
  <div class="form-row">
    <label for="image">Image</label>
    <input type="file" name="image_url" id="image" accept=".jpg,.png,.gif,.webp,.svg,image/*"/>
    <?php if ($isUpdate && !empty($prod['image_url'])): ?>
      <img src="<?= $prod['image_url'] ?>" alt="">
    <?php endif ?>
  </div>
  <div class="form-row">
    <label for="category">Category</label>
    <select name="category_id" id="category">
      <?php foreach ($categories as $item): ?>
        <option value="<?= $item['id'] ?>"<?php if($isUpdate && $prod['category_id']==$item['id']): ?>selected<?php endif ?>><?= $item['name'] ?></option>
      <?php endforeach ?>
    </select>
  </div>
  <a href="index.php" class="btn">Back to product list</a>
  <button type="submit" class="btn btn-primary">Save</button>
</form>