<?php
require_once __DIR__ . '/../config/helper.php';
$path = parse_url($_SERVER["REQUEST_URI"] ?? '', PHP_URL_PATH) ?? '';
$isDashboard = str_contains($path, '/backend/index.php');
$isUser = str_contains($path, '/backend/user/index.php');
$isCategory = str_contains($path, 'backend/index.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="stylesheet" href="/Lesson5/assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <strong>DemoShop</strong>
        <span class="nav-badge nav-badge-admin">Admin Area</span>
        <a href="/Lesson5/public/frontend/index.php"> Front Area</a>
        <a href="/Lesson5/public/auth/logout.php">Logout</a>
    </nav>
    <div class="admin-layout">
        <aside class="sidebar">
            <div class="sidebar-profile">
                <strong>Admin Panel</strong>
                <small>admin</small>
            </div>
            <a href="/Lesson5/public/backend/" class="sidebar-link" <?= $isDashboard ? 'active': ''  ?>>Dashboard</a>

            <p class="sidebar-title">Management</p>
            <a href="/Lesson5/public/backend/product" class="sidebar-link" <?= $isDashboard ? 'active': ''  ?>>Product</a>
            <a href="/Lesson5/public/backend/user" class="sidebar-link" <?= $isUser ? 'active': ''  ?>>User</a>
            <a href="/Lesson5/public/backend/category" class="sidebar-link" <?= $isUser ? 'active': ''  ?>>Category</a>

            <p class="sidebar-title">Quick Access</p>
            <a href="/Lesson5/public/backend/product/create.php" class="sidebar-link">Add Product</a>
            <a href="/Lesson5/public/backend/user/create.php" class="sidebar-link">Add User</a>
            <a href="/Lesson5/public/backend/category/create.php" class="sidebar-link">Add Category</a>
            <a href="/Lesson5/public/frontend" class="sidebar-link">Front Area</a>
        </aside>
        <main class="container">
            <?php if($msg = flash('success')): ?>
                <div class="alert alert-success"> <?= $msg ?></div>
            <?php endif ?>
            <?php if($msg = flash('error')): ?>
                <div class="alert alert-danger"> <?= $msg ?></div>
            <?php endif ?>