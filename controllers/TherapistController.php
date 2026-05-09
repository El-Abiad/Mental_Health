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
        $this->noteModel      = new Note();
    }

    // Clinic manager function to manage session cycle
    public function manageCycle(int $sessionId, string $newStatus): string {
        // Update session status
        $stmt = $this->db->prepare('UPDATE sessions SET status = ? WHERE id = ?');
        $stmt->execute([$newStatus, $sessionId]);
        return "Session $sessionId status updated to $newStatus";
    }

    public function dashboard(int $therapistId): void
    {
        $this->requireTherapist();

        $appointments = $this->therapistModel->getUpcomingAppointments($therapistId);
        $sessions     = $this->therapistModel->getTodaySessions($therapistId);
        $profile      = $this->therapistModel->getProfile($therapistId);

        $missedHighRisk = $this->therapistModel->getMissedHighRiskPatients($therapistId);

        $weeklyMoodReports = $this->therapistModel->getWeeklyMoodReports($therapistId);

        require __DIR__ . '/../views/therapist/dashboard.php';
    }


    public function availability(int $therapistId): void
    {
        $this->requireTherapist();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->updateAvailability($therapistId);
            return;
        }

        $availability = $this->therapistModel->getAvailability($therapistId);
        $profile      = $this->therapistModel->getProfile($therapistId); // IsSnoozed flag

        require __DIR__ . '/../views/therapist/availability.php';
    }

    private function updateAvailability(int $therapistId): void
    {
        $slots = $_POST['slots'] ?? [];

        foreach ($slots as $slot) {
            $day   = (int) ($slot['day']   ?? 0);
            $start = trim($slot['start'] ?? '');
            $end   = trim($slot['end']   ?? '');

            if ($day < 1 || $day > 7 || !$start || !$end) continue;

            $this->therapistModel->upsertAvailability($therapistId, $day, $start, $end);
        }

        if (isset($_POST['is_snoozed'])) {
            $snoozed = (int) $_POST['is_snoozed']; // 1 = snoozed, 0 = active
            $this->therapistModel->setSnooze($therapistId, $snoozed);

            if ($snoozed === 1) {
                $this->therapistModel->notifyPatientsTherapistSnoozed($therapistId);
            }
        }

        $this->redirect('/therapist/availability?saved=1');
    }

    public function viewSession(int $therapistId, int $sessionId): void
    {
        $this->requireTherapist();

        $session = $this->therapistModel->getSession($sessionId, $therapistId);

        if (!$session) {
            $this->abort(403, 'Session not found or access denied.');
        }

        $notes   = $this->noteModel->getBySession($sessionId);
        $patient = $this->therapistModel->getPatientBySession($sessionId);

        require __DIR__ . '/../views/therapist/session_view.php';
    }

    public function startSession(int $therapistId, int $sessionId): void
    {
        $this->requireTherapist();

        $alreadyLive = $this->therapistModel->hasLiveSession($therapistId);
        if ($alreadyLive) {
            $this->redirect("/therapist/session/{$sessionId}?error=already_live");
            return;
        }

        $this->therapistModel->startSession($sessionId, $therapistId);
        $this->redirect("/therapist/session/{$sessionId}");
    }

    public function endSession(int $therapistId, int $sessionId): void
    {
        $this->requireTherapist();
        $this->therapistModel->endSession($sessionId, $therapistId);
        $this->redirect("/therapist/session/{$sessionId}?ended=1");
    }


    public function notes(int $therapistId): void
    {
        $this->requireTherapist();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $patientId = (int) $_POST['patient_id'];
            $content = trim($_POST['session_note']);
            $this->noteModel->create($patientId, $therapistId, $content);
            $this->redirect('/therapist/notes?saved=1');
            return;
        }

        $notes = $this->noteModel->getByTherapist($therapistId);
        require __DIR__ . '/../views/therapist/notes.php';
    }

    public function saveNote(int $therapistId, int $sessionId): void
    {
        $this->requireTherapist();

        $content = trim($_POST['content'] ?? '');
        $noteId  = isset($_POST['note_id']) ? (int) $_POST['note_id'] : null;

        if ($content === '') {
            $this->redirect("/therapist/session/{$sessionId}?error=empty_note");
            return;
        }

        // REQ 13: scan note content for crisis keywords (e.g. "suicide")
        $this->checkCrisisKeywords($content, $sessionId, $therapistId);

        if ($noteId) {
            // REQ 24: update content + bump Version, CreatedAt stays untouched
            $this->noteModel->update($noteId, $therapistId, $content);
        } else {
            // new note — CreatedAt set by DB CURRENT_TIMESTAMP, never changed
            $this->noteModel->create($sessionId, $therapistId, $content);
        }

        $this->redirect("/therapist/session/{$sessionId}?note_saved=1");
    }

    /**
     * REQ 13: Trigger a warning when a high-risk keyword is found in notes.
     * REQ 14: System-level crisis alert is also available to patients via CrisisAlert table.
     */
    private function checkCrisisKeywords(string $content, int $sessionId, int $therapistId): void
    {
        $keywords = ['suicide', 'kill myself', 'end my life', 'self harm', 'overdose'];

        foreach ($keywords as $keyword) {
            if (stripos($content, $keyword) !== false) {
                // log a CrisisAlert for this patient
                $this->therapistModel->triggerCrisisAlert($sessionId, $therapistId, $keyword);
                break;
            }
        }
    }

    // ──────────────────────────────────────────────
    //  PROFILE
    //  REQ 26: IsSnoozed flag managed here too
    //  REQ 21: Personal info hidden from patients (handled in views/patient side)
    // ──────────────────────────────────────────────

    public function profile(int $therapistId): void
    {
        $this->requireTherapist();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->updateProfile($therapistId);
            return;
        }

        $profile = $this->therapistModel->getProfile($therapistId);
        require __DIR__ . '/../views/therapist/profile.php';
    }

    private function updateProfile(int $therapistId): void
    {
        $data = [
            'Specialization' => trim($_POST['specialization'] ?? ''),
            'LicenseStatus'  => trim($_POST['license_status'] ?? ''),
            'LicenseExpiry'  => trim($_POST['license_expiry']  ?? ''),
            'Status'         => trim($_POST['status']          ?? 'active'),
            'IsSnoozed'      => isset($_POST['is_snoozed']) ? 1 : 0,
        ];

        $this->therapistModel->updateProfile($therapistId, $data);
        $this->redirect('/therapist/profile?saved=1');
    }

    // ──────────────────────────────────────────────
    //  APPOINTMENTS
    //  REQ 11: Clinic manager checks cancelled sessions with user and therapist
    //  REQ 23: Fine applied if cancelled within 24h (handled in Payment model)
    // ──────────────────────────────────────────────

    /**
     * REQ 11: Therapist cancels appointment → reason logged → clinic manager notified
     * REQ 23: If within 24h → fine logic triggered in Payment model
     */
    public function cancelAppointment(int $therapistId, int $appointmentId): void
    {
        $this->requireTherapist();

        $reason = trim($_POST['reason'] ?? '');

        if ($reason === '') {
            $this->redirect('/therapist/dashboard?error=reason_required');
            return;
        }

        // REQ 11: save cancel reason + notify clinic manager
        $this->therapistModel->cancelAppointment($appointmentId, $therapistId, $reason);

        // REQ 23: check if within 24h → apply fine to patient
        $this->therapistModel->applyLateCancellationFine($appointmentId);

        $this->redirect('/therapist/dashboard?cancelled=1');
    }

    // ──────────────────────────────────────────────
    //  PATIENTS
    //  REQ 25: flag high-risk patients who miss sessions
    //  REQ 27: weekly mood report per patient
    // ──────────────────────────────────────────────

    public function patients(int $therapistId): void
    {
        $this->requireTherapist();

        $patients = $this->therapistModel->getPatients($therapistId);

        // REQ 34: therapist can see which journals are shared with them
        $sharedJournals = $this->therapistModel->getSharedJournals($therapistId);

        require __DIR__ . '/../views/therapist/patients.php';
    }

    /**
     * REQ 27: View weekly mood report for a specific patient.
     * DailyLog: PatientId, LogDate, MoodScore(1-10), SleepHours, Notes
     */
    public function patientMoodReport(int $therapistId, int $patientId): void
    {
        $this->requireTherapist();

        // confirm this patient belongs to this therapist
        $patient = $this->therapistModel->getPatientIfAssigned($therapistId, $patientId);
        if (!$patient) {
            $this->abort(403, 'Access denied.');
        }

        $moodLogs    = $this->therapistModel->getWeeklyMoodLogs($patientId);
        $sleepReport = $this->therapistModel->getSleepReport($patientId); // REQ 37

        require __DIR__ . '/../views/therapist/mood_report.php';
    }

    // ──────────────────────────────────────────────
    //  HELPERS
    // ──────────────────────────────────────────────

    private function requireTherapist(): void
    {
        // Temporarily disabled for demo purposes
        // if (
        //     !isset($_SESSION['user_id']) ||
        //     !isset($_SESSION['role'])    ||
        //     $_SESSION['role'] !== 'Therapist'
        // ) {
        //     $this->abort(403, 'Access denied.');
        // }
    }

    protected function redirect($url)
    {
        header("Location: {$url}");
        exit;
    }

    protected function abort($code, $message = '')
    {
        http_response_code($code);
        echo htmlspecialchars($message);
        exit;
    }
}