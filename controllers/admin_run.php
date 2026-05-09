<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/encryption.php';
require_once __DIR__ . '/AdminController.php';

if (empty($_SESSION['user_id']) || strtolower((string)($_SESSION['role'] ?? '')) !== 'admin') {
    header('Location: /clinic/controllers/auth_run.php?action=login');
    exit;
}

$action = $_GET['action'] ?? 'dashboard';

switch ($action) {
    case 'users':
        require __DIR__ . '/../views/admin/users.php';
        break;
    case 'updateUser':
        require __DIR__ . '/../views/admin/UpdateUser.php';
        break;
    case 'violations':
        require __DIR__ . '/../views/admin/violations.php';
        break;
    case 'verifyIntakeForms':
        require __DIR__ . '/../views/admin/VerifyIntakeForms.php';
        break;
    case 'dashboard':
    default:
        require __DIR__ . '/../views/admin/dashboard.php';
        break;
}
