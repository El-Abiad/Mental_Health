<?php
require_once 'config/db.php';
require_once 'controllers/TherapistController.php';
require_once 'controllers/NoteController.php';

$therapistController = new TherapistController();
$noteController = new NoteController();

// Simulate therapist actions
echo "--- Therapist Run ---\n";

// Manage cycle (Clinic manager function, but therapist can view)
echo "Managing session cycle...\n";

// Create a note
$note = $noteController->createNote(1, 1, "Patient showed improvement today.");
echo "Note created: " . $note['content'] . " at " . $note['timestamp'] . "\n";

// Get therapist notes
$notes = $noteController->getTherapistNotes(1);
echo "Therapist has " . count($notes) . " notes.\n";

echo "Therapist run completed.\n";