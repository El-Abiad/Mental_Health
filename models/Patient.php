<?php

require_once __DIR__ . '/../core/BaseController.php';

class Patient extends BaseController
{
    public function getPatientByUserId(int $userId): ?array
    {
        $stmt = $this->db->prepare('
            SELECT pp.UserId AS id, pp.UserId AS user_id, pp.Age, pp.MedicalHistory,
                   pp.IsAnonymous, pp.Status, u.FullName, u.Email, u.Phone
            FROM PatientProfile pp
            JOIN Users u ON pp.UserId = u.Id
            WHERE pp.UserId = ?
        ');
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row ?: null;
    }

    public function completeIntakeForm(int $patientId, string $medicalHistory = ''): bool
    {
        $stmt = $this->db->prepare('
            UPDATE PatientProfile
            SET MedicalHistory = ?, Status = "active"
            WHERE UserId = ?
        ');
        $stmt->bind_param('si', $medicalHistory, $patientId);
        return $stmt->execute();
    }

    public function signAgreement(int $patientId, string $signature = ''): bool
    {
        $content = 'Signed electronically' . ($signature !== '' ? ' by ' . $signature : '');
        $stmt = $this->db->prepare('
            INSERT INTO Agreement (PatientId, Content)
            VALUES (?, ?)
        ');
        $stmt->bind_param('is', $patientId, $content);
        return $stmt->execute();
    }

    public function addFavorite(int $patientId, int $therapistId): bool
    {
        $message = 'Patient requested therapist #' . $therapistId . ' as a favorite/preference.';
        $stmt = $this->db->prepare('INSERT INTO Notification (UserId, Message, Type) VALUES (?, ?, "favorite")');
        $stmt->bind_param('is', $patientId, $message);
        return $stmt->execute();
    }

    public function getFavorites(int $patientId): array
    {
        $result = $this->db->query('
            SELECT u.Id AS id, u.FullName AS name, tp.Specialization AS specialties,
                   (tp.LicenseStatus = "active") AS license_verified, tp.IsSnoozed AS is_snoozed
            FROM Users u
            JOIN TherapistProfile tp ON u.Id = tp.UserId
            JOIN UserRoles ur ON u.Id = ur.UserId
            JOIN Roles r ON ur.RoleId = r.RoleId
            WHERE r.RoleName = "therapist" AND u.IsActive = 1
            ORDER BY u.FullName
        ');
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getUpcomingSessions(int $patientId): array
    {
        $stmt = $this->db->prepare('
            SELECT s.SessionId AS id, t.FullName AS therapist_name,
                   a.ScheduledAt AS date, COALESCE(s.Status, a.Status) AS status,
                   "" AS notes
            FROM Appointment a
            LEFT JOIN Session s ON a.AppointmentId = s.AppointmentId
            LEFT JOIN Users t ON a.TherapistId = t.Id
            WHERE a.PatientId = ? AND a.ScheduledAt >= NOW()
            ORDER BY a.ScheduledAt ASC
        ');
        $stmt->bind_param('i', $patientId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getMoodLogs(int $patientId): array
    {
        $stmt = $this->db->prepare('
            SELECT LogId AS id, MoodScore AS mood, Notes AS notes, LogDate AS date, SleepHours AS sleep_hours
            FROM DailyLog
            WHERE PatientId = ?
            ORDER BY LogDate DESC
        ');
        $stmt->bind_param('i', $patientId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function addMoodLog(int $patientId, string $mood, string $notes = ''): bool
    {
        $score = $this->moodToScore($mood);
        $stmt = $this->db->prepare('
            INSERT INTO DailyLog (PatientId, MoodScore, Notes)
            VALUES (?, ?, ?)
        ');
        $stmt->bind_param('iis', $patientId, $score, $notes);
        return $stmt->execute();
    }

    public function triggerCrisis(int $patientId, string $severity = 'high'): bool
    {
        $stmt = $this->db->prepare('INSERT INTO CrisisAlert (PatientId, Severity, Status) VALUES (?, ?, "open")');
        $stmt->bind_param('is', $patientId, $severity);
        return $stmt->execute();
    }

    private function moodToScore(string $mood): int
    {
        return match (strtolower($mood)) {
            'happy' => 8,
            'calm' => 7,
            'anxious' => 4,
            'sad' => 3,
            default => max(1, min(10, (int)$mood)),
        };
    }
}
