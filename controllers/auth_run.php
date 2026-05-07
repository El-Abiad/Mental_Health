<?php

session_start();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/encryption.php';
require_once __DIR__ . '/../core/BaseController.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../controllers/AuthController.php';

$action = $_GET['action'] ?? 'login';

$controller = new AuthController();

switch ($action) {
    case 'loginPost':    $controller->loginPost();    break;
    case 'registerPost': $controller->registerPost(); break;
    case 'logout':       $controller->logout();       break;
    default:             $controller->login();        break;
}