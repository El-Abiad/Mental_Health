<?php
require_once __DIR__ . '/../core/BaseController.php';

class Patient extends BaseController {
    public function __construct() {
        parent::__construct();
    }

    public function getPatientByUserId(int $userId): ?array {
        $stmt = $this->db->prepare('SELECT p.*, p.UserId as id, u.FullName, u.Email FROM patientprofile p JOIN Users u ON p.UserId = u.Id WHERE p.UserId = ?');
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result ?: null;
    }

    public function completeIntakeForm(int $patientId): bool {
        $stmt = $this->db->prepare('UPDATE patientprofile SET intake_form_completed = 1 WHERE UserId = ?');
        $stmt->bind_param('i', $patientId);
        return $stmt->execute();
    }

    public function signAgreement(int $patientId): bool {
        $stmt = $this->db->prepare('UPDATE patientprofile SET agreement_signed = 1 WHERE UserId = ?');
        $stmt->bind_param('i', $patientId);
        return $stmt->execute();
    }

    public function addFavorite(int $patientId, int $therapistId): bool {
        $stmt = $this->db->prepare('INSERT IGNORE INTO patient_favorites (patient_id, therapist_id) VALUES (?, ?)');
        $stmt->bind_param('ii', $patientId, $therapistId);
        return $stmt->execute();
    }

    public function getFavorites(int $patientId): array {
        $stmt = $this->db->prepare('SELECT t.*, u.FullName as TherapistName FROM therapistprofile t JOIN Users u ON t.UserId = u.Id JOIN patient_favorites pf ON t.UserId = pf.therapist_id WHERE pf.patient_id = ?');
        $stmt->bind_param('i', $patientId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getUpcomingSessions(int $patientId): array {
        $stmt = $this->db->prepare('SELECT s.*, a.ScheduledAt, u.FullName as TherapistName FROM session s JOIN appointment a ON s.AppointmentId = a.AppointmentId JOIN Users u ON a.TherapistId = u.Id WHERE a.PatientId = ? AND a.ScheduledAt >= NOW() ORDER BY a.ScheduledAt ASC');
        $stmt->bind_param('i', $patientId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getMoodLogs(int $patientId): array {
        $stmt = $this->db->prepare('SELECT * FROM dailylog WHERE PatientId = ? ORDER BY LogDate DESC');
        $stmt->bind_param('i', $patientId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function addMoodLog(int $patientId, string $mood, string $notes = ''): bool {
        $moodScore = (int)$mood; // Convert mood to int
        $stmt = $this->db->prepare('INSERT INTO dailylog (PatientId, MoodScore, Notes, LogDate) VALUES (?, ?, ?, NOW())');
        $stmt->bind_param('iis', $patientId, $moodScore, $notes);
        return $stmt->execute();
    }
}