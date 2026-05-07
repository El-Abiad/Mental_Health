<?php

require_once __DIR__ . '/../core/BaseController.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../config/encryption.php';

class AuthController extends BaseController {

    public function login(): void {
        if ($this->isLoggedIn()) {
            $this->redirectByRole($_SESSION['role']);
        }
        $this->view('auth/login');
    }

    public function loginPost(): void {
        $email    = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($email) || empty($password)) {
            $this->view('auth/login', ['error' => 'All fields are required.']);
            return;
        }

        $user = User::findByEmail($email);

        if (!$user || !Encryption::verifyPassword($password, $user['Password'])) {
            $this->view('auth/login', ['error' => 'Invalid email or password.']);
            return;
        }

        if (!$user['IsActive']) {
            $this->view('auth/login', ['error' => 'Your account is disabled.']);
            return;
        }

        $_SESSION['user_id'] = $user['Id'];
        $_SESSION['role']    = User::getRole($user['Id']);
        $_SESSION['name']    = $user['FullName'];

        $this->redirectByRole($_SESSION['role']);
    }

    public function register(): void {
        if ($this->isLoggedIn()) {
            $this->redirectByRole($_SESSION['role']);
        }
        $this->view('auth/register');
    }

    public function registerPost(): void {
        $username = trim($_POST['username'] ?? '');
        $email    = trim($_POST['email']    ?? '');
        $password = trim($_POST['password'] ?? '');
        $fullname = trim($_POST['fullname'] ?? '');
        $phone    = trim($_POST['phone']    ?? '');

        if (empty($username) || empty($email) || empty($password) || empty($fullname)) {
            $this->view('auth/register', ['error' => 'All fields are required.']);
            return;
        }

        if (User::findByEmail( $email)) {
            $this->view('auth/register', ['error' => 'Email already exists.']);
            return;
        }

        $hashed = Encryption::hashPassword($password);
        User::create($username, $email, $hashed, $fullname, $phone);

        $this->view('auth/login', ['success' => 'Account created. Please log in.']);
    }

    public function logout(): void {
        session_start();
        session_destroy();
        $this->redirect('/clinic/views/auth/login.php');
    }

    private function redirectByRole(string $role): void {
        switch ($role) {
            case 'admin':     $this->redirect('/clinic/views/admin/dashboard.php');     break;
            case 'manager':   $this->redirect('/clinic/views/manager/dashboard.php');   break;
            case 'therapist': $this->redirect('/clinic/views/therapist/dashboard.php'); break;
            case 'patient':   $this->redirect('/clinic/views/patient/dashboard.php');   break;
            default:          $this->redirect('/clinic/views/auth/login.php');          break;
        }
    }
}