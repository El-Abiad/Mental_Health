<?php
require_once __DIR__ . '/../core/BaseController.php';

class Patient extends BaseController {
    public function __construct() {
        parent::__construct();
    }

    // Get patient by user id
    public function getPatientByUserId(int $userId): ?array {
        $stmt = $this->db->prepare('SELECT * FROM patients WHERE user_id = ?');
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    // Update intake form status
    public function completeIntakeForm(int $patientId): bool {
        $stmt = $this->db->prepare('UPDATE patients SET intake_form_completed = 1 WHERE id = ?');
        return $stmt->execute([$patientId]);
    }

    // Sign agreement
    public function signAgreement(int $patientId): bool {
        $stmt = $this->db->prepare('UPDATE patients SET agreement_signed = 1 WHERE id = ?');
        return $stmt->execute([$patientId]);
    }

    // Add therapist to favorites
    public function addFavorite(int $patientId, int $therapistId): bool {
        $stmt = $this->db->prepare('INSERT INTO patient_favorites (patient_id, therapist_id) VALUES (?, ?) ON DUPLICATE KEY UPDATE therapist_id = therapist_id');
        return $stmt->execute([$patientId, $therapistId]);
    }

    // Get favorites
    public function getFavorites(int $patientId): array {
        $stmt = $this->db->prepare('SELECT t.* FROM therapists t JOIN patient_favorites pf ON t.id = pf.therapist_id WHERE pf.patient_id = ?');
        $stmt->execute([$patientId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get upcoming sessions
    public function getUpcomingSessions(int $patientId): array {
        $stmt = $this->db->prepare('SELECT * FROM sessions WHERE patient_id = ? AND date >= NOW() ORDER BY date ASC');
        $stmt->execute([$patientId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get mood logs
    public function getMoodLogs(int $patientId): array {
        $stmt = $this->db->prepare('SELECT * FROM mood_logs WHERE patient_id = ? ORDER BY date DESC');
        $stmt->execute([$patientId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Add mood log
    public function addMoodLog(int $patientId, string $mood, string $notes = ''): bool {
        $stmt = $this->db->prepare('INSERT INTO mood_logs (patient_id, mood, notes, date) VALUES (?, ?, ?, CURDATE())');
        return $stmt->execute([$patientId, $mood, $notes]);
    }
}