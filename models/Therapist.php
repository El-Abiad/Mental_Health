<?php
require_once __DIR__ . '/../core/BaseController.php';

class Therapist extends BaseController {
    public function __construct() {
        parent::__construct();
    }

    public function getProfile(int $therapistId) {
        $stmt = $this->db->prepare('SELECT t.*, u.FullName, u.Email FROM therapistprofile t JOIN Users u ON t.UserId = u.Id WHERE t.UserId = ?');
        $stmt->bind_param('i', $therapistId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getUpcomingAppointments(int $therapistId) {
        $stmt = $this->db->prepare('SELECT a.*, u.FullName as PatientName FROM appointment a JOIN patientprofile p ON a.PatientId = p.UserId JOIN Users u ON p.UserId = u.Id WHERE a.TherapistId = ? AND a.ScheduledAt >= NOW() ORDER BY a.ScheduledAt ASC');
        $stmt->bind_param('i', $therapistId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getTodaySessions(int $therapistId) {
        $stmt = $this->db->prepare('SELECT s.*, a.ScheduledAt, u.FullName as PatientName FROM session s JOIN appointment a ON s.AppointmentId = a.AppointmentId JOIN Users u ON a.PatientId = u.Id WHERE a.TherapistId = ? AND DATE(a.ScheduledAt) = CURDATE()');
        $stmt->bind_param('i', $therapistId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getMissedHighRiskPatients(int $therapistId) {
        $stmt = $this->db->prepare("SELECT DISTINCT p.*, u.FullName as PatientName FROM patientprofile p JOIN Users u ON p.UserId = u.Id JOIN appointment a ON a.PatientId = p.UserId WHERE a.TherapistId = ? AND a.Status = 'cancelled'");
        $stmt->bind_param('i', $therapistId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getWeeklyMoodReports(int $therapistId) {
        $stmt = $this->db->prepare('SELECT d.*, u.FullName as PatientName FROM dailylog d JOIN appointment a ON a.PatientId = d.PatientId JOIN Users u ON d.PatientId = u.Id WHERE a.TherapistId = ? AND d.LogDate >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) GROUP BY d.LogId');
        $stmt->bind_param('i', $therapistId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getAvailability(int $therapistId) {
        $stmt = $this->db->prepare('SELECT * FROM availability WHERE TherapistId = ?');
        $stmt->bind_param('i', $therapistId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function upsertAvailability(int $therapistId, int $day, string $start, string $end) {
        $stmt1 = $this->db->prepare('DELETE FROM availability WHERE TherapistId = ? AND DayOfWeek = ?');
        $stmt1->bind_param('ii', $therapistId, $day);
        $stmt1->execute();
        
        $stmt2 = $this->db->prepare('INSERT INTO availability (TherapistId, DayOfWeek, StartTime, EndTime) VALUES (?, ?, ?, ?)');
        $stmt2->bind_param('iiss', $therapistId, $day, $start, $end);
        return $stmt2->execute();
    }

    public function setSnooze(int $therapistId, int $snoozed) {
        $stmt = $this->db->prepare('UPDATE therapistprofile SET IsSnoozed = ? WHERE UserId = ?');
        $stmt->bind_param('ii', $snoozed, $therapistId);
        return $stmt->execute();
    }

    public function notifyPatientsTherapistSnoozed(int $therapistId) {
        $stmt = $this->db->prepare('INSERT INTO notification (UserId, Message, Type, SentAt, IsRead) SELECT DISTINCT a.PatientId, "Your therapist is temporarily unavailable", "System", NOW(), 0 FROM appointment a WHERE a.TherapistId = ?');
        $stmt->bind_param('i', $therapistId);
        return $stmt->execute();
    }

    public function getSession(int $sessionId, int $therapistId) {
        $stmt = $this->db->prepare('SELECT s.*, a.PatientId, u.FullName as PatientName FROM session s JOIN appointment a ON s.AppointmentId = a.AppointmentId JOIN Users u ON a.PatientId = u.Id WHERE s.SessionId = ? AND a.TherapistId = ?');
        $stmt->bind_param('ii', $sessionId, $therapistId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getPatientBySession(int $sessionId) {
        $stmt = $this->db->prepare('SELECT p.*, u.FullName FROM patientprofile p JOIN Users u ON p.UserId = u.Id JOIN appointment a ON a.PatientId = p.UserId JOIN session s ON s.AppointmentId = a.AppointmentId WHERE s.SessionId = ?');
        $stmt->bind_param('i', $sessionId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function hasLiveSession(int $therapistId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as cnt FROM session s JOIN appointment a ON s.AppointmentId = a.AppointmentId WHERE a.TherapistId = ? AND s.Status = 'live'");
        $stmt->bind_param('i', $therapistId);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        return ($res['cnt'] ?? 0) > 0;
    }

    public function startSession(int $sessionId, int $therapistId) {
        $stmt = $this->db->prepare("UPDATE session s JOIN appointment a ON s.AppointmentId = a.AppointmentId SET s.Status = 'live', s.StartedAt = NOW() WHERE s.SessionId = ? AND a.TherapistId = ?");
        $stmt->bind_param('ii', $sessionId, $therapistId);
        return $stmt->execute();
    }

    public function endSession(int $sessionId, int $therapistId) {
        $stmt = $this->db->prepare("UPDATE session s JOIN appointment a ON s.AppointmentId = a.AppointmentId SET s.Status = 'completed', s.EndedAt = NOW() WHERE s.SessionId = ? AND a.TherapistId = ?");
        $stmt->bind_param('ii', $sessionId, $therapistId);
        return $stmt->execute();
    }

    public function triggerCrisisAlert(int $sessionId, int $therapistId, string $keyword) {
        $patient = $this->getPatientBySession($sessionId);
        if ($patient) {
            $stmt = $this->db->prepare("INSERT INTO crisisalert (PatientId, Severity, TriggeredAt, Status) VALUES (?, 'high', NOW(), 'active')");
            $stmt->bind_param('i', $patient['UserId']);
            return $stmt->execute();
        }
        return false;
    }

    public function updateProfile(int $therapistId, array $data) {
        $spec = $data['Specialization'] ?? '';
        $licStatus = $data['LicenseStatus'] ?? '';
        $licExp = $data['LicenseExpiry'] ?? '';
        $snoozed = $data['IsSnoozed'] ?? 0;
        
        $stmt = $this->db->prepare('UPDATE therapistprofile SET Specialization = ?, LicenseStatus = ?, LicenseExpiry = ?, IsSnoozed = ? WHERE UserId = ?');
        $stmt->bind_param('sssii', $spec, $licStatus, $licExp, $snoozed, $therapistId);
        return $stmt->execute();
    }

    public function cancelAppointment(int $appointmentId, int $therapistId, string $reason) {
        $stmt = $this->db->prepare("UPDATE appointment SET Status = 'cancelled', CancelReason = ? WHERE AppointmentId = ? AND TherapistId = ?");
        $stmt->bind_param('sii', $reason, $appointmentId, $therapistId);
        return $stmt->execute();
    }

    public function applyLateCancellationFine(int $appointmentId) {
        // Future feature, do nothing for now
        return true;
    }

    public function getPatients(int $therapistId) {
        $stmt = $this->db->prepare('SELECT DISTINCT p.*, u.FullName as PatientName FROM patientprofile p JOIN Users u ON p.UserId = u.Id JOIN appointment a ON a.PatientId = p.UserId WHERE a.TherapistId = ?');
        $stmt->bind_param('i', $therapistId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getSharedJournals(int $therapistId) {
        $stmt = $this->db->prepare('SELECT j.*, u.FullName as PatientName FROM journal j JOIN appointment a ON a.PatientId = j.PatientId JOIN Users u ON j.PatientId = u.Id WHERE a.TherapistId = ? AND j.IsPrivate = 0 GROUP BY j.JournalId');
        $stmt->bind_param('i', $therapistId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getPatientIfAssigned(int $therapistId, int $patientId) {
        $stmt = $this->db->prepare('SELECT p.*, u.FullName FROM patientprofile p JOIN Users u ON p.UserId = u.Id JOIN appointment a ON a.PatientId = p.UserId WHERE a.TherapistId = ? AND p.UserId = ? LIMIT 1');
        $stmt->bind_param('ii', $therapistId, $patientId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getWeeklyMoodLogs(int $patientId) {
        $stmt = $this->db->prepare('SELECT * FROM dailylog WHERE PatientId = ? AND LogDate >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) ORDER BY LogDate ASC');
        $stmt->bind_param('i', $patientId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getSleepReport(int $patientId) {
        $stmt = $this->db->prepare('SELECT LogDate, SleepHours FROM dailylog WHERE PatientId = ? ORDER BY LogDate ASC');
        $stmt->bind_param('i', $patientId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}