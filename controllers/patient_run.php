<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../core/BaseController.php';
require_once __DIR__ . '/PatientController.php';

if (
    empty($_SESSION['user_id']) ||
    ($_SESSION['role'] ?? '') !== 'patient'
) {
    header('Location: ../views/auth/login.php');
    exit;
}

$controller = new PatientController();

$action = $_GET['action'] ?? 'dashboard';

$allowed = [
    'dashboard',
    'intakeForm',
    'agreements',
    'sessions',
    'favorites',
    'logMood'
];

if (in_array($action, $allowed, true)) {
    // For now we just pass user_id to all methods as they expect it
    $controller->$action($_SESSION['user_id']);
} else {
    $controller->dashboard($_SESSION['user_id']);
}