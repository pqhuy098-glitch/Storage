<?php require_once __DIR__ . '/../config/helper.php'; ?>
<!doctype html>
<html>

<head>
  <meta charset="utf-8">
  <title>DemoShop</title>
  <link rel="stylesheet" href="/Lesson5/assets/css/style.css">
</head>

<body>
  <nav class="navbar">
    <strong>DemoShop Store</strong>
    <span class="nav-badge">User Area</span>
    <a href="/Lesson5/public/frontend/index.php">Home</a>
    <a href="/Lesson5/public/frontend/view_cart.php">Cart (<?= count($_SESSION['cart'] ?? []) ?>)</a>
    <a href="/Lesson5/public/frontend/order_history.php">History</a>
    <?php if (current_user()): ?>
      <span>Welcome <?= e(current_user()['username']) ?></span>
      <a href="/Lesson5/public/auth/logout.php">Logout</a>
    <?php else: ?>
      <a href="/Lesson5/public/auth/login.php">Login</a>
      <a href="/Lesson5/public/auth/register.php">Register</a>
    <?php endif; ?>
    <?php if (is_admin()): ?>
      <a href="/Lesson5/public/backend/index.php">Go to Admin</a>
    <?php endif; ?>
  </nav>
  <div class="container">
    <?php if ($msg = flash('success')): ?>
      <div class="alert alert-success"><?= e($msg) ?></div>
    <?php endif; ?>
    <?php if ($msg = flash('error')): ?>
      <div class="alert alert-danger">
        <?= e($msg) ?>
      </div>
    <?php endif; ?>