<?php
require_once __DIR__ . '/../core/BaseController.php';

class Therapist extends BaseController {
    public function __construct() {
        parent::__construct();
    }

    // Fetch therapist profile
    public function getProfile(int $therapistId) {
        $stmt = $this->db->prepare('SELECT * FROM therapists WHERE id = ?');
        $stmt->execute([$therapistId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get upcoming appointments
    public function getUpcomingAppointments(int $therapistId) {
        $stmt = $this->db->prepare('SELECT * FROM appointments WHERE therapist_id = ? AND date >= NOW() ORDER BY date ASC');
        $stmt->execute([$therapistId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get today's sessions
    public function getTodaySessions(int $therapistId) {
        $stmt = $this->db->prepare('SELECT * FROM sessions WHERE therapist_id = ? AND DATE(date) = CURDATE()');
        $stmt->execute([$therapistId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get missed high-risk patients
    public function getMissedHighRiskPatients(int $therapistId) {
        $stmt = $this->db->prepare('SELECT * FROM patients WHERE therapist_id = ? AND risk_level = "high" AND last_session IS NOT NULL');
        $stmt->execute([$therapistId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get weekly mood reports
    public function getWeeklyMoodReports(int $therapistId) {
        $stmt = $this->db->prepare('SELECT * FROM mood_reports WHERE therapist_id = ? AND DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)');
        $stmt->execute([$therapistId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get availability
    public function getAvailability(int $therapistId) {
        $stmt = $this->db->prepare('SELECT * FROM availability WHERE therapist_id = ?');
        $stmt->execute([$therapistId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Upsert availability
    public function upsertAvailability(int $therapistId, int $day, string $start, string $end) {
        $stmt = $this->db->prepare('
            INSERT INTO availability (therapist_id, day, start_time, end_time) 
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE start_time = ?, end_time = ?
        ');
        return $stmt->execute([$therapistId, $day, $start, $end, $start, $end]);
    }

    // Set snooze status
    public function setSnooze(int $therapistId, int $snoozed) {
        $stmt = $this->db->prepare('UPDATE therapists SET is_snoozed = ? WHERE id = ?');
        return $stmt->execute([$snoozed, $therapistId]);
    }

    // Notify patients therapist is snoozed
    public function notifyPatientsTherapistSnoozed(int $therapistId) {
        $stmt = $this->db->prepare('
            INSERT INTO notifications (patient_id, message, created_at) 
            SELECT id, "Your therapist is temporarily unavailable", NOW() FROM patients WHERE therapist_id = ?
        ');
        return $stmt->execute([$therapistId]);
    }

    // Get session
    public function getSession(int $sessionId, int $therapistId) {
        $stmt = $this->db->prepare('SELECT * FROM sessions WHERE id = ? AND therapist_id = ?');
        $stmt->execute([$sessionId, $therapistId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}