<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$role = strtolower((string)($_SESSION['role'] ?? ''));

if ($role === 'therapist') {
    header('Location: /Mental_Health/controllers/therapist_run.php?action=notes');
    exit;
}

header('Location: /Mental_Health/controllers/auth_run.php?action=login');
exit;
