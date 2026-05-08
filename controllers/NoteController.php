<?php
require_once __DIR__ . '/../core/BaseController.php';
require_once __DIR__ . '/../models/Note.php';

class NoteController extends BaseController {
    private Note $noteModel;

    public function __construct() {
        parent::__construct();
        $this->noteModel = new Note();
    }

    public function createNote(int $patientId, int $therapistId, string $content, ?int $sessionId = null): array {
        return $this->noteModel->create($patientId, $therapistId, $content, $sessionId);
    }

    public function getPatientNotes(int $patientId): array {
        return $this->noteModel->getPatientNotes($patientId);
    }

    public function getTherapistNotes(int $therapistId): array {
        return $this->noteModel->getTherapistNotes($therapistId);
    }

    public function getNote(int $noteId): ?array {
        return $this->noteModel->getNote($noteId);
    }
}