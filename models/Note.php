<?php
require_once __DIR__ . '/../core/BaseController.php';

class Note extends BaseController {
    public function __construct() {
        parent::__construct();
    }

    // Create a new note
    public function create(int $patientId, int $therapistId, string $content, ?int $sessionId = null): array {
        $stmt = $this->db->prepare('INSERT INTO notes (patient_id, therapist_id, session_id, content, timestamp) VALUES (?, ?, ?, ?, NOW())');
        $stmt->execute([$patientId, $therapistId, $sessionId, $content]);
        $noteId = $this->db->lastInsertId();

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
        $stmt = $this->db->prepare('SELECT * FROM notes WHERE patient_id = ? ORDER BY timestamp DESC');
        $stmt->execute([$patientId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get notes by therapist
    public function getTherapistNotes(int $therapistId): array {
        $stmt = $this->db->prepare('SELECT * FROM notes WHERE therapist_id = ? ORDER BY timestamp DESC');
        $stmt->execute([$therapistId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get notes by therapist
    public function getByTherapist(int $therapistId): array {
        $stmt = $this->db->prepare('SELECT * FROM notes WHERE therapist_id = ? ORDER BY timestamp DESC');
        $stmt->execute([$therapistId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get notes by session (assuming session_id column exists)
    public function getBySession(int $sessionId): array {
        $stmt = $this->db->prepare('SELECT * FROM notes WHERE session_id = ? ORDER BY timestamp DESC');
        $stmt->execute([$sessionId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update note content
    public function update(int $noteId, int $therapistId, string $content): bool {
        $stmt = $this->db->prepare('UPDATE notes SET content = ? WHERE id = ? AND therapist_id = ?');
        return $stmt->execute([$content, $noteId, $therapistId]);
    }
}