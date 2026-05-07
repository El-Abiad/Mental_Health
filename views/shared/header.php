<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title ?? 'Mental Health Platform') ?></title>
    <link rel="stylesheet" href="/clinic/assets/style.css">
</head>
<body class="header">
    <nav>
        <div class="nav-brand">MindCare</div>
        <div class="nav-links">
            <?php if (isset($_SESSION['user_id'])): ?>
                <span>Welcome, <?= htmlspecialchars($_SESSION['name']) ?></span>
                <a href="/clinic/controllers/AuthController.php?action=logout">Logout</a>
            <?php endif; ?>
        </div>
    </nav>
    <main>