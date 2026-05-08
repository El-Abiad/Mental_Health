<?php
require_once __DIR__ . '/../core/BaseController.php';
require_once __DIR__ . '/../models/Patient.php';

class PatientController extends BaseController {
    private Patient $patientModel;

    public function __construct() {
        parent::__construct();
        $this->patientModel = new Patient();
    }

    public function dashboard(int $userId): void {
        $patient = $this->patientModel->getPatientByUserId($userId);
        if (!$patient) {
            echo "Patient profile not found";
            return;
        }
        $patientId = $patient['id'];
        $upcomingSessions = $this->patientModel->getUpcomingSessions($patientId);
        $moodLogs = $this->patientModel->getMoodLogs($patientId);

        require __DIR__ . '/../views/patient/dashboard.php';
    }

    public function intakeForm(int $userId): void {
        $patient = $this->patientModel->getPatientByUserId($userId);
        if (!$patient) {
            echo "Patient profile not found";
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processIntakeForm($patient['id']);
            return;
        }

        require __DIR__ . '/../views/patient/intake_form.php';
    }

    private function processIntakeForm(int $patientId): void {
        // Process form data
        $this->patientModel->completeIntakeForm($patientId);
        $this->redirect('/patient/dashboard');
    }

    public function agreements(int $userId): void {
        $patient = $this->patientModel->getPatientByUserId($userId);
        if (!$patient) {
            echo "Patient profile not found";
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->signAgreement($patient['id']);
            return;
        }

        require __DIR__ . '/../views/patient/agreements.php';
    }

    private function signAgreement(int $patientId): void {
        $this->patientModel->signAgreement($patientId);
        $this->redirect('/patient/dashboard');
    }

    public function sessions(int $userId): void {
        $patient = $this->patientModel->getPatientByUserId($userId);
        if (!$patient) {
            echo "Patient profile not found";
            return;
        }

        $sessions = $this->patientModel->getUpcomingSessions($patient['id']);
        require __DIR__ . '/../views/patient/sessions.php';
    }

    public function favorites(int $userId): void {
        $patient = $this->patientModel->getPatientByUserId($userId);
        if (!$patient) {
            echo "Patient profile not found";
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->addFavorite($patient['id']);
            return;
        }

        $favorites = $this->patientModel->getFavorites($patient['id']);
        require __DIR__ . '/../views/patient/favorites.php';
    }

    private function addFavorite(int $patientId): void {
        $therapistId = (int) $_POST['therapist_id'];
        $this->patientModel->addFavorite($patientId, $therapistId);
        $this->redirect('/patient/favorites');
    }

    public function logMood(int $userId): void {
        $patient = $this->patientModel->getPatientByUserId($userId);
        if (!$patient) {
            echo "Patient profile not found";
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $mood = $_POST['mood'] ?? '';
            $notes = $_POST['notes'] ?? '';
            $this->patientModel->addMoodLog($patient['id'], $mood, $notes);
        }

        $this->redirect('/patient/dashboard');
    }

    protected function redirect($url) {
        header("Location: $url");
        exit;
    }
}