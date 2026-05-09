<?php

require_once __DIR__ . '/../core/BaseController.php';
require_once __DIR__ . '/../models/Therapist.php';
require_once __DIR__ . '/../models/Note.php';

class TherapistController extends BaseController
{
    private Therapist $therapistModel;
    private Note $noteModel;

    public function __construct()
    {
        parent::__construct();
        $this->therapistModel = new Therapist();
        $this->noteModel = new Note();
    }

    public function manageCycle(int $sessionId, string $newStatus): string
    {
        $stmt = $this->db->prepare('UPDATE Session SET Status = ? WHERE SessionId = ?');
        $stmt->bind_param('si', $newStatus, $sessionId);
        $stmt->execute();
        return "Session {$sessionId} status updated to {$newStatus}";
    }

    public function dashboard(int $therapistId): void
    {
        $this->requireRole('therapist');
        $appointments = $this->therapistModel->getUpcomingAppointments($therapistId);
        $sessions = $this->therapistModel->getTodaySessions($therapistId);
        $profile = $this->therapistModel->getProfile($therapistId);
        $missedHighRisk = $this->therapistModel->getMissedHighRiskPatients($therapistId);
        $weeklyMoodReports = $this->therapistModel->getWeeklyMoodReports($therapistId);
        require __DIR__ . '/../views/therapist/dashboard.php';
    }

    public function availability(int $therapistId): void
    {
        $this->requireRole('therapist');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $day = (int)($_POST['day'] ?? 0);
            $start = trim($_POST['start'] ?? '');
            $end = trim($_POST['end'] ?? '');
            if ($day >= 1 && $day <= 7 && $start !== '' && $end !== '') {
                $this->therapistModel->upsertAvailability($therapistId, $day, $start, $end);
            }

            $snoozed = isset($_POST['is_snoozed']) ? 1 : 0;
            $this->therapistModel->setSnooze($therapistId, $snoozed);
            if ($snoozed === 1) {
                $this->therapistModel->notifyPatientsTherapistSnoozed($therapistId);
            }

            $this->redirect('/clinic/controllers/therapist_run.php?action=availability&saved=1');
        }

        $availability = $this->therapistModel->getAvailability($therapistId);
        $profile = $this->therapistModel->getProfile($therapistId);
        require __DIR__ . '/../views/therapist/availability.php';
    }

    public function viewSession(int $therapistId, int $sessionId): void
    {
        $this->requireRole('therapist');
        $session = $this->therapistModel->getSession($sessionId, $therapistId);
        if (!$session) {
            $this->abort(403, 'Session not found or access denied.');
        }
        $notes = $this->noteModel->getBySession($sessionId);
        $patient = $this->therapistModel->getPatientBySession($sessionId);
        require __DIR__ . '/../views/therapist/session_view.php';
    }

    public function startSession(int $therapistId, int $sessionId): void
    {
        $this->requireRole('therapist');
        if (!$this->therapistModel->hasLiveSession($therapistId)) {
            $this->therapistModel->startSession($sessionId, $therapistId);
        }
        $this->redirect('/clinic/controllers/therapist_run.php?action=session&id=' . $sessionId);
    }

    public function endSession(int $therapistId, int $sessionId): void
    {
        $this->requireRole('therapist');
        $this->therapistModel->endSession($sessionId, $therapistId);
        $this->redirect('/Mental_Health/controllers/therapist_run.php?action=session&id=' . $sessionId);
    }

    public function notes(int $therapistId): void
    {
        $this->requireRole('therapist');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sessionId = (int)($_POST['session_id'] ?? 0);
            $content = trim($_POST['content'] ?? $_POST['session_note'] ?? '');
            if ($sessionId > 0 && $content !== '') {
                $this->checkCrisisKeywords($content, $sessionId, $therapistId);
                $this->noteModel->create(0, $therapistId, $content, $sessionId);
            }
            $this->redirect('/Mental_Health/controllers/therapist_run.php?action=notes&saved=1');
        }

        $notes = $this->noteModel->getByTherapist($therapistId);
        require __DIR__ . '/../views/therapist/notes.php';
    }

    public function saveNote(int $therapistId, int $sessionId): void
    {
        $this->requireRole('therapist');
        $content = trim($_POST['content'] ?? '');
        $noteId = isset($_POST['note_id']) ? (int)$_POST['note_id'] : null;

        if ($content === '') {
            $this->redirect('/Mental_Health/controllers/therapist_run.php?action=session&id=' . $sessionId . '&error=empty_note');
        }

        $this->checkCrisisKeywords($content, $sessionId, $therapistId);

        if ($noteId) {
            $this->noteModel->update($noteId, $therapistId, $content);
        } else {
            $this->noteModel->create(0, $therapistId, $content, $sessionId);
        }

        $this->redirect('/Mental_Health/controllers/therapist_run.php?action=session&id=' . $sessionId . '&note_saved=1');
    }

    public function profile(int $therapistId): void
    {
        $this->requireRole('therapist');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'Specialization' => trim($_POST['specialization'] ?? ''),
                'LicenseStatus' => trim($_POST['license_status'] ?? 'pending'),
                'LicenseExpiry' => trim($_POST['license_expiry'] ?? ''),
                'IsSnoozed' => isset($_POST['is_snoozed']) ? 1 : 0,
            ];
            $this->therapistModel->updateProfile($therapistId, $data);
            $this->redirect('/Mental_Health/controllers/therapist_run.php?action=profile&saved=1');
        }

        $profile = $this->therapistModel->getProfile($therapistId);
        require __DIR__ . '/../views/therapist/profile.php';
    }

    public function patients(int $therapistId): void
    {
        $this->requireRole('therapist');
        $patients = $this->therapistModel->getPatients($therapistId);
        $sharedJournals = $this->therapistModel->getSharedJournals($therapistId);
        require __DIR__ . '/../views/therapist/patients.php';
    }

    private function checkCrisisKeywords(string $content, int $sessionId, int $therapistId): void
    {
        foreach (['suicide', 'kill myself', 'end my life', 'self harm', 'overdose'] as $keyword) {
            if (stripos($content, $keyword) !== false) {
                $this->therapistModel->triggerCrisisAlert($sessionId, $therapistId, $keyword);
                return;
            }
        }
    }

    protected function abort(int $code, string $message = ''): void
    {
        http_response_code($code);
        echo htmlspecialchars($message);
        exit;
    }
}
