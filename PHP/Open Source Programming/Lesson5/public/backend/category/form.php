<?php $isUpdate = isset($cate); ?>
<form method="post" enctype="multipart/form-data">
  <input type="hidden" name="id" value="<?= $isUpdate ? $cate['id'] : '' ?>"/>
  <div class="form-row">
    <label for="name">Name</label>
    <input name="name" id="name" value="<?= $_POST['name'] ?? ($isUpdate ? $cate['name'] : '') ?>"/>
  </div>
  <div class="form-row">
    <label for="desc">Description</label>
    <textarea name="description" id="desc"><?= $_POST['description'] ?? ($isUpdate ? $cate['description'] : '') ?></textarea>
  </div>
  <?php if ($isUpdate): ?>
    <div class="form-row">
      <label for="created_at">Created at</label>
      <input name="created_at" id="created_at" value="<?= $isUpdate ? $cate['created_at'] : '' ?>" readonly class="label-readonly" />
    </div>
  <?php endif; ?>

  <a href="index.php" class="btn">Back to category list</a>
  <button type="submit" class="btn btn-primary">Save</button>
</form>