<?php
require_once 'config/db.php';
require_once 'controllers/NoteController.php';

$noteController = new NoteController();

echo "--- Note Module ---\n";

// Create a note
$note = $noteController->createNote(1, 1, "Patient reported feeling anxious.");
echo "Note created: " . $note['content'] . " at " . $note['timestamp'] . "\n";

// Get patient notes
$patientNotes = $noteController->getPatientNotes(1);
echo "Patient has " . count($patientNotes) . " notes.\n";

// Get therapist notes
$therapistNotes = $noteController->getTherapistNotes(1);
echo "Therapist has " . count($therapistNotes) . " notes.\n";

echo "Note run completed.\n";