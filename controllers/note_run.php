<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$role = strtolower((string)($_SESSION['role'] ?? ''));

if ($role === 'therapist') {
    header('Location: /clinic/controllers/therapist_run.php?action=notes');
    exit;
}

header('Location: /clinic/controllers/auth_run.php?action=login');
exit;
