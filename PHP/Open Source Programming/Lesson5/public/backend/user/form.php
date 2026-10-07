<?php $isUpdate = isset($user); ?>
<form method="post" enctype="multipart/form-data">
  <input type="hidden" name="id" value="<?= $isUpdate ? $user ['id'] : '' ?>"/>
  
  <div class="form-row">
    <label for="username">Username</label>
    <input name="username" id="username" value="<?= $_POST['username'] ?? ($isUpdate ? $user['username'] : '') ?>"/>
  </div>

  <div class="form-row">
    <label for="email">Email</label>
    <input name="email" id="email"value="<?= $_POST['email'] ?? ($isUpdate ? $user['email'] : '') ?>"/>
  </div>

  <div class="form-row">
    <label for="password">Password</label>
    <input name="password" id="password" type="password" value="<?= $_POST['password'] ?? '' ?>" placeholder="<?= $isUpdate ? '' : '' ?>"/>
  </div>

  <div class="form-row">
    <label for="full_name">Full name</label>
    <input name="full_name" id="full_name" value="<?= $_POST['full_name'] ?? ($isUpdate ? $user['full_name'] : '') ?>"/>
  </div>

  <div class="form-row">
    <label for="role">Select a role</label>
      <?php $selectedRole = $_POST['role'] ?? ($isUpdate ? $user['role'] : ''); ?>
        <select name="role" id="role">
            <option value="">Select a role</option>
            <option value="admin" <?= $selectedRole === 'admin' ? 'selected' : '' ?>>Admin</option>
            <option value="user" <?= $selectedRole === 'user' ? 'selected' : '' ?>>User</option>
        </select>
  </div>

  <a href="index.php" class="btn">Back to user list</a>
  <button type="submit" class="btn btn-primary">Save</button>
</form>