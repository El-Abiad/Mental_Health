<?php
require_once 'config/db.php';
require_once 'controllers/TherapistController.php';
require_once 'controllers/NoteController.php';

$t_ctrl = new TherapistController();
$n_ctrl = new NoteController();

echo "--- Therapist Module ---\n";
echo $t_ctrl->manageCycle(101, 'Live') . "\n";
$note = $n_ctrl->createNote(1, 1, "Patient is stable.");
echo "Note created at: " . $note['timestamp'];