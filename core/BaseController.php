<?php

require_once __DIR__ . '/../config/db.php';

class BaseController {

    protected mysqli $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    protected function view(string $path, array $data = []): void {
        extract($data);
        require_once __DIR__ . '/../views/' . $path . '.php';
    }

    protected function redirect(string $url): void {
        header('Location: ' . $url);
        exit();
    }

    protected function isLoggedIn(): bool {
        return isset($_SESSION['user_id']);
    }

    protected function requireLogin(): void {
        if (!$this->isLoggedIn()) {
            $this->redirect('../auth/login.php');
        }
    }

    protected function requireRole(string $role): void {
        $this->requireLogin();
        if ($_SESSION['role'] !== $role) {
            $this->redirect('../auth/login.php');
        }
    }
}