<?php

require_once __DIR__ . '/../core/BaseController.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../config/encryption.php';

class AuthController extends BaseController {

    public function login(): void {
        if ($this->isLoggedIn()) {
            if (empty($_SESSION['role'])) {
                session_destroy();
            } else {
                $this->redirectByRole($_SESSION['role']);
            }
        }
        $this->view('auth/login');
    }

    public function loginPost(): void {
        $email    = trim($_POST['email']    ?? '');
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

        $role = User::getRole($user['Id']);
        if (!$role) {
            $this->view('auth/login', ['error' => 'Your account is corrupted (no role assigned). Please contact support or register again.']);
            return;
        }

        $_SESSION['user_id'] = $user['Id'];
        $_SESSION['role']    = $role;
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

        if (User::findByEmail($email)) {
            $this->view('auth/register', ['error' => 'Email already exists.']);
            return;
        }

        if (User::findByUsername($username)) {
            $this->view('auth/register', ['error' => 'Username already exists.']);
            return;
        }

        $hashed = Encryption::hashPassword($password);
        User::create($username, $email, $hashed, $fullname, '3', $phone);

        $this->view('auth/login', ['success' => 'Account created. Please log in.']);
    }

    public function logout(): void {
        session_destroy();
        $this->redirect('/clinic/controllers/auth_run.php?action=login');
    }

    // FIX: redirect to run files (controllers) not views directly
    private function redirectByRole(string $role): void {
        switch ($role) {
            case 'admin':     $this->redirect('/clinic/controllers/admin_run.php?action=dashboard');     break;
            case 'manager':   $this->redirect('/clinic/controllers/manager_run.php?action=dashboard');   break;
            case 'therapist': $this->redirect('/clinic/controllers/therapist_run.php?action=dashboard'); break;
            case 'patient':   $this->redirect('/clinic/controllers/patient_run.php?action=dashboard');   break;
            default:          $this->redirect('/clinic/controllers/auth_run.php?action=login');           break;
        }
    }
}
