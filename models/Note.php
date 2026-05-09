<?php
require_once __DIR__ . '/../core/BaseController.php';

class Note extends BaseController {
    public function __construct() {
        parent::__construct();
    }

    // Create a new note
    public function create(int $patientId, int $therapistId, string $content, ?int $sessionId = null): array {
        $stmt = $this->db->prepare('INSERT INTO clinicalnote (PatientId, TherapistId, SessionId, Content) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('iiis', $patientId, $therapistId, $sessionId, $content);
        $stmt->execute();
        $noteId = $this->db->insert_id;

        return [
            'id' => $noteId,
            'patient_id' => $patientId,
            'therapist_id' => $therapistId,
            'session_id' => $sessionId,
            'content' => $content,
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }

    // Get notes for a patient
    public function getPatientNotes(int $patientId): array {
        $stmt = $this->db->prepare('SELECT * FROM clinicalnote WHERE PatientId = ? ORDER BY CreatedAt DESC');
        $stmt->bind_param('i', $patientId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Get notes by therapist
    public function getTherapistNotes(int $therapistId): array {
        $stmt = $this->db->prepare('SELECT * FROM clinicalnote WHERE TherapistId = ? ORDER BY CreatedAt DESC');
        $stmt->bind_param('i', $therapistId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Get notes by therapist
    public function getByTherapist(int $therapistId): array {
        return $this->getTherapistNotes($therapistId);
    }

    // Get notes by session
    public function getBySession(int $sessionId): array {
        $stmt = $this->db->prepare('SELECT * FROM clinicalnote WHERE SessionId = ? ORDER BY CreatedAt DESC');
        $stmt->bind_param('i', $sessionId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Update note content
    public function update(int $noteId, int $therapistId, string $content): bool {
        $stmt = $this->db->prepare('UPDATE clinicalnote SET Content = ?, Version = Version + 1 WHERE NoteId = ? AND TherapistId = ?');
        $stmt->bind_param('sii', $content, $noteId, $therapistId);
        return $stmt->execute();
    }
}