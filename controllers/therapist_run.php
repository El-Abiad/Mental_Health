<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../core/BaseController.php';
require_once __DIR__ . '/TherapistController.php';

if (
    empty($_SESSION['user_id']) ||
    ($_SESSION['role'] ?? '') !== 'therapist'
) {
    header('Location: ../views/auth/login.php');
    exit;
}

$controller = new TherapistController();

$action = $_GET['action'] ?? 'dashboard';

$allowed = [
    'dashboard',
    'availability',
    'viewSession',
    'startSession',
    'endSession',
    'notes',
    'saveNote',
    'profile',
    'cancelAppointment',
    'patients',
    'patientMoodReport'
];

// Map arguments where necessary
$therapistId = $_SESSION['user_id'];

if (in_array($action, $allowed, true)) {
    // Basic routing logic, some methods take a second parameter like sessionId or patientId
    // which should come from $_GET or $_POST. 
    $sessionId = isset($_GET['session_id']) ? (int)$_GET['session_id'] : (isset($_POST['session_id']) ? (int)$_POST['session_id'] : 0);
    $patientId = isset($_GET['patient_id']) ? (int)$_GET['patient_id'] : (isset($_POST['patient_id']) ? (int)$_POST['patient_id'] : 0);
    $appointmentId = isset($_GET['appointment_id']) ? (int)$_GET['appointment_id'] : (isset($_POST['appointment_id']) ? (int)$_POST['appointment_id'] : 0);

    if ($action === 'viewSession' || $action === 'startSession' || $action === 'endSession' || $action === 'saveNote') {
        $controller->$action($therapistId, $sessionId);
    } elseif ($action === 'patientMoodReport') {
        $controller->$action($therapistId, $patientId);
    } elseif ($action === 'cancelAppointment') {
        $controller->$action($therapistId, $appointmentId);
    } else {
        $controller->$action($therapistId);
    }
} else {
    $controller->dashboard($therapistId);
}